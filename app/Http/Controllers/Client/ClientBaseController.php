<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Query\Application\Auth\Firm\AuthorizeRequestFromActiveClient;
use Query\Application\Service\Client\ExecuteTask;
use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\ITaskExecutableByClient;

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

    protected function executeQueryTask(ITaskExecutableByClient $task): void
    {
        $clientRepository = $this->em->getRepository(Client::class);
        (new ExecuteTask($clientRepository))
                ->execute($this->firmId(), $this->clientId(), $task);
    }
    
    protected function executeClientTask(\Client\Domain\Model\IClientTask $task, $payload): void
    {
        $clientRepository = $this->em->getRepository(\Client\Domain\Model\Client::class);
        (new \Client\Application\Service\ExecuteTask($clientRepository))->execute($this->clientId(), $task, $payload);
    }

}
