<?php

namespace Query\Application\Service\Firm;

use Query\Domain\Model\Firm\Personnel;

class PersonnelView
{

    /**
     *
     * @var PersonnelRepository 
     */
    protected $personnelRepository;

    function __construct(PersonnelRepository $personnelRepository)
    {
        $this->personnelRepository = $personnelRepository;
    }

    public function showById(string $firmId, string $personnelId): Personnel
    {
        return $this->personnelRepository->ofId($firmId, $personnelId);
    }

    /**
     * 
     * @param string $firmId
     * @param int $page
     * @param int $pageSize
     * @return Personnel[]
     */
    public function showAll(string $firmId, int $page, int $pageSize, ?bool $activeStatus)
    {
        return $this->personnelRepository->all($firmId, $page, $pageSize, $activeStatus);
    }

}
