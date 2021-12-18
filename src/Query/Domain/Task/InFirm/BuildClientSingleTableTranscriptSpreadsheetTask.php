<?php

namespace Query\Domain\Task\InFirm;

use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\FirmClientSingleTableTranscriptSpreadsheet;
use Query\Domain\Model\Firm\ITaskInFirmExecutableByManager;
use Query\Domain\SharedModel\ISpreadsheet;
use Query\Domain\Task\Dependency\Firm\ClientRepository;

class BuildClientSingleTableTranscriptSpreadsheetTask implements ITaskInFirmExecutableByManager
{

    /**
     * 
     * @var ClientRepository
     */
    protected $clientRepository;

    /**
     * 
     * @var BuildSingleTableReportSpreadsheetPayload
     */
    protected $payload;

    /**
     * 
     * @var ISpreadsheet
     */
    protected $spreadsheet;

    /**
     * 
     * @var FirmClientSingleTableTranscriptSpreadsheet
     */
    public $result;

    public function __construct(
            ClientRepository $clientRepository, BuildSingleTableReportSpreadsheetPayload $payload,
            ISpreadsheet $spreadsheet)
    {
        $this->clientRepository = $clientRepository;
        $this->payload = $payload;
        $this->spreadsheet = $spreadsheet;
    }

    public function executeTaskInFirm(Firm $firm): void
    {
        $this->result = new FirmClientSingleTableTranscriptSpreadsheet(
                $this->spreadsheet, $this->payload->getTeamMemberReportSheetPayload());
        $clientIdList = $this->payload->getInspectedClientList();
        foreach ($this->clientRepository->allNonPaginatedActiveClientInFirm($firm, $clientIdList) as $client) {
            $this->result->inspectClient($client);
        }
    }

}
