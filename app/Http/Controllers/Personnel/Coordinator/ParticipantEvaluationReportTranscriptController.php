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
use Query\Domain\Task\InProgram\ParticipantEvaluationReportTranscriptResult;
use function response;

class ParticipantEvaluationReportTranscriptController extends PersonnelBaseController
{
    public function transcript($coordinatorId)
    {
        $result = $this->executeGenerateParticipantEvaluationReportTranscriptTaskAndReturnResult($coordinatorId);
        return $this->singleQueryResponse($result->toRelationalArray());
    }
    
    public function downloadXlsTranscript($coordinatorId)
    {
        $result = $this->executeGenerateParticipantEvaluationReportTranscriptTaskAndReturnResult($coordinatorId);
        
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
            "Content-Disposition" => "attachment; filename=evaluation-report-transcript.xlsx",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        return response()->stream($callback, 200, $headers);
    }
    
    protected function buildService()
    {
    }
    
    protected function executeGenerateParticipantEvaluationReportTranscriptTaskAndReturnResult($coordinatorId): ParticipantEvaluationReportTranscriptResult
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
        
        $result = new ParticipantEvaluationReportTranscriptResult();
        $task = new GenerateProgramEvaluationReportSummaryTask(
                $evaluationReportRepository, $evaluationReportFilter, $result);
        
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        (new ExecuteTaskInProgram($coordinatorRepository))
                ->execute($this->firmId(), $this->personnelId(), $coordinatorId, $task);
        
        return $result;
    }
}
