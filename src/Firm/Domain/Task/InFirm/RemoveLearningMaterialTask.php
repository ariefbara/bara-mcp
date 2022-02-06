<?php

namespace Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\FirmTaskExecutableByManager;
use Firm\Domain\Task\Dependency\Firm\Program\Mission\LearningMaterialRepository;

class RemoveLearningMaterialTask implements FirmTaskExecutableByManager
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

    public function __construct(LearningMaterialRepository $learningMaterialRepository, string $id)
    {
        $this->learningMaterialRepository = $learningMaterialRepository;
        $this->id = $id;
    }

    public function executeInFirm(Firm $firm): void
    {
        $learningMaterial = $this->learningMaterialRepository->aLearningMaterialOfId($this->id);
        $learningMaterial->assertAccessibleInFirm($firm);
        $learningMaterial->remove();
    }

}
