<?php

namespace Query\Domain\Model\Firm\Program;

use Query\Domain\Model\Firm\FeedbackForm;
use Query\Domain\Model\Firm\FeedbackForm\FeedbackFormReportSheet;
use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Program\EvaluationPlan\IContainSummaryTable;
use Query\Domain\SharedModel\ReportSpreadsheet\CustomFieldColumnsPayload;
use Query\Domain\SharedModel\ReportSpreadsheet\ISheetContainer;

class EvaluationPlan
{

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var int
     */
    protected $interval;

    /**
     *
     * @var bool
     */
    protected $disabled;

    /**
     *
     * @var FeedbackForm
     */
    protected $reportForm;

    /**
     * 
     * @var Mission|null
     */
    protected $mission;

    function getProgram(): Program
    {
        return $this->program;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->name;
    }

    function getInterval(): int
    {
        return $this->interval;
    }

    public function getMission(): ?Mission
    {
        return $this->mission;
    }

    function isDisabled(): bool
    {
        return $this->disabled;
    }

    function getReportForm(): FeedbackForm
    {
        return $this->reportForm;
    }

    protected function __construct()
    {
        
    }

//    public function toArrayOfSummaryTableHeader(): array
//    {
//        return array_merge(['Participant', 'Mentor'], $this->reportForm->toArrayOfSummaryTableHeader());
//    }
//    
//    public function generateSummaryTableEntryFromRecord(FormRecord $formRecord): array
//    {
//        return $this->reportForm->generateSummaryTableEntryFromRecord($formRecord);
//    }
//    
//    public function toArrayOfHorizontalTranscriptTableHeader(): array
//    {
//        return array_merge(['Mentor'], $this->reportForm->toArrayOfSummaryTableHeader());
//    }

    public function appendAllFieldsAsHeaderColumnOfSummaryTable(
            IContainSummaryTable $containSummaryTable, int $startColNumber): void
    {
        $this->reportForm->appendAllFieldsAsHeaderColumnOfSummaryTable($containSummaryTable, $startColNumber);
    }

    public function buildFeedbackFormReportSheet(ISheetContainer $reportSheet): FeedbackFormReportSheet
    {
        return $this->reportForm->buildFeedbackFormReportSheet($reportSheet);
    }

    public function feedbackFormEquals(FeedbackForm $feedbackForm): bool
    {
        return $this->reportForm === $feedbackForm;
    }

}
