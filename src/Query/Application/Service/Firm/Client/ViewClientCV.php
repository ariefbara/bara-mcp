<?php

namespace Query\Application\Service\Firm\Client;

use Query\Domain\Model\Firm\Client\ClientCV;

class ViewClientCV
{

    /**
     * 
     * @var ClientCVRepository
     */
    protected $clientCVRepository;

    public function __construct(ClientCVRepository $clientCVRepository)
    {
        $this->clientCVRepository = $clientCVRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param string $page
     * @param string $pageSize
     * @return ClientCV[]
     */
    public function showAll(string $firmId, string $clientId, string $page, string $pageSize)
    {
        return $this->clientCVRepository->allClientCVsBelongsClient($firmId, $clientId, $page, $pageSize);
    }

    public function showById(string $firmId, string $clientId, string $clientCVFormId): ClientCV
    {
        return $this->clientCVRepository
                        ->aClientCVBelongsClientCorrespondWithClientCVForm($firmId, $clientId, $clientCVFormId);
    }

}
