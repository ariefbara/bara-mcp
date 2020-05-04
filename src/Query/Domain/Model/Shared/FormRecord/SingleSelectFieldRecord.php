<?php

namespace Query\Domain\Model\Shared\FormRecord;

use Query\Domain\Model\Shared\ {
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
        ;
    }

}
