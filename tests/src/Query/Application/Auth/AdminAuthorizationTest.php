<?php

namespace Query\Application\Auth;

use Tests\TestBase;

class AdminAuthorizationTest extends TestBase
{

    protected $adminRepository, $adminId = 'adminId';
    protected $auth;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminRepository = $this->buildMockOfInterface(AdminRepository::class);
        $this->auth = new AdminAuthorization($this->adminRepository);
    }

    protected function execute()
    {
        $this->auth->execute($this->adminId);
    }

    public function test_execute_noRecordOfAdminInRepository_throwEx()
    {
        $operation = function () {
            $this->execute();
        };
        $errorDetail = 'unauthorized: only admin can make this request';
        $this->assertRegularExceptionThrowed($operation, 'Unauthorized', $errorDetail);
    }

    public function test_execute_repositoryContainRecordOfAdmin_void()
    {
        $this->adminRepository->expects($this->once())
            ->method('containRecordOfId')
            ->with($this->adminId)
            ->willReturn(true);
        $this->execute();
    }

}
