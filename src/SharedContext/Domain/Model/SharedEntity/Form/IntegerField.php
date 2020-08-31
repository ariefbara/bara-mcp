<?php

namespace SharedContext\Domain\Model\SharedEntity\Form;

use Resources\ {
    Domain\ValueObject\IntegerRange,
    Exception\RegularException,
    ValidationRule,
    ValidationService
};
use SharedContext\Domain\Model\SharedEntity\ {
    Form,
    FormRecord,
    FormRecordData
};

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
    protected $field;

    /**
     *
     * @var int
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

    public function setIntegerFieldRecordOf(FormRecord $formRecord, FormRecordData $formRecordData): void
    {
        $value = $formRecordData->getIntegerFieldRecordDataOf($this->id);

        $this->field->assertMandatoryRequirementSatisfied($value);

        $errorDetail = "bad request: input value of {$this->field->getName()} field is out of range";

        ValidationService::build()
                ->addRule(ValidationRule::optional(ValidationRule::integerValue()))
                ->execute($value, $errorDetail);

        if (!empty($value)) {
            if (!$this->minMaxValue->contain($value)) {
                throw RegularException::badRequest($errorDetail);
            }
        }

        $formRecord->setIntegerFieldRecord($this, $value);
    }

}
