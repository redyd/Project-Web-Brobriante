<?php

namespace forms;

require_once 'FormHandler.php';

class EmplacementForm extends FormHandler
{
    public array $emplacements;

    public function __construct(array $postData, array $emplacements)
    {
        $this->emplacements = $emplacements;
        parent::__construct($postData);
    }

    public function validate(): void
    {
        $this->validateField("emplacement", fn($emplacement) => in_array($emplacement, $this->emplacements), "L'emplacement doit être valide");
    }
}