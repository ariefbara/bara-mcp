<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Shared\FormData;

class UpdateBioForm
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
    
    public function execute(string $firmId, string $managerId, string $bioFormId, FormData $formData): void
    {
        $bioForm = $this->bioFormRepository->ofId($bioFormId);
        $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->updateBioForm($bioForm, $formData);
        $this->bioFormRepository->update();
    }


}
