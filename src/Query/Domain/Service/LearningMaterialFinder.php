<?php

namespace Query\Domain\Service;

use Query\Domain\Model\Firm\ {
    Program,
    Program\Mission\LearningMaterial
};

class LearningMaterialFinder
{

    /**
     *
     * @var LearningMaterialRepository
     */
    protected $learningMaterialRepository;

    public function __construct(LearningMaterialRepository $learningMaterialRepository)
    {
        $this->learningMaterialRepository = $learningMaterialRepository;
    }

    public function execute(Program $program, string $learningMaterialId): LearningMaterial
    {
        return $this->learningMaterialRepository
                        ->aLearningMaterialBelongsToProgram($program->getId(), $learningMaterialId);
    }

}
