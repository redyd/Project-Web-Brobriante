<?php

namespace input;

use forms\FormHandler;

require_once 'Input.php';

class FileInput extends Input
{
    private string $accept;
    private string $type;
    private string $class;

    public function __construct(string $id, string $name, ?FormHandler $form, bool $required = false, string $accept = "image/*", string $type = "file", string $class = "lab-col")
    {
        $this->accept = $accept;
        $this->type = $type;
        $this->class = $class;
        parent::__construct($id, $name, $form, $required);
    }

    public function create($value = ""): void
    {
        echo <<<HTML
        <label class="$this->class" for="$this->id">
            {$this->getRequiredName()}
            <input class="outline-accent" type="$this->type" name="$this->id" id="$this->id" accept="$this->accept">
        </label>
        HTML;
    }
}