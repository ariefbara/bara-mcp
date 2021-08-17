<?php

namespace App\Http\Controllers\Personnel\Coordinator;

use App\Http\Controllers\Personnel\PersonnelBaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Query\Application\Service\Coordinator\ExecuteTaskInProgram;
use Query\Domain\Model\Firm\Program\Coordinator;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportTranscriptFilter;
use Query\Domain\Task\InProgram\GenerateParticipantEvaluationReportTranscriptTask;
use function response;

class ParticipantEvaluationReportTranscriptController extends PersonnelBaseController
{
    public function transcript($coordinatorId, $participantId)
    {
        $task = $this->buildGenerateParticipantEvaluationReportTranscriptTask($participantId);
        $this->buildService()->execute($this->firmId(), $this->personnelId(), $coordinatorId, $task);
        
        return $this->singleQueryResponse($this->arrayPreserveJsOrder($task->getResult()->toArray()));
    }
    
    public function downloadXlsTranscript($coordinatorId, $participantId)
    {
        $task = $this->buildGenerateParticipantEvaluationReportTranscriptTask($participantId);
        $this->buildService()->execute($this->firmId(), $this->personnelId(), $coordinatorId, $task);
        
        $spreadsheet = new Spreadsheet();
        $task->getResult()->saveToSpreadsheet($spreadsheet);
        
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
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        return new ExecuteTaskInProgram($coordinatorRepository);
    }
    
    protected function buildGenerateParticipantEvaluationReportTranscriptTask($participantId)
    {
        $evaluationReportReporsitory = $this->em->getRepository(EvaluationReport::class);
        
        $evaluationPlanIdList = $this->request->query('evaluationPlanIdList');
        $mentorIdList = $this->request->query('mentorIdList');
        
        $evaluationReportTranscriptFilter = new EvaluationReportTranscriptFilter();
        if (!empty($evaluationPlanIdList)) {
            foreach ($evaluationPlanIdList as $evaluationPlanId) {
                $evaluationReportTranscriptFilter->addEvaluationPlanId($this->stripTagsVariable($evaluationPlanId));
            }
        }
        if (!empty($mentorIdList)) {
            foreach ($mentorIdList as $mentorId) {
                $evaluationReportTranscriptFilter->addMentorId($this->stripTagsVariable($mentorId));
            }
        }
        
        return new GenerateParticipantEvaluationReportTranscriptTask(
                $evaluationReportReporsitory, $participantId, $evaluationReportTranscriptFilter);
    }
}
