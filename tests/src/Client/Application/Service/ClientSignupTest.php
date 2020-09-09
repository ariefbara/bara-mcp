<?php

namespace Client\Application\Service;

use Client\Domain\Model\ClientData;
use Query\Domain\Model\Firm;
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class ClientSignupTest extends TestBase
{
    protected $service;
    protected $clientRepository, $nextId = 'nextId';
    protected $firmRepository, $firm;
    protected $dispatcher;


    protected $firmIdentifier = 'firm-identifier', $clientData, $clientEmail = 'covid@hadipranoto.com';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->clientRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);
        
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->firm->expects($this->any())->method('getId')->willReturn('firmId');
        $this->firmRepository = $this->buildMockOfInterface(FirmRepository::class);
        $this->firmRepository->expects($this->any())
                ->method('ofIdentifier')
                ->with($this->firmIdentifier)
                ->willReturn($this->firm);
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new ClientSignup($this->clientRepository, $this->firmRepository, $this->dispatcher);
        
        $this->clientData = $this->buildMockOfClass(ClientData::class);
        $this->clientData->expects($this->any())->method('getEmail')->willReturn($this->clientEmail);
        $this->clientData->expects($this->any())->method('getFirstName')->willReturn('firsname');
        $this->clientData->expects($this->any())->method('getPassword')->willReturn('password213');
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmIdentifier, $this->clientData);
    }
    
    public function test_execute_addClientCreatedInFirmToRepository()
    {
        $this->clientRepository->expects($this->once())
                ->method('add');
        $this->execute();
    }
    public function test_execute_repositoryContainRecordOfSameEmail_conflictError()
    {
        $this->clientRepository->expects($this->once())
                ->method('containRecordWithEmail')
                ->with($this->firmIdentifier, $this->clientEmail)
                ->willReturn(true);
        $operation = function (){
            $this->execute();
        };
        $errorDetail = 'conflict: email already registered';
        $this->assertRegularExceptionThrowed($operation, 'Conflict', $errorDetail);
    }
    public function test_execute_dispatchDispatcher()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch');
        $this->execute();
    }
}
