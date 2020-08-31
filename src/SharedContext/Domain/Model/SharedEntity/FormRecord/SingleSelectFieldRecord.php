<?php

namespace SharedContext\Domain\Model\SharedEntity\FormRecord;

use SharedContext\Domain\Model\SharedEntity\ {
    Form\SelectField\Option,
    Form\SingleSelectField,
    FormRecord
};

class SingleSelectFieldRecord
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

    function getSingleSelectField(): SingleSelectField
    {
        return $this->singleSelectField;
    }

    public function isRemoved(): bool
    {
        return $this->removed;
    }

    function __construct(FormRecord $formRecord, string $id, SingleSelectField $singleSelectField, ?Option $option)
    {
        $this->formRecord = $formRecord;
        $this->id = $id;
        $this->singleSelectField = $singleSelectField;
        $this->option = $option;
        $this->removed = false;
    }

    public function update(?Option $option): void
    {
        $this->option = $option;
    }

    public function isReferToRemovedField(): bool
    {
        return $this->singleSelectField->isRemoved();
    }

    public function remove(): void
    {
        $this->removed = true;
    }

}
