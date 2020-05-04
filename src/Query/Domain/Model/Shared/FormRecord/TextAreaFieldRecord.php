<?php

namespace Query\Domain\Model\Shared\FormRecord;

use Query\Domain\Model\Shared\ {
    Form\TextAreaField,
    FormRecord
};

class TextAreaFieldRecord
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
     * @var TextAreaField
     */
    protected $textAreaField;

    /**
     *
     * @var string
     */
    protected $value = null;

    /**
     *
     * @var bool
     */
    protected $removed;

    function getFormRecord(): FormRecord
    {
        return $this->formRecord;
    }

    function getTextAreaField(): TextAreaField
    {
        return $this->textAreaField;
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
