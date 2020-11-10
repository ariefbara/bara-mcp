<?php

namespace Query\Application\Auth\Firm;

use Resources\Exception\RegularException;

class AuthorizeUserIsActiveParticipantInFirm
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
    
    public function execute(string $firmId, string $userId): void
    {
        if (!$this->participantRepository->containRecordOfParticipantInFirmCorrespondWithUser($firmId, $userId)) {
            $errorDetail = "forbidden: only active user participating in firm's program can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
