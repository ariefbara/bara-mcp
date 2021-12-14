<?php

namespace Query\Domain\Service;

class FirmReportSpreadsheetGroupByFeedbackFormBuilderPayload
{

    protected $inspectedClientList = [];
    protected $inspectedFeedbackFormList = [];

    public function getInspectedClientList()
    {
        return $this->inspectedClientList;
    }

    public function getInspectedFeedbackFormList()
    {
        return $this->inspectedFeedbackFormList;
    }

    public function __construct()
    {
        
    }
    
    public function inspectClientList(string $clientId): self
    {
        $this->inspectedClientList[] = $clientId;
        return $this;
    }

    public function inspectFeedbackForm(FeedbackFormReportSheetRequest $feedbackFormReportSheetRequest): self
    {
        $this->inspectedFeedbackFormList[] = $feedbackFormReportSheetRequest;
        return $this;
    }



}
