<?php

namespace Query\Domain\Task\InFirm;

use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\FirmClientTranscriptWorkbookGroupByFeedbackForm;
use Query\Domain\Model\Firm\ITaskInFirmExecutableByManager;
use Query\Domain\SharedModel\IWorkbook;
use Query\Domain\Task\Dependency\Firm\ClientRepository;
use Query\Domain\Task\Dependency\Firm\FeedbackFormRepository;

class BuildFirmClientTranscriptWorkbookGroupByFeedbackFormTask implements ITaskInFirmExecutableByManager
{

    /**
     * 
     * @var ClientRepository
     */
    protected $clientRepository;

    /**
     * 
     * @var FeedbackFormRepository
     */
    protected $feedbackFormRepository;

    /**
     * 
     * @var IWorkbook
     */
    protected $workbook;

    /**
     * 
     * @var BuildReportGroupByFeedbackFormPayload
     */
    protected $payload;

    /**
     * 
     * @var FirmClientTranscriptWorkbookGroupByFeedbackForm|null
     */
    public $result;

    public function __construct(
            ClientRepository $clientRepository, FeedbackFormRepository $feedbackFormRepository, IWorkbook $workbook,
            BuildReportGroupByFeedbackFormPayload $payload)
    {
        $this->clientRepository = $clientRepository;
        $this->feedbackFormRepository = $feedbackFormRepository;
        $this->workbook = $workbook;
        $this->payload = $payload;
    }

    public function executeTaskInFirm(Firm $firm): void
    {
        $this->result = new FirmClientTranscriptWorkbookGroupByFeedbackForm($this->workbook);

        foreach ($this->payload->getReportedFeedbackFormRequestList() as $reportedFeedbackFormRequest) {
            $feedbackForm = $this->feedbackFormRepository->aFeedbackFormOfId($reportedFeedbackFormRequest->getFeedbackFormId());
            $teamMemberReportSheetPayload = $reportedFeedbackFormRequest->getTeamMemberReportSheetPayload();
            $customFieldColumnsPayload = $reportedFeedbackFormRequest->getCustomFieldColumnsPayload();
            $this->result->inspectFeedbackForm($feedbackForm, $teamMemberReportSheetPayload, $customFieldColumnsPayload);
        }

        $clients = $this->clientRepository->allNonPaginatedActiveClientInFirm($firm,
                $this->payload->getInspectedClientList());
        foreach ($clients as $client) {
            $this->result->addClientTranscriptSpreadsheet($client);
        }
    }

}
