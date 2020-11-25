<?php

namespace Notification\Domain\Model\Firm;

use Notification\Domain\Model\ {
    Firm,
    Firm\Manager\ManagerMail
};
use Tests\TestBase;

class ManagerTest extends TestBase
{
    protected $manager;
    protected $firm;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new TestableManager();
        
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->manager->firm = $this->firm;
    }
    
    public function test_createResetPasswordMail_returnManagerMail()
    {
        $this->assertInstanceOf(ManagerMail::class, $this->manager->createResetPasswordMail("managerMailId"));
    }
    
}

class TestableManager extends Manager
{
    public $firm;
    public $id;
    public $name = "manager name";
    public $email = "manager@email.org";
    public $resetPasswordCode = "resetPasswordCode";
    public $resetPasswordCodeExpiredTime;
    
    function __construct()
    {
        parent::__construct();
    }
}
