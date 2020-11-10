<?php

namespace Query\Application\Auth\Firm;

use Resources\Exception\RegularException;

class AuthorizeRequestFromActiveClient
{
    /**
     *
     * @var ClientRepository
     */
    protected $clientRepository;
    
    function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }
    
    public function execute(string $firmId, string $clientId): void
    {
        if (!$this->clientRepository->containRecordOfActiveClientInFirm($firmId, $clientId)) {
            $errorDetail = "forbidden: only active client can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
