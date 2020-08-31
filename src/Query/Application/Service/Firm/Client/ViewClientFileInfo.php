<?php

namespace Query\Application\Service\Firm\Client;

use Query\Domain\Model\Firm\Client\ClientFileInfo;

class ViewClientFileInfo
{

    /**
     *
     * @var ClientFileInfoRepository
     */
    protected $clientFileInfoRepository;

    public function __construct(ClientFileInfoRepository $clientFileInfoRepository)
    {
        $this->clientFileInfoRepository = $clientFileInfoRepository;
    }

    public function showById(string $firmId, string $clientId, string $clientFileInfoId): ClientFileInfo
    {
        return $this->clientFileInfoRepository->ofId($firmId, $clientId, $clientFileInfoId);
    }

}
