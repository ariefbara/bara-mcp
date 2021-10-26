<?php

namespace App\Http\Controllers\Personnel\ProgramConsultation;

use Query\Application\Service\Consultant\ViewDedicatedMentor;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;
use Query\Domain\Model\Firm\Team;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;

class DedicatedMentorController extends ProgramConsultationBaseController
{

    public function showAll($programConsultationId)
    {
        $cancelledStatus = $this->filterBooleanOfQueryRequest('cancelledStatus');
        $dedicatedMentors = $this->buildViewService()->showAll(
                $this->firmId(), $this->personnelId(), $programConsultationId, $this->getPage(), $this->getPageSize(),
                $cancelledStatus);
        
        $result = [];
        $result['total'] = count($dedicatedMentors);
        foreach ($dedicatedMentors as $dedicatedMentor) {
            $result['list'][] = [
                'id' => $dedicatedMentor->getId(),
                'modifiedTime' => $dedicatedMentor->getModifiedTimeString(),
                'cancelled' => $dedicatedMentor->isCancelled(),
                'participant' => [
                    'id' => $dedicatedMentor->getParticipant()->getId(),
                    'name' => $dedicatedMentor->getParticipant()->getName(),
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }

    public function show($programConsultationId, $dedicatedMentorId)
    {
        $dedicatedMentor = $this->buildViewService()
                ->showById($this->firmId(), $this->personnelId(), $programConsultationId, $dedicatedMentorId);
        return $this->singleQueryResponse($this->arrayDataOfDedicatedMentor($dedicatedMentor));
    }

    protected function arrayDataOfDedicatedMentor(DedicatedMentor $dedicatedMentor): array
    {
        return [
            'id' => $dedicatedMentor->getId(),
            'modifiedTime' => $dedicatedMentor->getModifiedTimeString(),
            'cancelled' => $dedicatedMentor->isCancelled(),
            'participant' => [
                'id' => $dedicatedMentor->getParticipant()->getId(),
                'team' => $this->arrayDataOfTeam($dedicatedMentor->getParticipant()->getTeamParticipant()),
                'client' => $this->arrayDataOfClient($dedicatedMentor->getParticipant()->getClientParticipant()),
                'user' => $this->arrayDataOfUser($dedicatedMentor->getParticipant()->getUserParticipant()),
            ],
        ];
    }
    protected function arrayDataOfTeam(?TeamProgramParticipation $teamParticipant): ?array
    {
        return empty($teamParticipant) ? null : [
            'id' => $teamParticipant->getTeam()->getId(),
            'name' => $teamParticipant->getTeam()->getName(),
            'members' => $this->arrayDataOfTeamMembers($teamParticipant->getTeam()),
        ];
    }
    protected function arrayDataOfTeamMembers(Team $team): array
    {
        $members = [];
        foreach ($team->iterateActiveMember() as $member) {
            $members[] = [
                'id' => $member->getId(),
                'client' => [
                    'id' => $member->getClient()->getId(),
                    'name' => $member->getClient()->getFullName(),
                ],
            ];
        }
        return $members;
    }
    protected function arrayDataOfClient(?ClientParticipant $clientParticipant): ?array
    {
        return empty($clientParticipant) ? null : [
            'id' => $clientParticipant->getClient()->getId(),
            'name' => $clientParticipant->getClient()->getFullName(),
        ];
    }
    protected function arrayDataOfUser(?UserParticipant $userParticipant): ?array
    {
        return empty($userParticipant) ? null : [
            'id' => $userParticipant->getUser()->getId(),
            'name' => $userParticipant->getUser()->getFullName(),
        ];
    }

    protected function buildViewService()
    {
        $consultantRepository = $this->em->getRepository(Consultant::class);
        $dedicatedMentorRepository = $this->em->getRepository(DedicatedMentor::class);
        return new ViewDedicatedMentor($consultantRepository, $dedicatedMentorRepository);
    }

}
