<?php

namespace Bara\Application\Service;

use Bara\Domain\Model\ {
    Admin,
    AdminData
};
use Resources\Exception\RegularException;

class AdminChangeProfile
{
    /**
     *
     * @var AdminRepository
     */
    protected $adminRepository;
    
    function __construct(AdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }
    
    public function execute(string $adminId, AdminData $adminData): Admin
    {
        $admin = $this->adminRepository->ofId($adminId);
        if (!$admin->emailEquals($adminData->getEmail())) {
            $this->assertEmailAvailable($adminData->getEmail());
        }
        
        $admin->updateProfile($adminData);
        $this->adminRepository->update();
        return $admin;
    }
    
    protected function assertEmailAvailable(string $email): void
    {
        if (!$this->adminRepository->isEmailAvailable($email)) {
            $errorDetail = "conflict: email already registered";
            throw RegularException::conflict($errorDetail);
        }
    }

}
