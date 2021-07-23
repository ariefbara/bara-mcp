<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\IViewAssetBelongsToPersonnelTask;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;

class ViewEvaluationReportTask implements IViewAssetBelongsToPersonnelTask
{

    /**
     * 
     * @var EvaluationReportRepository
     */
    protected $evaluationReportRepository;

    /**
     * 
     * @var string
     */
    protected $id;

    public function __construct(EvaluationReportRepository $evaluationReportRepository, string $id)
    {
        $this->evaluationReportRepository = $evaluationReportRepository;
        $this->id = $id;
    }

    public function viewAssetBelongsToPersonnel(string $personnelId): array
    {
        $evaluationReport = $this->evaluationReportRepository
                ->anEvaluationReportBelongsToPersonnel($personnelId, $this->id);
        return array_merge([
            'id' => $evaluationReport->getId(),
            'modifiedTime' => $evaluationReport->getModifiedTimeString(),
            'cancelled' => $evaluationReport->isCancelled(),
            'evaluationPlan' => [
                'id' => $evaluationReport->getEvaluationPlan()->getId(),
                'name' => $evaluationReport->getEvaluationPlan()->getName(),
            ], 
            'participant' => [
                'id' => $evaluationReport->getDedicatedMentor()->getParticipant()->getId(),
                'user' => $this->arrayDataOfUser($evaluationReport->getDedicatedMentor()->getParticipant()->getUserParticipant()),
                'client' => $this->arrayDataOfClient($evaluationReport->getDedicatedMentor()->getParticipant()->getClientParticipant()),
                'team' => $this->arrayDataOfTeam($evaluationReport->getDedicatedMentor()->getParticipant()->getTeamParticipant()),
            ],
        ], $evaluationReport->getFormRecord()->toArray());
    }
    protected function arrayDataOfUser(?UserParticipant $userParticipant): ?array
    {
        return empty($userParticipant) ? null : [
            'id' => $userParticipant->getUser()->getId(),
            'name' => $userParticipant->getUser()->getFullName()
        ];
    }
    protected function arrayDataOfClient(?ClientParticipant $clientParticipant): ?array
    {
        return empty($clientParticipant) ? null : [
            'id' => $clientParticipant->getClient()->getId(),
            'name' => $clientParticipant->getClient()->getFullName(),
        ];
    }
    protected function arrayDataOfTeam(?TeamProgramParticipation $teamParticipant): ?array
    {
        return empty($teamParticipant) ? null : [
            'id' => $teamParticipant->getTeam()->getId(),
            'name' => $teamParticipant->getTeam()->getName(),
        ];
    }

}
