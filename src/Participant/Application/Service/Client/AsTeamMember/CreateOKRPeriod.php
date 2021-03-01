<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Participant\Application\Service\Participant\OKRPeriodRepository;
use Participant\Domain\Model\Participant\OKRPeriodData;

class CreateOKRPeriod
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
     * @var OKRPeriodRepository
     */
    protected $okrPeriodRepository;

    public function __construct(
            TeamMemberRepository $teamMemberRepository, TeamParticipantRepository $teamParticipantRepository,
            OKRPeriodRepository $okrPeriodRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->teamParticipantRepository = $teamParticipantRepository;
        $this->okrPeriodRepository = $okrPeriodRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamId, string $participantId, OKRPeriodData $okrPeriodData): string
    {
        $teamParticipant = $this->teamParticipantRepository->ofId($participantId);
        $id = $this->okrPeriodRepository->nextIdentity();
        $okrPeriod = $this->teamMemberRepository->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamId)
                ->createOKRPeriodInTeamParticipant($teamParticipant, $id, $okrPeriodData);
        $this->okrPeriodRepository->add($okrPeriod);
        return $id;
    }

}
