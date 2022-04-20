<?php

namespace SharedContext\Domain\ValueObject;

use Tests\TestBase;

class ProgramSnapshotTest extends TestBase
{
    protected $name = 'program name';
    protected $price = 1000000;
    protected $autoAccept = false;
    protected $snap;

    protected function setUp(): void
    {
        parent::setUp();
        $this->snap = new TestableProgramSnapshot('name', 700000, false);
    }
    
    protected function construct()
    {
        return new TestableProgramSnapshot($this->name, $this->price, $this->autoAccept);
    }
    public function test_construct_setProperties()
    {
        $snap = $this->construct();
        $this->assertSame($this->price, $snap->price);
        $this->assertSame($this->autoAccept, $snap->autoAccept);
    }
    
    protected function generateInitialRegistrationStatus()
    {
        return $this->snap->generateInitialRegistrationStatus();
    }
    public function test_generateInitialRegistrationStatus_returnRegisteredStatus()
    {
        $status = new RegistrationStatus(RegistrationStatus::REGISTERED);
        $this->assertEquals($status, $this->generateInitialRegistrationStatus());
    }
    public function test_generateInitialRegistrationStatus_anAutoAcceptProgram_returnSettlementRequiredStatus()
    {
        $this->snap->autoAccept = true;
        $status = new RegistrationStatus(RegistrationStatus::SETTLEMENT_REQUIRED);
        $this->assertEquals($status, $this->generateInitialRegistrationStatus());
    }
    public function test_generateInitialRegistrationStatus_anAutoAcceptProgramFreeProgram_returnAcceptedStatus()
    {
        $this->snap->autoAccept = true;
        $this->snap->price = 0;
        $status = new RegistrationStatus(RegistrationStatus::ACCEPTED);
        $this->assertEquals($status, $this->generateInitialRegistrationStatus());
    }
    
    //
    protected function generateItemInfo()
    {
        return $this->snap->generateItemInfo();
    }
    public function test_generateItemInfo_returnItemInfo()
    {
        $itemInfo = new ItemInfo($this->snap->name, 1, $this->snap->price, null, null);
        $this->assertEquals($itemInfo, $this->generateItemInfo());
    }
    
}

class TestableProgramSnapshot extends ProgramSnapshot
{
    public $name;
    public $price;
    public $autoAccept;
}
