<?php

namespace Firm\Application\Service\Manager;

class AssignClientCVForm
{

    /**
     * 
     * @var ManagerRepository
     */
    protected $managerRepository;

    /**
     * 
     * @var ProfileFormRepository
     */
    protected $profileFormRepository;
    
    public function __construct(ManagerRepository $managerRepository, ProfileFormRepository $profileFormRepository)
    {
        $this->managerRepository = $managerRepository;
        $this->profileFormRepository = $profileFormRepository;
    }
    
    public function execute(string $firmId, string $managerId, string $profileFormId): string
    {
        $profileForm = $this->profileFormRepository->ofId($profileFormId);
        $id = $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->assignClientCVForm($profileForm);
        $this->managerRepository->update();
        return $id;
    }

}
