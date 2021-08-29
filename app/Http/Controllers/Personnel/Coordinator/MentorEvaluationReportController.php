<?php

namespace App\Http\Controllers\Personnel\Coordinator;

use App\Http\Controllers\Personnel\PersonnelBaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Query\Application\Service\Coordinator\ExecuteTaskInProgram;
use Query\Domain\Model\Firm\Program\Coordinator;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportSummaryFilter;
use Query\Domain\Task\InProgram\GenerateProgramEvaluationReportSummaryTask;
use Query\Domain\Task\InProgram\GenerateProgramMentorEvaluationReportSummaryTask;
use Query\Domain\Task\InProgram\IProgramEvaluationReportSummaryResult;
use Query\Domain\Task\InProgram\ParticipantEvaluationReportSummaryResult;
use function response;

class MentorEvaluationReportController extends PersonnelBaseController
{
    public function summary($coordinatorId)
    {
        $result = new ParticipantEvaluationReportSummaryResult();
        $task = $this->buildGenerateProgramEvaluationReportSummaryTask($result);
        
        $this->buildService()->execute($this->firmId(), $this->personnelId(), $coordinatorId, $task);
        
        return $this->singleQueryResponse($result->toRelationalArray());
    }
    public function downloadXlsSummary($coordinatorId)
    {
        $result = new ParticipantEvaluationReportSummaryResult();
        $task = $this->buildGenerateProgramEvaluationReportSummaryTask($result);
        
        $this->buildService()->execute($this->firmId(), $this->personnelId(), $coordinatorId, $task);
        
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex($spreadsheet->getActiveSheetIndex());
        $result->saveToSpreadsheet($spreadsheet);
        
        $writer = new Xlsx($spreadsheet);
        
        $callback = function() use($writer) {
            $file = fopen('php://output', 'w');
            $writer->save($file);
            fclose($file);
            return $file;
        };
        
        $headers = [
            "Content-type" => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename=evaluation-report-summary.xls",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        return response()->stream($callback, 200, $headers);
    }
    
    protected function buildService()
    {
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        return new ExecuteTaskInProgram($coordinatorRepository);
    }
    
    protected function buildGenerateProgramEvaluationReportSummaryTask(IProgramEvaluationReportSummaryResult $result)
    {
        $evaluationReportRepository = $this->em->getRepository(EvaluationReport::class);
        
        $evaluationReportFilter = new EvaluationReportSummaryFilter();
        $evaluationPlanIdList = $this->request->query('evaluationPlanIdList');
        $participantIdList = $this->request->query('participantIdList');
        $mentorIdList = $this->request->query('mentorIdList');
        
        if (!empty($evaluationPlanIdList)) {
            foreach ($evaluationPlanIdList as $evaluationPlanId) {
                $evaluationReportFilter->addEvaluationPlanId($this->stripTagsVariable($evaluationPlanId));
            }
        }
        if (!empty($participantIdList)) {
            foreach ($participantIdList as $participantId) {
                $evaluationReportFilter->addParticipantId($this->stripTagsVariable($participantId));
            }
        }
        if (!empty($mentorIdList)) {
            foreach ($mentorIdList as $mentorId) {
                $evaluationReportFilter->addMentorId($this->stripTagsVariable($mentorId));
            }
        }
        
        return new GenerateProgramEvaluationReportSummaryTask(
                $evaluationReportRepository, $evaluationReportFilter, $result);
    }
}
