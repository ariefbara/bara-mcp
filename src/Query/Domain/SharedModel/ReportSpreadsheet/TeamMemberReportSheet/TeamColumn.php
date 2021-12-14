<?php

namespace Query\Domain\SharedModel\ReportSpreadsheet\TeamMemberReportSheet;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\SharedModel\ReportSpreadsheet\TeamMemberReportSheet;

class TeamColumn
{

    /**
     * 
     * @var TeamMemberReportSheet
     */
    protected $teamMemberReportSheet;

    /**
     * 
     * @var int
     */
    protected $colNumber;

    public function __construct(TeamMemberReportSheet $teamMemberReportSheet, int $colNumber)
    {
        $this->teamMemberReportSheet = $teamMemberReportSheet;
        $this->colNumber = $colNumber;
        $this->teamMemberReportSheet->addHeaderColumnLabel($this->colNumber, 'Team');
    }
    
    public function insertCorrespondingReportValue(EvaluationReport $report): void
    {
        $value = $report->getTeamName();
        $this->teamMemberReportSheet->insertIntoCell($this->colNumber, $value);
    }

}
