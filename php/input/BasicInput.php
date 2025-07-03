<?php

namespace input;

use forms\FormHandler;

require_once 'Input.php';

class BasicInput extends Input
{
    private string $type;
    private string $placeholder;
    private string $classInput;

    public function __construct(string $id, string $name, ?FormHandler $form, string $type = "text", string $placeholder = "", bool $required = false, string $classInput = "")
    {
        $this->type = $type;
        $this->placeholder = $placeholder;
        $this->classInput = $classInput;
        parent::__construct($id, $name, $form, $required);
    }

    public function create($value = ""): void
    {
        echo <<<HTML
        <label class="lab-col" for="$this->id">
            {$this->getRequiredName()}
            <input class="$this->classInput outline-accent" type="$this->type" id="$this->id" name="$this->id" placeholder="$this->placeholder" value="$value" {$this->getRequired()}>
        </label>
        HTML;
    }
}