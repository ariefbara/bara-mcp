<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\Client\ClientBaseController;
use App\Http\Controllers\FormRecordToArrayDataConverter;
use Query\Application\Service\Client\AsProgramParticipant\ExecuteParticipantTask;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Task\PaginationPayload;
use Query\Domain\Task\Participant\ShowAllMentorEvaluationReportsTask;
use Query\Domain\Task\Participant\ShowEvaluationReportTask;

class EvaluationReportController extends ClientBaseController
{

    public function showAll($programParticipationId)
    {
        $evaluationReportRepository = $this->em->getRepository(EvaluationReport::class);
        $payload = new PaginationPayload($this->getPage(), $this->getPageSize());
        $task = new ShowAllMentorEvaluationReportsTask($evaluationReportRepository, $payload);
        $this->executeParticipantTaskService($programParticipationId, $task);
        
        $result = [];
        $result['total'] = count($task->result);
        foreach ($task->result as $evaluationReport) {
            $result['list'][] = [
                'id' => $evaluationReport->getId(),
                'modifiedTime' => $evaluationReport->getModifiedTimeString(),
                'evaluationPlan'=> [
                    'id' => $evaluationReport->getEvaluationPlan()->getId(),
                    'name' => $evaluationReport->getEvaluationPlan()->getName(),
                ],
                'mentor' => [
                    'id' => $evaluationReport->getDedicatedMentor()->getConsultant()->getId(),
                    'personnel' => [
                        'id' => $evaluationReport->getDedicatedMentor()->getConsultant()->getPersonnel()->getId(),
                        'name' => $evaluationReport->getDedicatedMentor()->getConsultant()->getPersonnel()->getName(),
                    ],
                ],
            ];
        }
        return $this->listQueryResponse($result);
        
    }

    public function show($programParticipationId, $id)
    {
        $evaluationReportRepository = $this->em->getRepository(EvaluationReport::class);
        $task = new ShowEvaluationReportTask($evaluationReportRepository, $id);
        $this->executeParticipantTaskService($programParticipationId, $task);
        
        return $this->singleQueryResponse($this->arrayDataOfEvaluationReport($task->result));
    }

    protected function arrayDataOfEvaluationReport(EvaluationReport $evaluationReport): array
    {
        $result = (new FormRecordToArrayDataConverter())->convert($evaluationReport);
        $result['id'] = $evaluationReport->getId();
        $result['modifiedTime'] = $evaluationReport->getModifiedTimeString();
        $result['evaluationPlan'] = [
            'id' => $evaluationReport->getEvaluationPlan()->getId(),
            'name' => $evaluationReport->getEvaluationPlan()->getName(),
        ];
        $result['mentor'] = [
            'id' => $evaluationReport->getDedicatedMentor()->getConsultant()->getId(),
            'personnel' => [
                'id' => $evaluationReport->getDedicatedMentor()->getConsultant()->getPersonnel()->getId(),
                'name' => $evaluationReport->getDedicatedMentor()->getConsultant()->getPersonnel()->getName(),
            ],
        ];
        return $result;
    }

    protected function executeParticipantTaskService(string $participantId, ITaskExecutableByParticipant $task): void
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant::class);
        $service = new ExecuteParticipantTask($clientParticipantRepository);
        $service->execute($this->firmId(), $this->clientId(), $participantId, $task);
    }

}
