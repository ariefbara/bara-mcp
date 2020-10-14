<?php

namespace Query\Domain\Service;

use Query\Domain\Model\Firm\Program;
use Tests\TestBase;

class LearningMaterialFinderTest extends TestBase
{
    protected $learningMaterialRepository;
    protected $finder;
    protected $program, $programId = "programId";
    protected $learningMaterialId = "learningMaterialId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->learningMaterialRepository = $this->buildMockOfInterface(LearningMaterialRepository::class);
        $this->finder = new LearningMaterialFinder($this->learningMaterialRepository);
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->program->expects($this->any())->method("getId")->willReturn($this->programId);
    }
    
    public function test_execute_returnLearningMaterialRepositorySearchaLearningMaterialBelongsToProgramResult()
    {
        $this->learningMaterialRepository->expects($this->once())
                ->method("aLearningMaterialBelongsToProgram")
                ->with($this->programId, $this->learningMaterialId);
        $this->finder->execute($this->program, $this->learningMaterialId);
    }
}
