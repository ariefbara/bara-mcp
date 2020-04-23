<?php

namespace Firm\Domain\Model\Shared\Form;

use Resources\{
    ValidationRule,
    ValidationService
};

class FieldVO
{

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
    protected $mandatory;

    function getName(): string
    {
        return $this->name;
    }

    function getDescription(): string
    {
        return $this->description;
    }

    function getPosition(): string
    {
        return $this->position;
    }

    function isMandatory(): bool
    {
        return $this->mandatory;
    }

    protected function setName(string $name): void
    {
        $errorDetail = 'bad request: field name is mandatory';
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, $errorDetail);
        $this->name = $name;
    }

    function __construct(FieldData $fieldData)
    {
        $this->setName($fieldData->getName());
        $this->description = $fieldData->getDescription();
        $this->position = $fieldData->getPosition();
        $this->mandatory = boolval($fieldData->getMandatory());
    }

}
