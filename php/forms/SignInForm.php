<?php

namespace forms;

require_once 'FileHandler.php';

class SignInForm extends FileHandler
{
    public function __construct(array $postData, array $files, ?array $extensions = null)
    {
        parent::__construct($postData, $files, $extensions);
    }

    public function validate(): void
    {
        $this->validateField('email', fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL), "Email invalide");
        $this->validateField('nom', fn($name) => !empty($name), "Le nom est obligatoire");
        $this->validateField('prenom', fn($fname) => !empty($fname), "Le prénom est obligatoire");
    }
}