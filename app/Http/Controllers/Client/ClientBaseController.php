<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Query\ {
    Application\Auth\Firm\AuthorizeRequestFromActiveClient,
    Domain\Model\Firm\Client
};

class ClientBaseController extends Controller
{
    protected function clientId()
    {
        return $this->request->clientId;
    }
    protected function firmId()
    {
        return $this->request->firmId;
    }
    
    protected function authorizeRequestFromActiveClient()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $authZ = new AuthorizeRequestFromActiveClient($clientRepository);
        $authZ->execute($this->firmId(), $this->clientId());
    }
    
    protected function clientQueryRepository()
    {
        return $this->em->getRepository(Client::class);
    }
}
