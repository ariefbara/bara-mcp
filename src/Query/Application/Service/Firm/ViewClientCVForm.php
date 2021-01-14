<?php

namespace Query\Application\Service\Firm;

use Query\Domain\Model\Firm\ClientCVForm;

class ViewClientCVForm
{

    /**
     * 
     * @var ClientCVFormRepository
     */
    protected $clientCVFormRepository;

    public function __construct(ClientCVFormRepository $clientCVFormRepository)
    {
        $this->clientCVFormRepository = $clientCVFormRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param int $page
     * @param int $pageSize
     * @param bool|null $disableStatus
     * @return ClientCVForm[]
     */
    public function showAll(string $firmId, int $page, int $pageSize, ?bool $disableStatus)
    {
        return $this->clientCVFormRepository->allClientCVFormsInFirm($firmId, $page, $pageSize, $disableStatus);
    }

    public function showById(string $firmId, string $clientCVFormId): ClientCVForm
    {
        return $this->clientCVFormRepository->aClientCVFormInFirm($firmId, $clientCVFormId);
    }

}
