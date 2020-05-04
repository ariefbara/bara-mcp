<?php

namespace Firm\Domain\Model\Shared\Form;

use Firm\Domain\Model\Shared\Form;
use Resources\Domain\ValueObject\IntegerRange;

class TextAreaField
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

    function __construct(Form $form, string $id, TextAreaFieldData $textAreaFieldData)
    {
        $this->form = $form;
        $this->id = $id;
        $this->fieldVO = new FieldVO($textAreaFieldData->getFieldData());
        $this->minMaxValue = new IntegerRange($textAreaFieldData->getMinValue(), $textAreaFieldData->getMaxValue());
        $this->placeholder = $textAreaFieldData->getPlaceholder();
        $this->defaultValue = $textAreaFieldData->getDefaultValue();
        $this->removed = false;
    }

    public function update(TextAreaFieldData $textAreaFieldData): void
    {
        $this->fieldVO = new FieldVO($textAreaFieldData->getFieldData());
        $this->minMaxValue = new IntegerRange($textAreaFieldData->getMinValue(), $textAreaFieldData->getMaxValue());
        $this->placeholder = $textAreaFieldData->getPlaceholder();
        $this->defaultValue = $textAreaFieldData->getDefaultValue();
    }

    public function remove(): void
    {
        $this->removed = true;
    }

}
