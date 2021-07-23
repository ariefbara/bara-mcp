<?php

namespace Query\Application\Service\Personnel;

use Query\Domain\Model\Firm\IViewAssetBelongsToPersonnelTask;

class ExecuteTask
{

    /**
     * 
     * @var PersonnelRepository
     */
    protected $personnelRepository;

    /**
     * 
     * @var IViewAssetBelongsToPersonnelTask
     */
    protected $task;
    
    public function __construct(PersonnelRepository $personnelRepository, IViewAssetBelongsToPersonnelTask $task)
    {
        $this->personnelRepository = $personnelRepository;
        $this->task = $task;
    }
    
    public function execute(string $firmId, string $personnelId): array
    {
        return $this->personnelRepository->aPersonnelInFirm($firmId, $personnelId)
                ->viewOwnedAsset($this->task);
    }


}
