<?php

namespace Query\Application\Auth\Firm\Program;

use Resources\Exception\RegularException;

class ParticipantAuthorization
{

    /**
     *
     * @var ParticipantRepository
     */
    protected $participantRepository;

    function __construct(ParticipantRepository $participantRepository)
    {
        $this->participantRepository = $participantRepository;
    }

    public function execute(string $firmId, string $programId, string $clientId): void
    {
        if (!$this->participantRepository->containRecordOfActiveParticipantCorrespondWithClient(
                        $firmId, $programId, $clientId)) {
            $errorDetail = 'forbidden: only active program participant allow to make this request';
            throw RegularException::forbidden($errorDetail);
        }
    }

}
