<?php

namespace Query\Domain\Task\InFirm;

use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\FirmReportSpreadsheetGroupByFeedbackForm;
use Query\Domain\Model\Firm\ITaskInFirmExecutableByManager;
use Query\Domain\SharedModel\ISpreadsheet;
use Query\Domain\Task\Dependency\Firm\ClientRepository;
use Query\Domain\Task\Dependency\Firm\FeedbackFormRepository;

class BuildReportSpreadsheetGroupByFeedbackFormTask implements ITaskInFirmExecutableByManager
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
     * @var ISpreadsheet
     */
    protected $spreadsheet;

    /**
     * 
     * @var BuildReportGroupByFeedbackFormPayload
     */
    protected $payload;

    /**
     * 
     * @var FirmReportSpreadsheetGroupByFeedbackForm|null
     */
    public $result;

    public function __construct(
            FeedbackFormRepository $feedbackFormRepository, ClientRepository $clientRepository,
            ISpreadsheet $spreadsheet, BuildReportGroupByFeedbackFormPayload $payload)
    {
        $this->feedbackFormRepository = $feedbackFormRepository;
        $this->clientRepository = $clientRepository;
        $this->spreadsheet = $spreadsheet;
        $this->payload = $payload;
    }

    public function executeTaskInFirm(Firm $firm): void
    {
        $inspectedClientList = new Firm\Team\Member\InspectedClientList();
        foreach ($this->payload->getInspectedClientList() as $clientId) {
            $client = $this->clientRepository->aClientOfId($clientId);
            $inspectedClientList->addClient($client);
        }
        $this->result = new FirmReportSpreadsheetGroupByFeedbackForm($inspectedClientList, $this->spreadsheet);
        foreach ($this->payload->getReportedFeedbackFormRequestList() as $request) {
            $feedbackForm = $this->feedbackFormRepository->aFeedbackFormOfId($request->getFeedbackFormId());
            $teamMemberReportSheetPayload = $request->getTeamMemberReportSheetPayload();
            $customFieldColumnsPayload = $request->getCustomFieldColumnsPayload();
            $this->result->addReportSheet($feedbackForm, $teamMemberReportSheetPayload, $customFieldColumnsPayload);
        }
    }

}
