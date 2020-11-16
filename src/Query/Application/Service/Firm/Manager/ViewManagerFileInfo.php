<?php

namespace Query\Application\Service\Firm\Manager;

use Query\Domain\Model\Firm\Manager\ManagerFileInfo;

class ViewManagerFileInfo
{

    /**
     *
     * @var ManagerFileInfoRepository
     */
    protected $managerFileInfoRepository;

    function __construct(ManagerFileInfoRepository $managerFileInfoRepository)
    {
        $this->managerFileInfoRepository = $managerFileInfoRepository;
    }

    public function showById(string $firmId, string $managerId, string $managerFileInfoId): ManagerFileInfo
    {
        return $this->managerFileInfoRepository
                        ->aManagerFileInfoBelongsToManager($firmId, $managerId, $managerFileInfoId);
    }

}
