<?php

namespace SharedContext\Domain\Model\SharedEntity\FormRecord;

use SharedContext\Domain\Model\SharedEntity\ {
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

    function getStringField(): StringField
    {
        return $this->stringField;
    }

    public function isRemoved(): bool
    {
        return $this->removed;
    }

    public function __construct(FormRecord $formRecord, string $id, StringField $stringField, ?string $value)
    {
        $this->formRecord = $formRecord;
        $this->id = $id;
        $this->stringField = $stringField;
        $this->value = $value;
        $this->removed = false;
    }

    public function update(?string $value): void
    {
        $this->value = $value;
    }

    public function isReferToRemovedField(): bool
    {
        return $this->stringField->isRemoved();
    }

    public function remove(): void
    {
        $this->removed = true;
    }

}
