<?php

namespace Shared\Domain\Model\Form;

use Resources\ {
    Domain\ValueObject\IntegerRange,
    Exception\RegularException
};
use Shared\Domain\Model\ {
    Form,
    FormRecord,
    FormRecordData
};

class MultiSelectField
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
     * @var SelectField
     */
    protected $selectField;

    /**
     *
     * @var IntegerRange
     */
    protected $minMaxValue;

    /**
     *
     * @var bool
     */
    protected $removed;

    public function getName(): string
    {
        return $this->selectField->getName();
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
        ;
    }

    public function setMultiSelectFieldRecordOf(FormRecord $formRecord, FormRecordData $formRecordData): void
    {
        $selectedOptions = [];
        $selectedOptionIds = $formRecordData->getSelectedOptionIdListOf($this->id);
        $this->selectField->assertMandatoryRequirementSatisfied($selectedOptionIds);

        foreach ($selectedOptionIds as $selectedOptionId) {
            $selectedOptions[] = $this->selectField->getOptionOrDie($selectedOptionId);
        }
        $this->assertSelectedOptionWithinRange($selectedOptions);

        $formRecord->setMultiSelectFieldRecord($this, $selectedOptions);
    }

    protected function assertSelectedOptionWithinRange(array $selectedOptions): void
    {
        $selectedOptionCount = empty(count($selectedOptions)) ? null : count($selectedOptions);
        if (!empty($selectedOptionCount)) {
            if (!$this->minMaxValue->contain($selectedOptionCount)) {
                $errorDetail = "bad request: selected option for {$this->selectField->getName()} field is out of range";
                throw RegularException::badRequest($errorDetail);
            }
        }
    }

}
