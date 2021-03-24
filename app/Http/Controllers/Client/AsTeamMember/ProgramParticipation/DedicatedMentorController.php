<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use Query\Application\Service\TeamMember\ViewDedicatedMentor;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;
use Query\Domain\Model\Firm\Team\Member;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;

class DedicatedMentorController extends AsTeamMemberBaseController
{

    public function showAll($teamId, $teamProgramParticipationId)
    {
        $cancelledStatus = $this->filterBooleanOfQueryRequest('cancelledStatus');
        $dedicatedMentors = $this->buildViewService()->showAll(
                $this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $this->getPage(),
                $this->getPageSize(), $cancelledStatus);

        $result = [];
        $result['total'] = count($dedicatedMentors);
        foreach ($dedicatedMentors as $dedicatedMentor) {
            $result['list'][] = $this->arrayDataOfDedicatedMentor($dedicatedMentor);
        }
        return $this->listQueryResponse($result);
    }

    public function show($teamId, $teamProgramParticipationId, $dedicatedMentorId)
    {
        $dedicatedMentor = $this->buildViewService()->showById(
                $this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $dedicatedMentorId);
        return $this->singleQueryResponse($this->arrayDataOfDedicatedMentor($dedicatedMentor));
    }

    protected function arrayDataOfDedicatedMentor(DedicatedMentor $dedicatedMentor): array
    {
        return [
            'id' => $dedicatedMentor->getId(),
            'modifiedTime' => $dedicatedMentor->getModifiedTimeString(),
            'cancelled' => $dedicatedMentor->isCancelled(),
            'consultant' => [
                'id' => $dedicatedMentor->getConsultant()->getId(),
                'personnel' => [
                    'id' => $dedicatedMentor->getConsultant()->getPersonnel()->getId(),
                    'name' => $dedicatedMentor->getConsultant()->getPersonnel()->getName(),
                ],
            ],
        ];
    }

    protected function buildViewService()
    {
        $teamMemberRepository = $this->em->getRepository(Member::class);
        $teamParticipantRepository = $this->em->getRepository(TeamProgramParticipation::class);
        $dedicatedMentorRepository = $this->em->getRepository(DedicatedMentor::class);
        return new ViewDedicatedMentor($teamMemberRepository, $teamParticipantRepository, $dedicatedMentorRepository);
    }

}
