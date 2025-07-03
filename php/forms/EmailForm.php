<?php

namespace forms;

require_once 'FormHandler.php';

class EmailForm extends FormHandler
{

    public function validate(): void
    {
        $this->validateField('email', fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL), "Email invalide");
        $this->validateField('email', fn($email) => !empty($email), "L'email est obligatoire");
    }
}
