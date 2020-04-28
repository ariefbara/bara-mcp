<?php

namespace Shared\Domain\Model\FormRecord\MultiSelectFieldRecord;

use Shared\Domain\Model\{
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

    function __construct(MultiSelectFieldRecord $multiSelectFieldRecord, string $id, Option $option)
    {
        $this->multiSelectFieldRecord = $multiSelectFieldRecord;
        $this->id = $id;
        $this->option = $option;
        $this->removed = false;
    }

    public function remove(): void
    {
        $this->removed = true;
    }

}
