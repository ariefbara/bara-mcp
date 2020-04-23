<?php

namespace Bara\Application\Service;

use Bara\Domain\Model\ {
    Admin,
    AdminData
};
use Resources\Exception\RegularException;

class AdminAdd
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

    public function execute(AdminData $adminData, string $password): Admin
    {
        $this->assertEmailAvailable($adminData->getEmail());
        
        $id = $this->adminRepository->nextIdentity();
        $admin = new Admin($id, $adminData, $password);
        $this->adminRepository->add($admin);
        
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
