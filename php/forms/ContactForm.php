<?php

namespace forms;

require_once 'FormHandler.php';

class ContactForm extends FormHandler
{

    public function validate(): void
    {

        $this->validateField('email', fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL), "Email invalide");
        $this->validateField('email', fn($email) => !empty($email), "L'email est obligatoire");
        $this->validateField('subject', fn($subject) => !empty($subject), "L'objet du contact est obligatoire");
        $this->validateField('message', fn($message) => !empty($message), "Le message est obligatoire");
    }
}