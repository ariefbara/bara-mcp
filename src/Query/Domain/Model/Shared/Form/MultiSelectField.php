<?php

namespace Query\Domain\Model\Shared\Form;

use Query\Domain\Model\Firm\Program\EvaluationPlan\SummaryTable\IField;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Model\Shared\Form;
use Query\Domain\Model\Shared\FormRecord;
use Query\Domain\SharedModel\ReportSpreadsheet\ReportSheet\IField as IField2;
use Resources\Domain\ValueObject\IntegerRange;
use SharedContext\Domain\Model\SharedEntity\Form\SelectField\Option;

class MultiSelectField implements IField, IField2
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
    protected $removed = false;

    function getForm(): Form
    {
        return $this->form;
    }

    function getId(): string
    {
        return $this->id;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
        ;
    }

    function getName(): string
    {
        return $this->selectField->getName();
    }

    function getDescription(): ?string
    {
        return $this->selectField->getDescription();
    }

    function getPosition(): ?string
    {
        return $this->selectField->getPosition();
    }

    function isMandatory(): bool
    {
        return $this->selectField->isMandatory();
    }

    /**
     * 
     * @return Option[]
     */
    function getUnremovedOptions()
    {
        return $this->selectField->getUnremovedOptions();
    }

    function getMinValue(): ?int
    {
        return $this->minMaxValue->getMinValue();
    }

    function getMaxValue(): ?int
    {
        return $this->minMaxValue->getMaxValue();
    }
    
    public function idEquals(string $id): bool
    {
        return $this->id === $id;
    }

    public function extractCorrespondingValueFromRecord(FormRecord $formRecord)
    {
        return $formRecord->getListOfMultiSelectFieldRecordSelectedOptionNameCorrespondWith($this);
    }

    public function getCorrespondingValueFromRecord(IContainFieldRecord $containFieldRecord)
    {
        return $containFieldRecord->getListOfMultiSelectFieldRecordSelectedOptionNameCorrespondWith($this);
    }

    public function getLabel(): string
    {
        return $this->getName();
    }

    public function getCorrespondingValueFromEvaluationReport(EvaluationReport $report)
    {
        return $report->getListOfMultiSelectFieldRecordSelectedOptionNameCorrespondWith($this);
    }

}
