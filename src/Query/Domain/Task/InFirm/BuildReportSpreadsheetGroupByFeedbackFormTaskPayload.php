<?php

namespace Query\Domain\Task\InFirm;

class BuildReportSpreadsheetGroupByFeedbackFormTaskPayload
{

    /**
     * 
     * @var string[]
     */
    protected $inspectedClientList = [];

    /**
     * 
     * @var FeedbackFormReportSheetRequest[]
     */
    protected $reportedFeedbackFormRequestList = [];

    public function getInspectedClientList(): array
    {
        return $this->inspectedClientList;
    }

    /**
     * 
     * @return FeedbackFormReportSheetRequest[]
     */
    public function getReportedFeedbackFormRequestList(): array
    {
        return $this->reportedFeedbackFormRequestList;
    }

    public function __construct()
    {
        
    }

    public function inspectClient(string $clientId): self
    {
        $this->inspectedClientList[] = $clientId;
        return $this;
    }

    public function reportFeedbackForm(FeedbackFormReportSheetRequest $feedbackFormReportSheetRequest): self
    {
        $this->reportedFeedbackFormRequestList[] = $feedbackFormReportSheetRequest;
        return $this;
    }

}
