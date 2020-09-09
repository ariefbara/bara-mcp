<?php

namespace Personnel\Application\Service\Firm;

use Personnel\Domain\Model\Firm\ {
    Personnel,
    PersonnelProfileData
};
use Tests\TestBase;

class PersonnelUpdateProfileTest extends TestBase
{
    protected $service;
    protected $firmId = 'firmId';
    protected $personnelRepository, $personnel, $personnelId = 'personnelId';
    protected $personnelProfileData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->personnelRepository = $this->buildMockOfInterface(PersonnelRepository::class);
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->personnelRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->personnelId)
                ->willReturn($this->personnel);
        $this->service = new PersonnelUpdateProfile($this->personnelRepository);
        
        $this->personnelProfileData = $this->buildMockOfClass(PersonnelProfileData::class);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->personnelId, $this->personnelProfileData);
    }
    
    public function test_execute_updatePersonnelProfile()
    {
        $this->personnel->expects($this->once())
                ->method('updateProfile')
                ->with($this->personnelProfileData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->personnelRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
