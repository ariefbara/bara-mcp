<?php

namespace Client\Application\Service\Client;

use Client\Application\Service\ClientRepository;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class SubmitBio
{
    /**
     * 
     * @var ClientRepository
     */
    protected $clientRepository;
    
    /**
     * 
     * @var BioFormRepository
     */
    protected $bioFormRepository;
    
    public function __construct(ClientRepository $clientRepository, BioFormRepository $bioFormRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->bioFormRepository = $bioFormRepository;
    }
    
    public function execute(string $firmId, string $clientId, string $bioFormId, FormRecordData $formRecordData): void
    {
        $bioForm = $this->bioFormRepository->ofId($bioFormId);
        $this->clientRepository->ofId($firmId, $clientId)->submitBio($bioForm, $formRecordData);
        $this->clientRepository->update();
    }


}
