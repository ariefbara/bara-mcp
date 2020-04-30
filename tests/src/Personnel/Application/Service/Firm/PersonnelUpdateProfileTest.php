<?php

namespace Personnel\Application\Service\Firm;

class PersonnelUpdateProfileTest extends \Tests\TestBase
{
    protected $service;
    protected $firmId = 'firmId';
    protected $personnelRepository, $personnel, $personnelId = 'personnelId';
    protected $name = 'new name', $phone = '081231231';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->personnelRepository = $this->buildMockOfInterface(PersonnelRepository::class);
        $this->personnel = $this->buildMockOfClass(\Personnel\Domain\Model\Firm\Personnel::class);
        $this->personnelRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->personnelId)
                ->willReturn($this->personnel);
        $this->service = new PersonnelUpdateProfile($this->personnelRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->personnelId, $this->name, $this->phone);
    }
    
    public function test_execute_updatePersonnelProfile()
    {
        $this->personnel->expects($this->once())
                ->method('updateProfile')
                ->with($this->name, $this->phone);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->personnelRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
