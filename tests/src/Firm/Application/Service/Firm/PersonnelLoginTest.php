<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\Firm\Personnel;
use Tests\TestBase;

class PersonnelLoginTest extends TestBase
{
    protected $service;
    protected $personnelRepository, $personnel, $firmIdentifier = "firm-identifier", $email = "personnel@email.org";
    protected $password = 'passwor123';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->personnelRepository = $this->buildMockOfInterface(PersonnelRepository::class);
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        
        $this->service = new PersonnelLogin($this->personnelRepository);
    }
    
    protected function execute()
    {
        $this->personnelRepository->expects($this->any())
                ->method('ofEmail')
                ->willReturn($this->personnel);
        $this->personnel->expects($this->any())
                ->method('passwordMatches')
                ->willReturn(true);
        return $this->service->execute($this->firmIdentifier, $this->email, $this->password);
    }
    
    public function test_execute_returnPersonnel()
    {
        $this->assertEquals($this->personnel, $this->execute());
    }
    public function test_execute_personnelNotFound_throwEx()
    {
        $this->personnelRepository->expects($this->once())
                ->method('ofEmail')
                ->with($this->firmIdentifier, $this->email)
                ->willThrowException(\Resources\Exception\RegularException::notFound('error'));
        $operation = function (){
            $this->execute();
        };
        $errorDetail = 'unauthorized: invalid email or password';
        $this->assertRegularExceptionThrowed($operation, 'Unauthorized', $errorDetail);
    }
    public function test_execute_passwordNotMatch_throwEx()
    {
        $this->personnel->expects($this->once())
                ->method('passwordMatches')
                ->with($this->password)
                ->willReturn(false);
        $operation = function (){
            $this->execute();
        };
        $errorDetail = 'unauthorized: invalid email or password';
        $this->assertRegularExceptionThrowed($operation, 'Unauthorized', $errorDetail);
        
    }
}
