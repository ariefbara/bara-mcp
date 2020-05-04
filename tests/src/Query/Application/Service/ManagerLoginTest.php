<?php

namespace Query\Application\Service\Firm;

use Query\Domain\Model\Firm\Manager;
use Resources\Exception\RegularException;
use Tests\TestBase;

class ManagerLoginTest extends TestBase
{

    protected $service;
    protected $firmIdentifier = 'firm_identifier';
    protected $managerRepository, $manager, $managerId = 'managerId';
    protected $email = 'manager@email.org', $password = 'password123';

    protected function setUp(): void
    {
        parent::setUp();
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository->expects($this->any())
                ->method('ofEmail')
                ->with($this->firmIdentifier, $this->email)
                ->willReturn($this->manager);
        $this->service = new ManagerLogin($this->managerRepository);
    }

    protected function execute()
    {
        $this->manager->expects($this->any())
                ->method('passwordMatches')
                ->willReturn(true);
        $this->managerRepository->expects($this->any())
                ->method('ofEmail')
                ->with($this->firmIdentifier, $this->email)
                ->willReturn($this->manager);
        return $this->service->execute($this->firmIdentifier, $this->email, $this->password);
    }

    public function test_execute_returnManager()
    {
        $this->assertEquals($this->manager, $this->execute());
    }

    public function test_execute_managerNotFound_throwEx()
    {
        $this->managerRepository->expects($this->any())
                ->method('ofEmail')
                ->with($this->firmIdentifier, $this->email)
                ->willThrowException(RegularException::notFound('not found error'));
        $operation = function () {
            $this->execute();
        };
        $errorDetail = "unauthorized: invalid email or password";
        $this->assertRegularExceptionThrowed($operation, 'Unauthorized', $errorDetail);
    }

    public function test_execute_passwordNotMatch_throwEx()
    {
        $this->manager->expects($this->once())
                ->method('passwordMatches')
                ->with($this->password)
                ->willReturn(false);
        $operation = function () {
            $this->execute();
        };
        $errorDetail = "unauthorized: invalid email or password";
        $this->assertRegularExceptionThrowed($operation, 'Unauthorized', $errorDetail);
    }

}
