<?php

namespace Query\Domain\Task\InFirm;

use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\ITaskInFirmExecutableByManager;
use Query\Domain\Model\Firm\Program\Mission\LearningMaterial;
use Query\Domain\Task\Dependency\Firm\Program\Mission\LearningMaterialFilter;
use Query\Domain\Task\Dependency\Firm\Program\Mission\LearningMaterialRepository;

class ViewAllLearningMaterialTask implements ITaskInFirmExecutableByManager
{

    /**
     * 
     * @var LearningMaterialRepository
     */
    protected $learningMaterialRepository;

    /**
     * 
     * @var LearningMaterialFilter
     */
    protected $filter;

    /**
     * 
     * @var LearningMaterial[]
     */
    public $result;

    public function __construct(LearningMaterialRepository $learningMaterialRepository, LearningMaterialFilter $filter)
    {
        $this->learningMaterialRepository = $learningMaterialRepository;
        $this->filter = $filter;
    }

    public function executeTaskInFirm(Firm $firm): void
    {
        $this->result = $this->learningMaterialRepository->allLearningMaterialInFirm($firm->getId(), $this->filter);
    }

}
