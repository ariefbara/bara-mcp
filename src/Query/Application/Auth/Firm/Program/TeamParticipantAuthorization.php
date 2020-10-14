<?php

namespace Query\Application\Auth\Firm\Program;

use Resources\Exception\RegularException;

class TeamParticipantAuthorization
{

    /**
     *
     * @var ParticipantRepository
     */
    protected $participantRepository;

    public function __construct(ParticipantRepository $participantRepository)
    {
        $this->participantRepository = $participantRepository;
    }
    
    public function execute(string $teamId, string $programId): void
    {
        if (!$this->participantRepository->containRecordOfActiveParticipantCorrespondWithTeam($teamId, $programId)) {
            $errorDetail = "forbidden: only active program participant can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
