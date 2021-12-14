<?php

namespace Query\Domain\Service;

class FirmReportSpreadsheetGroupByFeedbackFormBuilder
{

    /**
     * 
     * @var FeedbackFormRepository
     */
    protected $feedbackFormRepository;

    /**
     * 
     * @var ClientRepository
     */
    protected $clientRepository;

    /**
     * 
     * @var FirmReportSpreadsheetGroupByFeedbackFormBuilderPayload
     */
    protected $payload;

    public function __construct(
            FeedbackFormRepository $feedbackFormRepository, ClientRepository $clientRepository,
            FirmReportSpreadsheetGroupByFeedbackFormBuilderPayload $payload)
    {
        $this->feedbackFormRepository = $feedbackFormRepository;
        $this->clientRepository = $clientRepository;
        $this->payload = $payload;
    }
    
    public function execute()
    {
        
    }

}
