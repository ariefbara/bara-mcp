<?php

namespace Query\Domain\Model\Shared\FormRecord;

use Query\Domain\Model\Shared\Form\SelectField\Option;
use Query\Domain\Model\Shared\Form\SingleSelectField;
use Query\Domain\Model\Shared\FormRecord;
use Query\Domain\Model\Shared\IFieldRecord;

class SingleSelectFieldRecord implements IFieldRecord
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
     * @var SingleSelectField
     */
    protected $singleSelectField;

    /**
     *
     * @var Option
     */
    protected $option = null;

    /**
     *
     * @var bool
     */
    protected $removed;

    function getFormRecord(): FormRecord
    {
        return $this->formRecord;
    }

    function getSingleSelectField(): SingleSelectField
    {
        return $this->singleSelectField;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getOption(): ?Option
    {
        return $this->option;
    }

    public function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
    }
    
    public function isActiveFieldRecordCorrespondWith(SingleSelectField $singleSelectField): bool
    {
        return !$this->removed && $this->singleSelectField === $singleSelectField;
    }
    
    public function getSelectedOptionName(): ?string
    {
        return isset($this->option) ? $this->option->getName() : null;
    }

    public function correspondWithFieldName(string $fieldName): bool
    {
        return $this->singleSelectField->getName() === $fieldName;
    }

    public function getValue()
    {
        return $this->getSelectedOptionName();
    }

}
