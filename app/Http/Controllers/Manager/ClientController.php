<?php

namespace App\Http\Controllers\Manager;

use Firm\Domain\Model\Firm\Client as Client2;
use Firm\Domain\Model\Firm\ClientRegistrationData;
use Firm\Domain\Task\InFirm\ActivateClientTask;
use Firm\Domain\Task\InFirm\AddClientTask;
use Query\Domain\Model\Firm\Client;
use Query\Domain\Task\Dependency\Firm\ClientFilter;
use Query\Domain\Task\InFirm\ShowAllClientTask;
use Query\Domain\Task\InFirm\ShowClientTask;

class ClientController extends ManagerBaseController
{
    
    public function add()
    {
        $clientRepository = $this->em->getRepository(Client2::class);
        
        $firstName = $this->stripTagsInputRequest('firstName');
        $lastName = $this->stripTagsInputRequest('lastName');
        $email = $this->stripTagsInputRequest('email');
        $password = $this->stripTagsInputRequest('password');
        $payload = new ClientRegistrationData($firstName, $lastName, $email, $password);
        
        $task = new AddClientTask($clientRepository, $payload);
        $this->executeFirmTaskExecutableByManager($task);
        
        $client = $this->buildAndExecuteShowClientTask($task->addedClientId)->result;
        return $this->commandCreatedResponse($this->arrayDataOfClient($client));
    }
    
    public function activate($id)
    {
        $clientRepository = $this->em->getRepository(Client2::class);
        $task = new ActivateClientTask($clientRepository, $id);
        $this->executeFirmTaskExecutableByManager($task);
        
        return $this->show($id);
    }
    
    public function showAll()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $payload = (new ClientFilter())
                ->setPage($this->getPage())
                ->setPageSize($this->getPageSize())
                ->setName($this->stripTagQueryRequest('name'))
                ->setEmail($this->stripTagQueryRequest('email'))
                ->setActivatedStatus($this->filterBooleanOfQueryRequest('activatedStatus'));
        
        $task = new ShowAllClientTask($clientRepository, $payload);
        $this->executeFirmQueryTask($task);
        
        $result = [];
        $result["total"] = count($task->results);
        foreach ($task->results as $client) {
            $result["list"][] = $this->arrayDataOfClient($client);
        }
        return $this->listQueryResponse($result);
    }
    
    public function show($id)
    {
        $client = $this->buildAndExecuteShowClientTask($id)->result;
        return $this->singleQueryResponse($this->arrayDataOfClient($client));
    }
    
    protected function arrayDataOfClient(Client $client): array
    {
        return [
            "id" => $client->getId(),
            "name" => $client->getFullName(),
            "email" => $client->getEmail(),
            "signupTime" => $client->getSignupTimeString(),
            "activated" => $client->isActivated(),
        ];
    }
    
    protected function buildAndExecuteShowClientTask(string $id)
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $task = new ShowClientTask($clientRepository, $id);
        $this->executeFirmQueryTask($task);
        return $task;
    }
}
