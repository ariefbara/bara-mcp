<?php

namespace Query\Domain\SharedModel\ReportSpreadsheet;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Model\Firm\Team\Member\InspectedClientList;
use Query\Domain\SharedModel\ReportSpreadsheet\ReportSheet\IField;
use Query\Domain\SharedModel\ReportSpreadsheet\TeamMemberReportSheet\TeamColumn;

class TeamMemberReportSheet implements ISheetContainer, IReportSheet
{

    /**
     * 
     * @var InspectedClientList
     */
    protected $inspectedClientList;

    /**
     * 
     * @var TeamColumn|null
     */
    protected $teamColumn;

    /**
     * 
     * @var int|null
     */
    protected $individualColNumber;

    /**
     * 
     * @var ReportSheet
     */
    protected $reportSheet;

    protected function setTeamColumn(TeamMemberReportSheetPayload $payload): void
    {
        if ($payload->isTeamInspected()) {
            $this->teamColumn = new TeamColumn($this, $payload->getTeamColNumber());
        }
    }

    protected function setIndividualColumn(TeamMemberReportSheetPayload $payload): void
    {
        if ($payload->isIndividualInspected()) {
            $this->individualColNumber = $payload->getIndividualColNumber();
            $this->addHeaderColumnLabel($this->individualColNumber, 'Individual');
        }
    }

    public function __construct(InspectedClientList $inspectedClientList, ISheet $sheet,
            TeamMemberReportSheetPayload $payload)
    {
        $this->inspectedClientList = $inspectedClientList;
        $this->reportSheet = new ReportSheet($payload->getReportSheetPayload(), $sheet);
        $this->setTeamColumn($payload);
        $this->setIndividualColumn($payload);
    }

    public function addHeaderColumnLabel(int $colNumber, string $label): void
    {
        $this->reportSheet->addHeaderColumnLabel($colNumber, $label);
    }

    public function addFieldColumn(IField $field, ?int $colNumber): void
    {
        if (is_null($colNumber)) {
            $colNumber = 1 + $this->reportSheet->getColumnCount() + intval(isset($this->teamColumn)) + intval(isset($this->individualColNumber));
        }
        $this->reportSheet->addFieldColumn($field, $colNumber);
    }

    public function includeReport(EvaluationReport $report): bool
    {
        if (isset($this->individualColNumber)) {
            $individualNames = $report->getListOfIndividualParticipantNameOrMemberNameOfTeamParticipantWithinInspection(
                    $this->inspectedClientList);
            foreach ($individualNames as $individualName) {
                $this->reportSheet->insertIntoCell($this->individualColNumber, $individualName);
                if (isset($this->teamColumn)) {
                    $this->teamColumn->insertCorrespondingReportValue($report);
                }
                $this->reportSheet->includeReport($report);
            }
        } else {
            if (isset($this->teamColumn)) {
                $this->teamColumn->insertCorrespondingReportValue($report);
            }
            $this->reportSheet->includeReport($report);
        }
        return true;
    }

    public function insertIntoCell(int $colNumber, $value): void
    {
        $this->reportSheet->insertIntoCell($colNumber, $value);
    }

    public function setLabel(string $label): void
    {
        $this->reportSheet->setLabel($label);
    }

}
