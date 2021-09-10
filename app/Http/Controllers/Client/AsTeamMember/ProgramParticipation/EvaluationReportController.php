<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use App\Http\Controllers\FormRecordToArrayDataConverter;
use Query\Application\Service\Client\TeamMember\ExecuteParticipantTask;
use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Model\Firm\Team\Member;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Task\PaginationPayload;
use Query\Domain\Task\Participant\ShowAllMentorEvaluationReportsTask;
use Query\Domain\Task\Participant\ShowEvaluationReportTask;

class EvaluationReportController extends AsTeamMemberBaseController
{

    public function showAll($teamId, $teamProgramParticipationId)
    {
        $evaluationReportRepository = $this->em->getRepository(EvaluationReport::class);
        $payload = new PaginationPayload($this->getPage(), $this->getPageSize());
        $task = new ShowAllMentorEvaluationReportsTask($evaluationReportRepository, $payload);
        $this->executeParticipantTaskService($teamId, $teamProgramParticipationId, $task);
        
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

    public function show($teamId, $teamProgramParticipationId, $id)
    {
        $evaluationReportRepository = $this->em->getRepository(EvaluationReport::class);
        $task = new ShowEvaluationReportTask($evaluationReportRepository, $id);
        $this->executeParticipantTaskService($teamId, $teamProgramParticipationId, $task);
        
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

    protected function executeParticipantTaskService(
            string $teamId, string $teamParticipantId, ITaskExecutableByParticipant $task): void
    {
        $teamMemberRepository = $this->em->getRepository(Member::class);
        $teamParticipantRepository = $this->em->getRepository(TeamProgramParticipation::class);
        $service = new ExecuteParticipantTask($teamMemberRepository, $teamParticipantRepository);
        
        $service->execute($this->firmId(), $this->clientId(), $teamId, $teamParticipantId, $task);
    }

}
