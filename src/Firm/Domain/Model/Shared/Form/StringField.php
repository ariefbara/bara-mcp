<?php

namespace Firm\Domain\Model\Shared\Form;

use Firm\Domain\Model\Shared\Form;
use Resources\Domain\ValueObject\IntegerRange;

class StringField
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
     * @var string
     */
    protected $defaultValue = null;

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

    function __construct(Form $form, string $id, StringFieldData $stringFieldData)
    {
        $this->form = $form;
        $this->id = $id;
        $this->fieldVO = new FieldVO($stringFieldData->getFieldData());
        $this->minMaxValue = new IntegerRange($stringFieldData->getMinValue(), $stringFieldData->getMaxValue());
        $this->placeholder = $stringFieldData->getPlaceholder();
        $this->defaultValue = $stringFieldData->getDefaultValue();
        $this->removed = false;
    }

    public function update(StringFieldData $stringFieldData): void
    {
        $this->fieldVO = new FieldVO($stringFieldData->getFieldData());
        $this->minMaxValue = new IntegerRange($stringFieldData->getMinValue(), $stringFieldData->getMaxValue());
        $this->placeholder = $stringFieldData->getPlaceholder();
        $this->defaultValue = $stringFieldData->getDefaultValue();
    }

    public function remove(): void
    {
        $this->removed = true;
    }

}
