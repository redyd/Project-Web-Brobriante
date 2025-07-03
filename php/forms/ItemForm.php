<?php

namespace forms;

require_once 'FileHandler.php';

class ItemForm extends FileHandler
{
    private array $options;

    public function __construct(array $postData, array $options, array $postFiles, ?array $validExtensions = null)
    {
        $this->options = $options;
        parent::__construct($postData, $postFiles, $validExtensions);
    }

    public function validate(): void
    {
        $this->validateField("intitule", fn($name) => !empty($name), "Le nom est obligatoire");
        $this->validateField("categorie", fn($category) => !empty($category), "La catégorie est obligatoire");
        $this->validateField("categorie", fn($category) => in_array($category, array_keys($this->options)), "La catégorie sélectionnée doit être valide");
        $this->validateField("description", fn($description) => !empty($description), "La description est obligatoire");
    }
}
