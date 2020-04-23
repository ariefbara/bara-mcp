<?php

namespace Firm\Application\Service\Firm\Program\Mission;

use Firm\Domain\Model\Firm\Program\Mission\LearningMaterial;
use Tests\TestBase;

class LearningMaterialUpdateTest extends TestBase
{
    protected $missionCompositionId;
    protected $learningMaterialRepository, $learningMaterial, $learningMaterialId = 'learningMaterialId';
    protected $service;
    protected $name = 'new name', $content = 'string represent content';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->missionCompositionId = $this->buildMockOfClass(MissionCompositionId::class);
        $this->learningMaterial = $this->buildMockOfClass(LearningMaterial::class);
        $this->learningMaterialRepository = $this->buildMockOfInterface(LearningMaterialRepository::class);
        $this->learningMaterialRepository->expects($this->any())
                ->method('ofId')
                ->with($this->missionCompositionId, $this->learningMaterialId)
                ->willReturn($this->learningMaterial);
        $this->service = new LearningMaterialUpdate($this->learningMaterialRepository);
    }
    protected function execute()
    {
        $this->service->execute($this->missionCompositionId, $this->learningMaterialId, $this->name, $this->content);
    }
    public function test_execute_updateLearningMaterial()
    {
        $this->learningMaterial->expects($this->once())
                ->method('update')
                ->with($this->name, $this->content);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->learningMaterialRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
