<?php

namespace Query\Domain\Model\Shared\Form;

use Query\Domain\Model\Firm\Program\EvaluationPlan\SummaryTable\IField;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Model\Shared\Form;
use Query\Domain\Model\Shared\FormRecord;
use Query\Domain\SharedModel\ReportSpreadsheet\ReportSheet\IField as IField2;
use Resources\Domain\ValueObject\IntegerRange;

class AttachmentField implements IField, IField2
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
        return $this->fieldVO->getName();
    }

    function getDescription(): ?string
    {
        return $this->fieldVO->getDescription();
    }

    function getPosition(): ?string
    {
        return $this->fieldVO->getPosition();
    }

    function isMandatory(): bool
    {
        return $this->fieldVO->isMandatory();
    }

    function getMinValue(): ?int
    {
        return $this->minMaxValue->getMinValue();
    }

    function getMaxValue(): ?int
    {
        return $this->minMaxValue->getMaxValue();
    }

    public function extractCorrespondingValueFromRecord(FormRecord $formRecord)
    {
        return $formRecord->getFileInfoListOfAttachmentFieldRecordCorrespondWith($this);
    }

    public function getCorrespondingValueFromRecord(IContainFieldRecord $containFieldRecord)
    {
        return $containFieldRecord->getFileInfoListOfAttachmentFieldRecordCorrespondWith($this);
    }

    public function getLabel(): string
    {
        return $this->getName();
    }

    public function getCorrespondingValueFromEvaluationReport(EvaluationReport $report)
    {
        return $report->getFileInfoListOfAttachmentFieldRecordCorrespondWith($this);
    }

}
