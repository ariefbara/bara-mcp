<?php

namespace Personnel\Domain\SharedModel;

use Personnel\Domain\ {
    Model\Firm\Personnel\ProgramConsultant,
    SharedModel\ActivityLog\ConsultantActivityLog
};
use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;

class ActivityLogTest extends TestBase
{

    protected $consultant;
    protected $id = "newId", $message = "new message";

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultant = $this->buildMockOfClass(ProgramConsultant::class);
    }

    protected function executeConstruct()
    {
        return new TestableActivityLog($this->id, $this->message, $this->consultant);
    }

    public function test_construct_setProperties()
    {
        $activityLog = $this->executeConstruct();
        $this->assertEquals($this->id, $activityLog->id);
        $this->assertEquals($this->message, $activityLog->message);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $activityLog->occuredTime);
    }
    public function test_construct_setconsultantActivityLog()
    {
        $activityLog = $this->executeConstruct();
        $consultantActivityLog = new ConsultantActivityLog($activityLog, $this->id, $this->consultant);
        $this->assertEquals($consultantActivityLog, $activityLog->consultantActivityLog);
    }

}

class TestableActivityLog extends ActivityLog
{

    public $id;
    public $message;
    public $occuredTime;
    public $consultantActivityLog;

}
