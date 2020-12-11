<?php

namespace Query\Application\Auth;

use Resources\Exception\RegularException;

class AuthorizeTeamIsUnconcludedProgramRegistrant
{

    /**
     * 
     * @var TeamRegistrantRepository
     */
    protected $teamRegistrantRepository;

    function __construct(TeamRegistrantRepository $teamRegistrantRepository)
    {
        $this->teamRegistrantRepository = $teamRegistrantRepository;
    }

    public function execute(string $firmId, string $teamId, string $programId): void
    {
        if (!$this->teamRegistrantRepository
                        ->containRecordOfUnconcludedRegistrationToProgram($firmId, $teamId, $programId)
        ) {
            $errorDetail = "forbidden: only unconcluded program registrant can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
