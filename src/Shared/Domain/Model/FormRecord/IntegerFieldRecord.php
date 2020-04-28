<?php

namespace Shared\Domain\Model\FormRecord;

use Shared\Domain\Model\{
    Form\IntegerField,
    FormRecord
};

class IntegerFieldRecord
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
     * @var IntegerField
     */
    protected $integerField;

    /**
     *
     * @var int
     */
    protected $value = null;

    /**
     *
     * @var bool
     */
    protected $removed;

    function getIntegerField(): IntegerField
    {
        return $this->integerField;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getValue(): ?int
    {
        return $this->value;
    }

    public function isRemoved(): bool
    {
        return $this->removed;
    }

    public function __construct(FormRecord $formRecord, string $id, IntegerField $integerField, ?int $value)
    {
        $this->formRecord = $formRecord;
        $this->id = $id;
        $this->integerField = $integerField;
        $this->value = $value;
        $this->removed = false;
    }

    public function update(?int $value): void
    {
        $this->value = $value;
    }

    public function isReferToRemovedField(): bool
    {
        return $this->integerField->isRemoved();
    }

    public function remove(): void
    {
        $this->removed = true;
    }

}
