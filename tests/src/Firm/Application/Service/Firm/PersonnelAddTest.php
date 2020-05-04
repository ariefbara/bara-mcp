<?php

namespace Firm\Application\Service\Firm;

use Firm\ {
    Application\Service\FirmRepository,
    Domain\Model\Firm,
    Domain\Model\Firm\PersonnelData
};
use Tests\TestBase;

class PersonnelAddTest extends TestBase
{

    protected $service;
    protected $personnelRepository;
    protected $firmRepository, $firm, $firmId = 'firm-id';
    protected $personnelData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->personnelRepository = $this->buildMockOfInterface(PersonnelRepository::class);
        $this->firmRepository = $this->buildMockOfInterface(FirmRepository::class);
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->firmRepository->expects($this->any())
            ->method('ofId')
            ->with($this->firmId)
            ->willReturn($this->firm);

        $this->service = new PersonnelAdd($this->personnelRepository, $this->firmRepository);

        $this->personnelData = $this->buildMockOfClass(PersonnelData::class);
        $this->personnelData->expects($this->any())
            ->method('getName')
            ->willReturn('name');
        $this->personnelData->expects($this->any())
            ->method('getEmail')
            ->willReturn('personnel@email.org');
        $this->personnelData->expects($this->any())
            ->method('getPassword')
            ->willReturn('password123');
    }
    
    protected function execute()
    {
        $this->personnelRepository->expects($this->any())
            ->method('isEmailAvailable')
            ->willReturn(true);
        return $this->service->execute($this->firmId, $this->personnelData);
    }
    public function test_execute_addPersonnelToRepository()
    {
        $this->personnelRepository->expects($this->once())
            ->method('add');
        $this->execute();
    }
    public function test_execute_emailUnavailable_throwEx()
    {
        $this->personnelRepository->expects($this->once())
            ->method('isEmailAvailable')
            ->willReturn(false);
        $operation  = function (){
            return $this->service->execute($this->firmId, $this->personnelData);
        };
        $errorDetail = "conflict: email already registered";
        $this->assertRegularExceptionThrowed($operation, "Conflict", $errorDetail);
        
    }
    public function test_execute_returnNewId()
    {
        $this->personnelRepository->expects($this->once())
                ->method('nextIdentity')
                ->willReturn($id = 'id');
        $this->assertEquals($id, $this->execute());
    }

}
