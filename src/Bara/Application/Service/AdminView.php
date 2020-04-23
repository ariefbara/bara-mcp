<?php

namespace Bara\Application\Service;

use Bara\Domain\Model\Admin;

class AdminView
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
    
    public function showById(string $adminId): Admin
    {
        return $this->adminRepository->ofId($adminId);
    }
    
    /**
     * 
     * @param int $page
     * @param int $pageSize
     * @return Admin[]
     */
    public function showAll(int $page, int $pageSize)
    {
        return $this->adminRepository->all($page, $pageSize);
    }

}
