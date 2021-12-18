<?php

namespace Query\Domain\Model\Firm\Client;

use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\SharedModel\ReportSpreadsheet\IReportSheet;
use Query\Domain\SharedModel\ReportSpreadsheet\TeamMemberReportSheet;

class ClientSingleTableTrascriptSheet implements IReportSheet
{

    /**
     * 
     * @var Client
     */
    protected $client;

    /**
     * 
     * @var TeamMemberReportSheet
     */
    protected $teamMemberReportSheet;

    public function __construct(Client $client, TeamMemberReportSheet $teamMemberReportSheet)
    {
        $this->client = $client;
        $this->teamMemberReportSheet = $teamMemberReportSheet;
        $this->teamMemberReportSheet->setLabel($this->client->getFullName());
    }

    public function includeReport(EvaluationReport $report): bool
    {
        if ($report->correspondWithClient($this->client)) {
            $this->teamMemberReportSheet->includeReport($report);
        }
        return true;
    }

}
