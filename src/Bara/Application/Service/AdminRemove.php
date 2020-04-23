<?php

namespace Bara\Application\Service;

class AdminRemove
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
    
    public function execute(string $adminId): void
    {
        $this->adminRepository->ofId($adminId)->remove();
        $this->adminRepository->update();
    }

}
