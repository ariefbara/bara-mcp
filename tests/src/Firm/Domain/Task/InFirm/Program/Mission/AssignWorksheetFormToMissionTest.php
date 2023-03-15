<?php

namespace Firm\Domain\Task\InFirm\Program\Mission;

use Tests\src\Firm\Domain\Task\InFirm\FirmTaskTestBase;

class AssignWorksheetFormToMissionTest extends FirmTaskTestBase
{
    protected $task;
    protected $payload;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setMissionRelatedDependency();
        $this->setWorksheetFormRelatedDependency();
        //
        $this->task = new AssignWorksheetFormToMission($this->missionRepository, $this->worksheetFormRepository);
        //
        $this->payload = (new AssignWorksheetFormToMissionPayload())
                ->setMissionId($this->missionId)
                ->setWorksheetFormId($this->worksheetFormId);
    }
    
    //
    protected function execute()
    {
        $this->task->executeInFirm($this->firm, $this->payload);
    }
    public function test_execute_assignWorksheetFormInMission()
    {
        $this->mission->expects($this->once())
                ->method('assignWorksheetForm')
                ->with($this->worksheetForm);
        $this->execute();
    }
    public function test_execute_assertMissionManageableInFirm()
    {
        $this->mission->expects($this->once())
                ->method('assertManageableInFirm')
                ->with($this->firm);
        $this->execute();
    }
    public function test_execute_assertWorksheetFormAccessibleInFirm()
    {
        $this->worksheetForm->expects($this->once())
                ->method('assertAccessibleInFirm')
                ->with($this->firm);
        $this->execute();
    }
}
