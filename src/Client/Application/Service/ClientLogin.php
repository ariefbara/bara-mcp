<?php

namespace Client\Application\Service;

use Client\Domain\Model\Client;
use Resources\Exception\RegularException;

class ClientLogin
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
    
    public function execute($email, $password): Client
    {
        $errorDetail = 'unauthorized: invalid email or password';
        try {
            $client =  $this->clientRepository->ofEmail($email);
        } catch (RegularException $ex) {
            throw RegularException::unauthorized($errorDetail);
        }
        
        if (!$client->passwordMatch($password)) {
            throw RegularException::unauthorized($errorDetail);
        }
        if (!$client->isActivated()) {
            $errorDetail = 'unauthorized: account not activated';
            throw RegularException::unauthorized($errorDetail);
        }
        
        return $client;
    }

}
