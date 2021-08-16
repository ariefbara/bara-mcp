<?php

namespace Query\Domain\Model\Shared\Form;

use Query\Domain\Model\Shared\Form;
use Query\Domain\Model\Shared\FormRecord;
use Query\Domain\Model\Shared\IField;
use Resources\Domain\ValueObject\IntegerRange;

class IntegerField implements IField
{

    /**
     *
     * @var Form
     */
    protected $form;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var FieldVO
     */
    protected $fieldVO;

    /**
     *
     * @var IntegerRange
     */
    protected $minMaxValue;

    /**
     *
     * @var string||null
     */
    protected $placeholder = null;

    /**
     *
     * @var int||null
     */
    protected $defaultValue = null;

    /**
     *
     * @var bool
     */
    protected $removed = false;

    function getForm(): Form
    {
        return $this->form;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    function getDefaultValue(): ?int
    {
        return $this->defaultValue;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
        ;
    }

    function getName(): string
    {
        return $this->fieldVO->getName();
    }

    function getDescription(): ?string
    {
        return $this->fieldVO->getDescription();
    }

    function getPosition(): ?string
    {
        return $this->fieldVO->getPosition();
    }

    function isMandatory(): bool
    {
        return $this->fieldVO->isMandatory();
    }

    function getMinValue(): ?int
    {
        return $this->minMaxValue->getMinValue();
    }

    function getMaxValue(): ?int
    {
        return $this->minMaxValue->getMaxValue();
    }
    
    public function idEquals(string $id): bool
    {
        return $this->id === $id;
    }
    
    public function extractCorrespondingValueFromRecord(FormRecord $formRecord)
    {
        $result = $formRecord->getIntegerFieldRecordValueCorrespondWith($this);
        return empty($result) ? null : strval($result);
    }

}
