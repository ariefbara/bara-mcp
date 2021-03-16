<?php

namespace Tests\src\Firm\Domain\Model\Firm;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Firm\ManagerData;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class ManagerTestBase extends TestBase
{
    protected $firm;
    /**
     * 
     * @var TestableManager
     */
    protected $manager;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
        $managerData = new ManagerData('name', 'manager@email.org', 'password123', '08213123142');
        $this->manager = new TestableManager($this->firm, 'id', $managerData);
    }
    protected function assertInactiveManager(callable $operation)
    {
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: only active manager can make this request');
    }
    protected function assertAssetNotManageableByFirm(callable $operation)
    {
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: can only manage asset manageable by firm');
    }
    protected function setAssetManageableByFirm(MockObject $asset): void
    {
        $asset->expects($this->any())->method('isManageableByFirm')->willReturn(true);
    }
    protected function setAssetUnmanageableByFirm(MockObject $asset): void
    {
        $asset->expects($this->any())->method('isManageableByFirm')->willReturn(false);
    }
}

class TestableManager extends Manager
{
    public $firm;
    public $id;
    public $name;
    public $email;
    public $password;
    public $phone;
    public $joinTime;
    public $removed = false;
}
