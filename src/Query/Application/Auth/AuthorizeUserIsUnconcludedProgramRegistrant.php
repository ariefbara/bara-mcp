<?php

namespace Query\Application\Auth;

use Resources\Exception\RegularException;

class AuthorizeUserIsUnconcludedProgramRegistrant
{

    /**
     * 
     * @var UserRegistrantRepository
     */
    protected $userRegistrantRepository;

    function __construct(UserRegistrantRepository $userRegistrantRepository)
    {
        $this->userRegistrantRepository = $userRegistrantRepository;
    }

    public function execute(string $userId, string $firmId, string $programId): void
    {
        if (!$this->userRegistrantRepository
                        ->containRecordOfUnconcludedRegistrationToProgram($userId, $firmId, $programId)
        ) {
            $errorDetail = "forbidden: only unconcluded program regsitrant can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
