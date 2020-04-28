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

    function getId(): string
    {
        return $this->id;
    }
    
    public function getName(): string
    {
        return $this->field->getName();
    }

    public function getPosition(): ?string
    {
        return $this->field->getPosition();
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
