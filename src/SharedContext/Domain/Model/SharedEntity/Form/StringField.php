<?php

namespace SharedContext\Domain\Model\SharedEntity\Form;

use Resources\ {
    Domain\ValueObject\IntegerRange,
    Exception\RegularException
};
use SharedContext\Domain\Model\SharedEntity\ {
    Form,
    FormRecord,
    FormRecordData
};

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

    public function setStringFieldRecordOf(FormRecord $formRecord, FormRecordData $formRecordData): void
    {
        $value = $formRecordData->getStringFieldRecordDataOf($this->id);

        $this->field->assertMandatoryRequirementSatisfied($value);
        if (!empty($value)) {
            $errorDetail = "bad request: invalid input length for {$this->field->getName()} field";
            if (!$this->minMaxValue->contain(strlen($value))) {
                throw RegularException::badRequest($errorDetail);
            }
        }

        $formRecord->setStringFieldRecord($this, $value);
    }

}
