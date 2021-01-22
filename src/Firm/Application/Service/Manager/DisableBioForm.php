<?php

namespace Firm\Application\Service\Manager;

class DisableBioForm
{

    /**
     * 
     * @var ManagerRepository
     */
    protected $managerRepository;

    /**
     * 
     * @var BioFormRepository
     */
    protected $bioFormRepository;

    public function __construct(ManagerRepository $managerRepository, BioFormRepository $bioFormRepository)
    {
        $this->managerRepository = $managerRepository;
        $this->bioFormRepository = $bioFormRepository;
    }

    public function execute(string $firmId, string $managerId, string $bioFormId): void
    {
        $bioForm = $this->bioFormRepository->ofId($bioFormId);
        $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->disableBioForm($bioForm);
        $this->bioFormRepository->update();
    }

}
