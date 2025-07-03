<?php

namespace forms;

require_once 'FormHandler.php';

class LoginForm extends FormHandler
{
    public function validate(): void
    {
        $this->validateField('email', fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL), "Email invalide");
        $this->validateField('mot_passe', fn($password) => !empty($password), "Mot de passe invalide");
    }
}