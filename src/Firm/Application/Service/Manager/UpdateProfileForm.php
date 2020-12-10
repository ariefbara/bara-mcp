<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Shared\FormData;

class UpdateProfileForm
{

    /**
     * 
     * @var ProfileFormRepository
     */
    protected $profileFormRepository;

    /**
     * 
     * @var ManagerRepository
     */
    protected $managerRepository;

    function __construct(ProfileFormRepository $profileFormRepository, ManagerRepository $managerRepository)
    {
        $this->profileFormRepository = $profileFormRepository;
        $this->managerRepository = $managerRepository;
    }
    
    public function execute(string $firmId, string $managerId, string $profileFormId, FormData $formData): void
    {
        $profileForm = $this->profileFormRepository->ofId($profileFormId);
        $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->updateProfileForm($profileForm, $formData);
        $this->profileFormRepository->update();
    }

}
