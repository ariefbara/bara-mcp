<?php

namespace Shared\Domain\Model\FormRecord;

use Shared\Domain\Model\{
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

    public function __construct(FormRecord $formRecord, string $id, TextAreaField $textAreaField, ?string $value)
    {
        $this->formRecord = $formRecord;
        $this->textAreaField = $textAreaField;
        $this->id = $id;
        $this->value = $value;
        $this->removed = false;
    }

    public function update(?string $value): void
    {
        $this->value = $value;
    }

    public function isReferToRemovedField(): bool
    {
        return $this->textAreaField->isRemoved();
    }

    public function remove(): void
    {
        $this->removed = true;
    }

}
