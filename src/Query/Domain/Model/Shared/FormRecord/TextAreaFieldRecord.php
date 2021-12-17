<?php

namespace Query\Domain\Model\Shared\FormRecord;

use Query\Domain\Model\Shared\Form\TextAreaField;
use Query\Domain\Model\Shared\FormRecord;
use Query\Domain\Model\Shared\IFieldRecord;

class TextAreaFieldRecord implements IFieldRecord
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
    }
    
    public function isActiveFieldRecordCorrespondWith(TextAreaField $textAreaField): bool
    {
        return !$this->removed && $this->textAreaField === $textAreaField;
    }

    public function correspondWithFieldName(string $fieldName): bool
    {
        return $this->textAreaField->getName() === $fieldName;
    }

}
