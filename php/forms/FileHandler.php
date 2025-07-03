<?php

namespace forms;

require_once __DIR__ . '/FormHandler.php';
require_once __DIR__ . '/../const.php';

/**
 * Cette classe permet de récupérer les informations et de stocker une image
 */
abstract class FileHandler extends FormHandler
{

    protected array $file;
    private string $destination;
    public array $validExtensions;

    public function __construct(array $postData, array $postFiles, ?array $validExtensions)
    {
        $validExtensions = $validExtensions ?? $this->getExtensions();
        $this->file = $this->infoOnFile(current($postFiles));
        $this->validExtensions = $validExtensions;
        parent::__construct($postData);
    }

    public function uploadFile(string $nameID = "image"): bool
    {
        if (!$this->validFile()) {
            return false;
        }

        if (!$this->checkExtension($this->file['extension'])) {
            $this->errors[$nameID] = "L'extension fournie est incorrecte";
            return false;
        }

        if ($this->getFileSize() > MAX_UPLOAD_SIZE) {
            $this->errors[$nameID] = "La taille du ficher dépasse la taille de " . MAX_UPLOAD_SIZE / (1024 * 1024) . "Mb (" . round($this->getFileSize() / (1024 * 1024), 2) . "Mb)";
            return false;
        }

        $destination = uniqid("img_") . "." . $this->file['extension'];
        $this->destination = $destination;

        return move_uploaded_file($this->file['tmp_name'], FILE_UPLOAD . $destination);
    }

    private function infoOnFile(array|bool $file): array
    {
        if ($file && !empty($file['name'])) {
            return [
                'name' => $file['name'],
                'tmp_name' => $file['tmp_name'],
                'type' => mime_content_type($file['tmp_name']),
                'extension' => strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)),
                'size' => $file['size'],
                'error' => $file['error']
            ];
        }
        return [];
    }

    public function getFileDestination(): string|null
    {
        return $this->destination ?? null;
    }

    public function getFileName(): string
    {
        return $this->file["name"];
    }

    public function getFileSize(): int
    {
        return $this->file["size"];
    }

    public function checkExtension(string $extension): bool
    {
        return in_array($extension, $this->validExtensions);
    }


    private function validFile(): bool
    {
        return (!empty($this->file)
            && $this->file['error'] === UPLOAD_ERR_OK);
    }

    public function getExtensions(): array
    {
        return AUTHORIZE_EXTENSIONS;
    }

    abstract function validate(): void;
}
