<?php

namespace Query\Domain\Model\Firm\FeedbackForm;

use Query\Domain\Model\Firm\FeedbackForm;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\SharedModel\ReportSpreadsheet\CustomFieldColumnsPayload;
use Query\Domain\SharedModel\ReportSpreadsheet\IReportSheet;
use Query\Domain\SharedModel\ReportSpreadsheet\ISheetContainer;

class FeedbackFormReportSheet implements IReportSheet
{
    /**
     * 
     * @var FeedbackForm
     */
    protected $feedbackForm;
    /**
     * 
     * @var ISheetContainer
     */
    protected $reportSheet;
    
    public function __construct(FeedbackForm $feedbackForm, ISheetContainer $reportSheet)
    {
        $this->feedbackForm = $feedbackForm;
        $this->reportSheet = $reportSheet;
    }
    
    public function includeReport(EvaluationReport $report): bool
    {
        if ($report->isIncludeableInFeedbackForm($this->feedbackForm)) {
            $this->reportSheet->includeReport($report);
            return true;
        }
        return false;
    }
}
