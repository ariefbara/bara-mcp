<?php

namespace Query\Domain\Task\InFirm;

use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\FirmSingleTableReportSpreadsheet;
use Query\Domain\Model\Firm\ITaskInFirmExecutableByManager;
use Query\Domain\Model\Firm\Team\Member\InspectedClientList;
use Query\Domain\SharedModel\ISpreadsheet;
use Query\Domain\Task\Dependency\Firm\ClientRepository;

class BuildSingleTableReportSpreadsheetTask implements ITaskInFirmExecutableByManager
{

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
     * @var BuildSingleTableReportSpreadsheetPayload
     */
    protected $payload;

    /**
     * 
     * @var FirmSingleTableReportSpreadsheet
     */
    public $result;

    public function __construct(
            ClientRepository $clientRepository, ISpreadsheet $spreadsheet,
            BuildSingleTableReportSpreadsheetPayload $payload)
    {
        $this->clientRepository = $clientRepository;
        $this->spreadsheet = $spreadsheet;
        $this->payload = $payload;
    }

    public function executeTaskInFirm(Firm $firm): void
    {
        $inspectedClientList = new InspectedClientList();
        foreach ($this->payload->getInspectedClientList() as $clientId) {
            $inspectedClientList->addClient($this->clientRepository->aClientOfId($clientId));
        }
        $this->result = new FirmSingleTableReportSpreadsheet(
                $inspectedClientList, $this->spreadsheet, $this->payload->getTeamMemberReportSheetPayload());
    }

}
