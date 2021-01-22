<?php

namespace Query\Application\Service\Firm\Client;

use Query\Domain\Model\Firm\Client\ClientBio;

class ViewClientBio
{

    /**
     * 
     * @var ClientBioRepository
     */
    protected $clientBioRepository;

    public function __construct(ClientBioRepository $clientBioRepository)
    {
        $this->clientBioRepository = $clientBioRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param string $page
     * @param string $pageSize
     * @return ClientBio[]
     */
    public function showAll(string $firmId, string $clientId, string $page, string $pageSize)
    {
        return $this->clientBioRepository->allBiosBelongsClient($firmId, $clientId, $page, $pageSize);
    }

    public function showById(string $firmId, string $clientId, string $bioFormId): ClientBio
    {
        return $this->clientBioRepository
                        ->aBioBelongsToClientCorrespondWithBioForm($firmId, $clientId, $bioFormId);
    }

}
