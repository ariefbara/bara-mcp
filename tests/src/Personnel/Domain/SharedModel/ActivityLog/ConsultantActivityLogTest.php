<?php

namespace Personnel\Domain\SharedModel\ActivityLog;

use Personnel\Domain\ {
    Model\Firm\Personnel\ProgramConsultant,
    SharedModel\ActivityLog
};
use Tests\TestBase;

class ConsultantActivityLogTest extends TestBase
{
    protected $activityLog;
    protected $consultant;
    protected $id = "newId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->activityLog = $this->buildMockOfClass(ActivityLog::class);
        $this->consultant = $this->buildMockOfClass(ProgramConsultant::class);
    }
    
    public function test_construct_setProperties()
    {
        $consultantActivityLog = new TestableConsultantActivityLog($this->activityLog, $this->id, $this->consultant);
        $this->assertEquals($this->activityLog, $consultantActivityLog->activityLog);
        $this->assertEquals($this->id, $consultantActivityLog->id);
        $this->assertEquals($this->consultant, $consultantActivityLog->consultant);
    }
}

class TestableConsultantActivityLog extends ConsultantActivityLog
{
    public $activityLog;
    public $id;
    public $consultant;
}
