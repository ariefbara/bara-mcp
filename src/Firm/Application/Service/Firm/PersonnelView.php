<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\Firm\Personnel;

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
    public function showAll(string $firmId, int $page, int $pageSize)
    {
        return $this->personnelRepository->all($firmId, $page, $pageSize);
    }

}
