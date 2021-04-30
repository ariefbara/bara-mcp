<?php

namespace Query\Application\Service\Firm\Client\AsTeamMember\AsProgramParticipant;

use Query\Application\Service\TeamMember\TeamMemberRepository;
use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Service\Firm\Program\MentorRepository;

class ViewMentor
{

    /**
     * 
     * @var TeamMemberRepository
     */
    protected $teamMemberRepository;

    /**
     * 
     * @var TeamProgramParticipationRepository
     */
    protected $teamParticipantRepository;

    /**
     * 
     * @var MentorRepository
     */
    protected $mentorRepository;

    public function __construct(
            TeamMemberRepository $teamMemberRepository, TeamProgramParticipationRepository $teamParticipantRepository,
            MentorRepository $mentorRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->teamParticipantRepository = $teamParticipantRepository;
        $this->mentorRepository = $mentorRepository;
    }
    
    public function showAll(
            string $firmId, string $clientId, string $teamId, string $programId, int $page, int $pageSize)
    {
        $teamParticipant = $this->teamParticipantRepository->aTeamProgramParticipationCorrespondWithProgram($teamId, $programId);
        return $this->teamMemberRepository->aTeamMemberOfClientCorrespondWithTeam($firmId, $clientId, $teamId)
                ->viewAllMentors($teamParticipant, $this->mentorRepository, $page, $pageSize);
    }
    
    public function showById(string $firmId, string $clientId, string $teamId, string $programId, string $mentorId): Consultant
    {
        $teamParticipant = $this->teamParticipantRepository->aTeamProgramParticipationCorrespondWithProgram($teamId, $programId);
        return $this->teamMemberRepository->aTeamMemberOfClientCorrespondWithTeam($firmId, $clientId, $teamId)
                ->viewMentor($teamParticipant, $this->mentorRepository, $mentorId);
    }

}
