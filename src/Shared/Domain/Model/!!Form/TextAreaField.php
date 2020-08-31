<?php

namespace Shared\Domain\Model\Form;

use Resources\{
    Domain\ValueObject\IntegerRange,
    Exception\RegularException
};
use Shared\Domain\Model\{
    Form,
    FormRecord,
    FormRecordData
};

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
    protected $field;

    /**
     *
     * @var string
     */
    protected $defaultValue = null;

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
     * @var bool
     */
    protected $removed;

    public function getName(): string
    {
        return $this->field->getName();
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
        ;
    }

    public function setTextAreaFieldRecordOf(FormRecord $formRecord, FormRecordData $formRecordData): void
    {
        $value = $formRecordData->getTextAreaFieldRecordDataOf($this->id);

        $this->field->assertMandatoryRequirementSatisfied($value);
        if (!empty($value)) {
            if (!$this->minMaxValue->contain(strlen($value))) {
                $errorDetail = "bad request: input value for {$this->field->getName()} field is out of range";
                throw RegularException::badRequest($errorDetail);
            }
        }

        $formRecord->setTextAreaFieldRecord($this, $value);
    }

}
