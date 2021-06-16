<?php

namespace Query\Application\Service\Client;

use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\BioSearchFilter;

class ViewFirm
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
    
    public function show(string $firmId, string $clientId): Firm
    {
        return $this->clientRepository->aClientInFirm($firmId, $clientId)
                ->viewFirm();
    }
    
    public function showBioSearchFilter(string $firmId, string $clientId): ?BioSearchFilter
    {
        return $this->clientRepository->aClientInFirm($firmId, $clientId)
                ->viewBioSearchFilter();
    }

}
