<?php

namespace Query\Domain\Model\Shared\FormRecord;

use Query\Domain\Model\Shared\Form\IntegerField;
use Query\Domain\Model\Shared\Form\StringField;
use Query\Domain\Model\Shared\FormRecord;

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

    function getFormRecord(): FormRecord
    {
        return $this->formRecord;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getIntegerField(): IntegerField
    {
        return $this->integerField;
    }

    function getValue(): ?int
    {
        return $this->value;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
    }
    
    public function isActiveFieldRecordCorrespondWith(IntegerField $integerField): bool
    {
        return !$this->removed && $this->integerField === $integerField;
    }

}
