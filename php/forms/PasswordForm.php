<?php

namespace forms;

class PasswordForm extends FormHandler
{

    public function validate(): void
    {
        $this->validateField('mot_passe', fn($password) => !empty($password), "Mot de passe invalide");
        $this->validateField('mot_passe', fn($pwd) => strlen($pwd) >= 8, "Le mot de passe doit contenir minimum 8 caractères");
        $this->validateField('mot_passe', fn($pwd) => !preg_match('/\s/', $pwd), "Le mot de passe ne peut pas contenir d'espaces");
        $this->validateField('mot_passe_confirm', fn($pwd) => $pwd === $this->getData('mot_passe'), "Les mots de passe sont différents");
    }

}