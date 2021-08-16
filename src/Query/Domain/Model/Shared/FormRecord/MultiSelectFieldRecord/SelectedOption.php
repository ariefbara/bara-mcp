<?php

namespace Query\Domain\Model\Shared\FormRecord\MultiSelectFieldRecord;

use Query\Domain\Model\Shared\ {
    Form\SelectField\Option,
    FormRecord\MultiSelectFieldRecord
};

class SelectedOption
{

    /**
     *
     * @var MultiSelectFieldRecord
     */
    protected $multiSelectFieldRecord;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Option
     */
    protected $option;

    /**
     *
     * @var bool
     */
    protected $removed;

    function getMultiSelectFieldRecord(): MultiSelectFieldRecord
    {
        return $this->multiSelectFieldRecord;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getOption(): Option
    {
        return $this->option;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
    }
    
    public function getOptionName(): ?string
    {
        return $this->option->getName();
    }

}
