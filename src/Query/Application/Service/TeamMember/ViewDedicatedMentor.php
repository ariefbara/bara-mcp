<?php

namespace Query\Application\Service\TeamMember;

use Query\Domain\Model\Firm\Program\DedicatedMentorRepository;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;

class ViewDedicatedMentor
{

    /**
     * 
     * @var TeamMemberRepository
     */
    protected $teamMemberRepository;

    /**
     * 
     * @var TeamParticipantRepository
     */
    protected $teamParticipantRepository;

    /**
     * 
     * @var DedicatedMentorRepository
     */
    protected $dedicatedMentorRepository;

    public function __construct(TeamMemberRepository $teamMemberRepository,
            TeamParticipantRepository $teamParticipantRepository, DedicatedMentorRepository $dedicatedMentorRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->teamParticipantRepository = $teamParticipantRepository;
        $this->dedicatedMentorRepository = $dedicatedMentorRepository;
    }

    public function showById(
            string $firmId, string $clientId, string $teamId, string $teamParticipantId, string $dedicatedMentorId): DedicatedMentor
    {
        $teamParticipant = $this->teamParticipantRepository->ofId($teamParticipantId);
        return $this->teamMemberRepository->aTeamMemberOfClientCorrespondWithTeam($firmId, $clientId, $teamId)
                        ->viewDedicatedMentor($teamParticipant, $this->dedicatedMentorRepository, $dedicatedMentorId);
    }

    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param string $teamId
     * @param string $teamParticipantId
     * @param int $page
     * @param int $pageSize
     * @param bool|null $cancelledStatus
     * @return DedicatedMentor[]
     */
    public function showAll(
            string $firmId, string $clientId, string $teamId, string $teamParticipantId, int $page, int $pageSize,
            ?bool $cancelledStatus)
    {
        $teamParticipant = $this->teamParticipantRepository->ofId($teamParticipantId);
        return $this->teamMemberRepository->aTeamMemberOfClientCorrespondWithTeam($firmId, $clientId, $teamId)
                        ->viewAllDedicatedMentor(
                                $teamParticipant, $this->dedicatedMentorRepository, $page, $pageSize, $cancelledStatus);
    }

}
