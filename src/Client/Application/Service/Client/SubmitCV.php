<?php

namespace Client\Application\Service\Client;

use Client\Application\Service\ClientRepository;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class SubmitCV
{
    /**
     * 
     * @var ClientRepository
     */
    protected $clientRepository;
    
    /**
     * 
     * @var ClientCVFormRepository
     */
    protected $clientCVFormRepository;
    
    public function __construct(ClientRepository $clientRepository, ClientCVFormRepository $clientCVFormRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->clientCVFormRepository = $clientCVFormRepository;
    }
    
    public function execute(string $firmId, string $clientId, string $clientCVFormId, FormRecordData $formRecordData): void
    {
        $clientCVForm = $this->clientCVFormRepository->ofId($clientCVFormId);
        $this->clientRepository->ofId($firmId, $clientId)->submitCV($clientCVForm, $formRecordData);
        $this->clientRepository->update();
    }


}
