<?php

namespace Query\Application\Service\Firm;

use Query\Domain\Model\Firm\Client;
use Resources\Exception\RegularException;

class ClientLogin
{
    /**
     *
     * @var ClientRepository
     */
    protected $clientRepository;
    
    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }
    
    public function execute(string $firmIdentifier, string $email, string $password): Client
    {
        $errorDetail = 'unauthorized: invalid email or password';
        
        try {
            $client = $this->clientRepository->ofEmail($firmIdentifier, $email);
        } catch (RegularException $ex) {
            throw RegularException::unauthorized($errorDetail);
        }
        
        if (!$client->isActivated() || !$client->passwordMatch($password)) {
            throw RegularException::unauthorized($errorDetail);
        }
        
        return $client;
    }

}
