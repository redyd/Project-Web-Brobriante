<?php

namespace input;

require_once 'Input.php';

class CheckboxInput extends Input
{

    public function create($value = 1): void
    {
        $check = $value == 1 ? "checked" : "";

        echo <<<HTML
        <label class="lab-row" for="$this->id">
            {$this->getRequiredName()}
            <input class="outline-accent" type="checkbox" id="$this->id" name="$this->id" value="1" $check>
        </label>
        HTML;
    }
}