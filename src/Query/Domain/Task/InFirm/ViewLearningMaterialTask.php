<?php

namespace Query\Domain\Task\InFirm;

use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\ITaskInFirmExecutableByManager;
use Query\Domain\Model\Firm\Program\Mission\LearningMaterial;
use Query\Domain\Task\Dependency\Firm\Program\Mission\LearningMaterialRepository;

class ViewLearningMaterialTask implements ITaskInFirmExecutableByManager
{

    /**
     * 
     * @var LearningMaterialRepository
     */
    protected $learningMaterialRepository;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var LearningMaterial
     */
    public $result;

    public function __construct(LearningMaterialRepository $learningMaterialRepository, string $id)
    {
        $this->learningMaterialRepository = $learningMaterialRepository;
        $this->id = $id;
    }

    public function executeTaskInFirm(Firm $firm): void
    {
        $this->result = $this->learningMaterialRepository->aLearningMaterialInFirm($firm->getId(), $this->id);
    }

}
