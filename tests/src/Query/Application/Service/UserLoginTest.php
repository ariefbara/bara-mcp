<?php

namespace Query\Application\Service;

use Query\Domain\Model\User;
use Resources\Exception\RegularException;
use Tests\TestBase;

class UserLoginTest extends TestBase
{

    protected $service;
    protected $userRepository, $user, $email = 'user@email.org', $password = 'password123';

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->buildMockOfInterface(UserRepository::class);
        $this->user = $this->buildMockOfClass(User::class);

        $this->service = new UserLogin($this->userRepository);
    }

    protected function execute()
    {
        $this->userRepository->expects($this->any())
                ->method('ofEmail')
                ->with($this->email)
                ->willReturn($this->user);
        $this->user->expects($this->any())
                ->method('passwordMatches')
                ->with($this->password)
                ->willReturn(true);
        $this->user->expects($this->any())
                ->method('isActivated')
                ->willReturn(true);
        return $this->service->execute($this->email, $this->password);
    }

    public function test_execute_returnUserEntity()
    {
        $this->assertEquals($this->user, $this->execute());
    }

    public function test_execute_userNotFound_throwEx()
    {
        $this->userRepository->expects($this->once())
                ->method('ofEmail')
                ->with($this->email)
                ->willThrowException(RegularException::notFound('not found: user not found'));
        $operation = function () {
            $this->execute();
        };
        $errorDetail = 'unauthorized: invalid email or password';
        $this->assertRegularExceptionThrowed($operation, "Unauthorized", $errorDetail);
    }

    public function test_execute_passwordNotMatch_throwEx()
    {
        $this->user->expects($this->once())
                ->method('passwordMatches')
                ->with($this->password)
                ->willReturn(false);
        $operation = function () {
            $this->execute();
        };
        $errorDetail = 'unauthorized: invalid email or password';
        $this->assertRegularExceptionThrowed($operation, "Unauthorized", $errorDetail);
    }

    public function test_execute_inactiveUser_throwEx()
    {
        $this->user->expects($this->once())
                ->method('isActivated')
                ->willReturn(false);
        $operation = function () {
            $this->execute();
        };
        $errorDetail = 'unauthorized: account not activated';
        $this->assertRegularExceptionThrowed($operation, "Unauthorized", $errorDetail);
    }

}
