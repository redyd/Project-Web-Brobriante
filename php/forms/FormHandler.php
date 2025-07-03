<?php

namespace forms;
abstract class FormHandler
{

    protected array $data;
    protected array $errors = [];

    public function __construct(array $postData)
    {
        $this->data = $this->sanitize($postData);
    }

    public function validateField(string $fieldName, callable $validationFunction, string $error): void
    {
        if (!isset($this->data[$fieldName]) || !$validationFunction($this->data[$fieldName])) {
            $this->errors[$fieldName] = $error;
        }
    }

    public function isValid(): bool
    {
        return empty($this->getErrors());
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getError($name): string
    {
        return $this->errors[$name] ?? "";
    }

    public function addError(string $name, string $error): void
    {
        $this->errors[$name] = $error;
    }

    public function getData($name): string
    {
        return $this->data[$name] ?? "";
    }

    public function getAllData(): array
    {
        $data = $this->data;
        unset($data['mot_passe_confirm'], $data['form_type']);
        return $data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;

    }

    private function sanitize(array $data): array
    {
        return array_map(fn($value) => htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8'), $data);
    }

    abstract public function validate(): void;
}
