<?php

namespace input;

use forms\FormHandler;

require_once 'Input.php';

class OptionInput extends Input
{
    private array $options;

    public function __construct(string $id, string $name, array $options, ?FormHandler $form, bool $required = false)
    {
        $this->options = $options;
        parent::__construct($id, $name, $form, $required);
    }

    public function create($value = ""): void
    {
        $optionsHtml = implode(
            "",
            array_map(
                fn($key, $option) => "<option value=\"$key\"" . ($key == $value ? " selected" : "") . ">$option</option>",
                array_keys($this->options),
                $this->options));

        echo <<<HTML
        <label class="lab-col" for="$this->id">
            {$this->getRequiredName()}
            <select name="$this->id" id="$this->id" class="outline-accent">
                $optionsHtml
            </select>
        </label>
        HTML;
    }
}

