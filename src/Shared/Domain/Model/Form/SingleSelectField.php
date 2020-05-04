<?php

namespace Shared\Domain\Model\Form;

use Shared\Domain\Model\{
    Form,
    FormRecord,
    FormRecordData
};

class SingleSelectField
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
     * @var string
     */
    protected $defaultValue = null;

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

    public function setSingleSelectFieldRecordOf(FormRecord $formRecord, FormRecordData $formRecordData): void
    {
        $optionId = $formRecordData->getSelectedOptionIdOf($this->id);

        $this->selectField->assertMandatoryRequirementSatisfied($optionId);
        $selectedOption = empty($optionId) ? null : $this->selectField->getOptionOrDie($optionId);

        $formRecord->setSingleSelectFieldRecord($this, $selectedOption);
    }

}
