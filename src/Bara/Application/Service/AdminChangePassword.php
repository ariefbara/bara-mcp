<?php

namespace Bara\Application\Service;

class AdminChangePassword
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
    
    public function execute(string $adminId, string $previousPassword, string $newPassword): void
    {
        $this->adminRepository->ofId($adminId)->changePassword($previousPassword, $newPassword);
        $this->adminRepository->update();
    }

}
