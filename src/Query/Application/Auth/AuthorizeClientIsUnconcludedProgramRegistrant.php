<?php

namespace Query\Application\Auth;

use Resources\Exception\RegularException;

class AuthorizeClientIsUnconcludedProgramRegistrant
{

    /**
     * 
     * @var ClientRegistrantRepository
     */
    protected $clientRegistrantRepository;

    function __construct(ClientRegistrantRepository $clientRegistrantRepository)
    {
        $this->clientRegistrantRepository = $clientRegistrantRepository;
    }

    public function execute(string $firmId, string $clientId, string $programId): void
    {
        if (!$this->clientRegistrantRepository
                        ->containRecordOfUnconcludedRegistrationToProgram($firmId, $clientId, $programId)
        ) {
            $errorDetail = "forbidden: only unconcluded registrant can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
