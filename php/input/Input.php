<?php

namespace input;
use forms\FormHandler;

abstract class Input
{
    protected string $id;
    protected string $name;
    protected bool $required;

    public function __construct(string $id, string $name, ?FormHandler $form, bool $required = false)
    {
        $this->id = $id;
        $this->name = $name;
        $this->required = $required;

        if ($form !== null) {
            $this->create($form->getData($id));
            $this->echoError($form->getError($id));
        }

    }

    public function echoError($error): void
    {
        if (!empty($error)) {
            echo "<span class=\"error-txt\">$error</span>";
        }
    }

    protected function getRequiredName(): string
    {
        if (!empty($this->name)) {
            return $this->name . ($this->required ? '*' : '');
        }
        return '';
    }

    protected function getRequired(): string
    {
        return $this->required ? ' required' : '';
    }

    abstract public function create($value): void;
}
