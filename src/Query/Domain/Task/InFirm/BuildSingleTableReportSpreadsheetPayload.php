<?php

namespace Query\Domain\Task\InFirm;

use Query\Domain\SharedModel\ReportSpreadsheet\TeamMemberReportSheetPayload;

class BuildSingleTableReportSpreadsheetPayload
{

    /**
     * 
     * @var TeamMemberReportSheetPayload
     */
    protected $teamMemberReportSheetPayload;

    /**
     * 
     * @var string[]
     */
    protected $inspectedClientList = [];

    public function getTeamMemberReportSheetPayload(): TeamMemberReportSheetPayload
    {
        return $this->teamMemberReportSheetPayload;
    }

    public function getInspectedClientList(): array
    {
        return $this->inspectedClientList;
    }

    public function __construct(TeamMemberReportSheetPayload $teamMemberReportSheetPayload)
    {
        $this->teamMemberReportSheetPayload = $teamMemberReportSheetPayload;
    }

    public function inspectClient(string $clientId): self
    {
        $this->inspectedClientList[] = $clientId;
        return $this;
    }

}
