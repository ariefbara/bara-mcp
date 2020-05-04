<?php

namespace Firm\Domain\Model\Shared\Form;

use Firm\Domain\Model\Shared\Form;
use Resources\Domain\ValueObject\IntegerRange;

class AttachmentField
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

    function __construct(Form $form, string $id, AttachmentFieldData $attachmentFieldData)
    {
        $this->form = $form;
        $this->id = $id;
        $this->fieldVO = new FieldVO($attachmentFieldData->getFieldData());
        $this->minMaxValue = new IntegerRange($attachmentFieldData->getMinValue(), $attachmentFieldData->getMaxValue());
        $this->removed = false;
    }

    public function update(AttachmentFieldData $attachmentFieldData): void
    {
        $this->fieldVO = new FieldVO($attachmentFieldData->getFieldData());
        $this->minMaxValue = new IntegerRange($attachmentFieldData->getMinValue(), $attachmentFieldData->getMaxValue());
    }

    public function remove(): void
    {
        $this->removed = true;
    }

}
