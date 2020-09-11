<?php

namespace Query\Application\Auth\Firm\Program;

use Resources\Exception\RegularException;

class UserParticipantAuthorization
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

    public function execute(string $firmId, string $programId, string $userId)
    {
        if (!$this->participantRepository
                        ->containRecordOfActiveParticipantCorrespondWithUser($firmId, $programId, $userId)) {
            $errorDetail = "forbidden: only active program user participant can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
