<?php

namespace input;

use forms\FormHandler;

require_once 'Input.php';

class TextareaInput extends Input
{
    private string $placeholder;
    private string $class;

    public function __construct(string $id, string $name, ?FormHandler $form, string $placeholder = "", bool $required = false, string $class = "textarea-form")
    {
        $this->placeholder = $placeholder;
        $this->class = $class;
        parent::__construct($id, $name, $form, $required);
    }

    public function create($value = ""): void
    {
        echo <<<HTML
        <label class="lab-col" for="$this->id">
        {$this->getRequiredName()}
        <textarea name="$this->id" id="$this->id" class="$this->class outline-accent" placeholder="$this->placeholder">$value</textarea>
        </label>
        HTML;
    }
}