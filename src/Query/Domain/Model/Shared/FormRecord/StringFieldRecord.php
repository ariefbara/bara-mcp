<?php

namespace Query\Domain\Model\Shared\FormRecord;

use Query\Domain\Model\Shared\ {
    Form\StringField,
    FormRecord
};

class StringFieldRecord
{

    /**
     *
     * @var FormRecord
     */
    protected $formRecord;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var StringField
     */
    protected $stringField;

    /**
     *
     * @var string
     */
    protected $value = null;

    /**
     *
     * @var bool
     */
    protected $removed = false;

    function getFormRecord(): FormRecord
    {
        return $this->formRecord;
    }

    function getStringField(): StringField
    {
        return $this->stringField;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getValue(): ?string
    {
        return $this->value;
    }

    public function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
        ;
    }

}
