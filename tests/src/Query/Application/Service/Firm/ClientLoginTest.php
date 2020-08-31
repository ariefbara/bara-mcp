<?php

namespace Query\Application\Service\Firm;

use Query\Domain\Model\Firm\Client;
use Resources\Exception\RegularException;
use Tests\TestBase;

class ClientLoginTest extends TestBase
{
    protected $service;
    protected $clientRepository, $client;
    
    protected $firmIdentifier = 'firmIdentifier', $email = 'client@email.org', $password = 'password123';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository = $this->buildMockOfClass(ClientRepository::class);
        $this->clientRepository->expects($this->any())
                ->method('ofEmail')
                ->with($this->firmIdentifier, $this->email)
                ->willReturn($this->client);
        
        $this->service = new ClientLogin($this->clientRepository);
    }
    
    protected function execute()
    {
        $this->clientRepository->expects($this->any())
                ->method('ofEmail')
                ->with($this->firmIdentifier, $this->email)
                ->willReturn($this->client);
        
        $this->client->expects($this->any())
                ->method('isActivated')
                ->willReturn(true);
        
        $this->client->expects($this->any())
                ->method('passwordMatch')
                ->with($this->password)
                ->willReturn(true);
        
        return $this->service->execute($this->firmIdentifier, $this->email, $this->password);
    }
    public function test_execute_returnClient()
    {
        $this->assertEquals($this->client, $this->execute());
    }
    public function test_execute_clientNotFound_UnauthorizedError()
    {
        
        $this->clientRepository->expects($this->any())
                ->method('ofEmail')
                ->with($this->firmIdentifier, $this->email)
                ->willThrowException(RegularException::notFound('error'));
        
        $operation = function (){
            $this->execute();
        };
        $errorDetail = 'unauthorized: invalid email or password';
        $this->assertRegularExceptionThrowed($operation, 'Unauthorized', $errorDetail);
    }
    public function test_execute_unmatchPassword_unauthorizedError()
    {
        $this->client->expects($this->once())
                ->method('passwordMatch')
                ->with($this->password)
                ->willReturn(false);
        $operation = function (){
            $this->execute();
        };
        $errorDetail = 'unauthorized: invalid email or password';
        $this->assertRegularExceptionThrowed($operation, 'Unauthorized', $errorDetail);
    }
    public function test_execute_inactiveClient_unauthorizedError()
    {
        $this->client->expects($this->once())
                ->method('isActivated')
                ->willReturn(false);
        $operation = function (){
            $this->execute();
        };
        $errorDetail = 'unauthorized: invalid email or password';
        $this->assertRegularExceptionThrowed($operation, 'Unauthorized', $errorDetail);
    }
}
