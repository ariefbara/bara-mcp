<?php

namespace Query\Application\Service\Firm;

use Query\Domain\Model\Firm\Manager;

class ManagerView
{

    /**
     *
     * @var ManagerRepository
     */
    protected $managerRepository;

    function __construct(ManagerRepository $managerRepository)
    {
        $this->managerRepository = $managerRepository;
    }

    public function showById(string $firmId, string $managerId): Manager
    {
        return $this->managerRepository->ofId($firmId, $managerId);
    }

    /**
     * 
     * @param string $firmId
     * @param int $page
     * @param int $pageSize
     * @return Manager[]
     */
    public function showAll(string $firmId, int $page, int $pageSize)
    {
        return $this->managerRepository->all($firmId, $page, $pageSize);
    }

}
