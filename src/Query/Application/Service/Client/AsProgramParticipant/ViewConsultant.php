<?php

namespace Query\Application\Service\Client\AsProgramParticipant;

use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Service\Firm\Program\MentorRepository;

class ViewConsultant
{

    /**
     * 
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

    /**
     * 
     * @var MentorRepository
     */
    protected $mentorRepository;

    public function __construct(ClientParticipantRepository $clientParticipantRepository,
            MentorRepository $mentorRepository)
    {
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->mentorRepository = $mentorRepository;
    }

    public function showById(string $firmId, string $clientId, string $programId, string $mentorId): Consultant
    {
        return $this->clientParticipantRepository
                        ->aClientParticipantCorrespondWithProgram($firmId, $clientId, $programId)
                        ->viewMentor($this->mentorRepository, $mentorId);
    }

    public function showAll(string $firmId, string $clientId, string $programId, int $page, int $pageSize)
    {
        return $this->clientParticipantRepository
                        ->aClientParticipantCorrespondWithProgram($firmId, $clientId, $programId)
                        ->viewAllMentors($this->mentorRepository, $page, $pageSize);
    }

}
