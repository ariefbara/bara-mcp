<?php

namespace Firm\Domain\Model\Shared\Form\SelectField;

use Firm\Domain\Model\Shared\Form\SelectField;
use Resources\{
    ValidationRule,
    ValidationService
};

class Option
{

    /**
     *
     * @var SelectField
     */
    protected $selectField;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var string
     */
    protected $description = null;

    /**
     *
     * @var string
     */
    protected $position = null;

    /**
     *
     * @var bool
     */
    protected $removed = false;

    function getId(): string
    {
        return $this->id;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function setName(string $name)
    {
        $errorDetail = 'bad request: option name is mandatory';
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, $errorDetail);
        $this->name = $name;
    }

    function __construct(SelectField $selectField, string $id, OptionData $optionData)
    {
        $this->selectField = $selectField;
        $this->id = $id;
        $this->setName($optionData->getName());
        $this->description = $optionData->getDescription();
        $this->position = $optionData->getPosition();
        $this->removed = false;
    }

    public function update(OptionData $optionData): void
    {
        $this->setName($optionData->getName());
        $this->description = $optionData->getDescription();
        $this->position = $optionData->getPosition();
    }

    public function remove(): void
    {
        $this->removed = true;
    }

}
