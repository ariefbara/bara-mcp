<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\FormRecordToArrayDataConverter;
use Query\Application\Service\Client\ExecuteTask;
use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\ITaskExecutableByClient;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Task\Client\ShowActiveEvaluationReportTask;
use Query\Domain\Task\Client\ShowAllActiveEvaluationReportsTask;
use Query\Domain\Task\PaginationPayload;

class EvaluationReportController extends ClientBaseController
{
    public function showAll()
    {
        $evaluationReportRepository = $this->em->getRepository(EvaluationReport::class);
        $payload = new PaginationPayload($this->getPage(), $this->getPageSize());
        $task = new ShowAllActiveEvaluationReportsTask($evaluationReportRepository, $payload);
        $this->executeTask($task);
        
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
                'team' => $this->arrayDataOfTeam($evaluationReport->getDedicatedMentor()->getParticipant()->getTeamParticipant()),
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    public function show($id)
    {
        $evaluationReportRepository = $this->em->getRepository(EvaluationReport::class);
        $task = new ShowActiveEvaluationReportTask($evaluationReportRepository, $id);
        $this->executeTask($task);
        
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
        $result['team'] = $this->arrayDataOfTeam($evaluationReport->getDedicatedMentor()->getParticipant()->getTeamParticipant());
        return $result;
    }
    protected function arrayDataOfTeam(?TeamProgramParticipation $teamParticipant): ?array
    {
        return empty($teamParticipant) ? null : [
            'id' => $teamParticipant->getTeam()->getId(),
            'name' => $teamParticipant->getTeam()->getName(),
        ];
    }
    
    protected function executeTask(ITaskExecutableByClient $task): void
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $service = new ExecuteTask($clientRepository);
        $service->execute($this->firmId(), $this->clientId(), $task);
    }
}
