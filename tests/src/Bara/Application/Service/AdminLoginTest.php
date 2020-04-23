<?php

namespace Bara\Application\Service;

use Bara\Domain\Model\Admin;
use Resources\Exception\RegularException;
use Tests\TestBase;

class AdminLoginTest extends TestBase
{

    protected $service;
    protected $adminRepository, $admin, $email = 'admin@email.org', $password = 'password123';

    public function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->buildMockOfClass(Admin::class);
        $this->adminRepository = $this->buildMockOfInterface(AdminRepository::class);

        $this->service = new AdminLogin($this->adminRepository);
    }

    protected function execute()
    {
        $this->adminRepository->expects($this->any())
            ->method('ofEmail')
            ->with($this->email)
            ->willReturn($this->admin);
        return $this->service->execute($this->email, $this->password);
    }

    function test_execute_returnAdmin()
    {
        $this->admin->expects($this->any())
            ->method('passwordMatch')
            ->with($this->password)
            ->willReturn(true);
        $this->assertEquals($this->admin, $this->execute());
    }

    function test_execute_passwordNotMatch_throwEx()
    {
        $operation = function () {
            $this->execute();
        };
        $errorDetail = 'unauthorized: invalid email or password';
        $this->assertRegularExceptionThrowed($operation, 'Unauthorized', $errorDetail);
    }

    public function test_execute_adminNotFound_throwEx()
    {
        $this->adminRepository->expects($this->once())
            ->method('ofEmail')
            ->with($this->email)
            ->willThrowException(new RegularException('not found'));
        $operation = function () {
            $this->execute();
        };
        $errorDetail = 'unauthorized: invalid email or password';
        $this->assertRegularExceptionThrowed($operation, 'Unauthorized', $errorDetail);
    }

}
