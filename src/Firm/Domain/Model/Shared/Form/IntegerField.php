<?php

namespace Firm\Domain\Model\Shared\Form;

use Firm\Domain\Model\Shared\Form;
use Resources\Domain\ValueObject\IntegerRange;

class IntegerField
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
     * @var string
     */
    protected $placeholder = null;

    /**
     *
     * @var int
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

    protected function setDefaultValue(?int $defaultValue): void
    {
        $this->defaultValue = $defaultValue;
    }

    function __construct(Form $form, string $id, IntegerFieldData $integerFieldData)
    {
        $this->form = $form;
        $this->id = $id;
        $this->fieldVO = new FieldVO($integerFieldData->getFieldData());
        $this->minMaxValue = new IntegerRange($integerFieldData->getMinValue(), $integerFieldData->getMaxValue());
        $this->setDefaultValue($integerFieldData->getDefaultValue());
        $this->placeholder = $integerFieldData->getPlaceholder();
        $this->removed = false;
    }

    public function update(IntegerFieldData $integerFieldData): void
    {
        $this->fieldVO = new FieldVO($integerFieldData->getFieldData());
        $this->minMaxValue = new IntegerRange($integerFieldData->getMinValue(), $integerFieldData->getMaxValue());
        $this->setDefaultValue($integerFieldData->getDefaultValue());
        $this->placeholder = $integerFieldData->getPlaceholder();
    }

    public function remove(): void
    {
        $this->removed = true;
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

}
