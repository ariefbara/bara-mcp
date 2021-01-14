<?php

namespace Firm\Application\Service\Manager;

class DisableClientCVForm
{

    /**
     * 
     * @var ClientCVFormRepository
     */
    protected $clientCVFormRepository;

    /**
     * 
     * @var ManagerRepository
     */
    protected $managerRepository;
    
    public function __construct(ClientCVFormRepository $clientCVFormRepository, ManagerRepository $managerRepository)
    {
        $this->clientCVFormRepository = $clientCVFormRepository;
        $this->managerRepository = $managerRepository;
    }
    
    public function execute(string $firmId, string $managerId, string $clientCVFormId): void
    {
        $clientCVForm = $this->clientCVFormRepository->ofId($clientCVFormId);
        $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->disableClientCVForm($clientCVForm);
        $this->clientCVFormRepository->update();
    }

}
