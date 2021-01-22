<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Shared\FormData;

class CreateBioForm
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

    public function execute(string $firmId, string $managerId, FormData $formData): string
    {
        $id = $this->bioFormRepository->nextIdentity();
        $bioForm = $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->createBioForm($id, $formData);
        $this->bioFormRepository->add($bioForm);
        return $id;
    }

}
