<?php

namespace forms;

require_once 'FileHandler.php';

class PersonForm extends FileHandler
{
    public function __construct(array $postData, array $postFiles, ?array $validExtensions = null)
    {
        parent::__construct($postData, $postFiles, $validExtensions);
    }

    public function validate(): void
    {
        $this->validateField('nom', fn($name) => !empty($name), "Le nom est obligatoire");
        $this->validateField('prenom', fn($firstname) => !empty($firstname), "Le prénom est obligatoire");
        $this->validateField('email', fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL), "Email invalide");
        $this->validateField('email', fn($email) => !empty($email), "L'email est obligatoire");
    }
}
