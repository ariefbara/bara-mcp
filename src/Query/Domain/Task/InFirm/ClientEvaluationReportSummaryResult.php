<?php

namespace Query\Domain\Task\InFirm;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Query\Domain\Model\Firm\Program\EvaluationPlan\ClientSummaryTable;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;

class ClientEvaluationReportSummaryResult implements IFirmEvaluationReportSummaryResult
{

    /**
     * 
     * @var ClientSummaryTable[]
     */
    protected $clientSummaryTables;

    public function __construct()
    {
        $this->clientSummaryTables = [];
    }

    public function includeEvaluationReport(EvaluationReport $evaluationReport): void
    {
        $includedInExistingTable = false;
        foreach ($this->clientSummaryTables as $clientSummaryTable) {
            if ($clientSummaryTable->canInclude($evaluationReport)) {
                $clientSummaryTable->includeEvaluationReport($evaluationReport);
                $includedInExistingTable = true;
                break;
            }
        }
        if (!$includedInExistingTable) {
            $this->clientSummaryTables[] = new ClientSummaryTable($evaluationReport);
        }
    }
    
    public function saveToSpreadsheet(Spreadsheet $spreadsheet): void
    {
        foreach ($this->clientSummaryTables as $clientSummaryTable) {
            $clientSummaryTable->saveToSpreadsheet($spreadsheet);
        }
    }

    public function toRelationalArray(): array
    {
        $result = [];
        foreach ($this->clientSummaryTables as $clientSummaryTable) {
            $result[] = $clientSummaryTable->toRelationalArray();
        }
        return $result;
    }

}
