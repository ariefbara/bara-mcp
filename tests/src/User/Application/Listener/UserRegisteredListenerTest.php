<?php

namespace User\Application\Listener;

use Tests\TestBase;

class UserRegisteredListenerTest extends TestBase
{

    protected $listener;
    protected $event, $userRegistrantId = 'userRegistrantId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->listener = new TestableUserRegistrationReceivedListener();
        $this->event = $this->buildMockOfClass(UserRegisteredEventInterface::class);
        $this->event->expects($this->once())->method('getUserRegistrantId')->willReturn($this->userRegistrantId);
    }
    public function test_handel_setUserRegistrantId()
    {
        $this->listener->handle($this->event);
        $this->assertEquals($this->userRegistrantId, $this->listener->userRegistrantId);
    }

}

class TestableUserRegistrationReceivedListener extends UserRegisteredListener
{

    public $userRegistrantId;

}
