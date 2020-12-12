<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Shared\FormData;

class CreateProfileForm
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
    
    public function execute(string $firmId, string $managerId, FormData $formData): string
    {
        $id = $this->profileFormRepository->nextIdentity();
        $profileForm = $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->createProfileForm($id, $formData);
        $this->profileFormRepository->add($profileForm);
        return $id;
    }


}
