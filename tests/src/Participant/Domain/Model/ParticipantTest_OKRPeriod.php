<?php

namespace Participant\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\Model\Participant\OKRPeriod;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\KeyResultData;
use Participant\Domain\Model\Participant\OKRPeriod\ObjectiveData;
use Participant\Domain\Model\Participant\OKRPeriodData;
use Resources\Domain\Data\DataCollection;
use Tests\src\Participant\Domain\Model\ParticipantTestBase;

class ParticipantTest_OKRPeriod extends ParticipantTestBase
{
    protected $okrPeriod;
    protected $okrPeriodId = 'okrPeriodId', $okrPeriodData;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->okrPeriod = $this->buildMockOfClass(OKRPeriod::class);
        $this->participant->okrPeriods = new ArrayCollection();
        $this->okrPeriodData = $this->buildMockOfClass(OKRPeriodData::class);
        $this->setMockDataContainValidLabelData($this->okrPeriodData);
        
        $objectiveData = $this->buildMockOfClass(ObjectiveData::class);
        $objectiveData->expects($this->any())->method('getWeight')->willReturn(10);
        $this->setMockDataContainValidLabelData($objectiveData);
        $objectiveDataCollection = new DataCollection();
        $objectiveDataCollection->push($objectiveData, null);
        $this->okrPeriodData->expects($this->any())->method('getObjectiveDataCollectionIterator')->willReturn($objectiveDataCollection);
        
        $keyResultData = $this->buildMockOfClass(KeyResultData::class);
        $keyResultData->expects($this->any())->method('getTarget')->willReturn(999);
        $keyResultData->expects($this->any())->method('getWeight')->willReturn(20);
        $this->setMockDataContainValidLabelData($keyResultData);
        $keyResultDataCollection = new DataCollection();
        $keyResultDataCollection->push($keyResultData, null);
        $objectiveData->expects($this->any())->method('getKeyResultDataIterator')->willReturn($keyResultDataCollection);
    }
    
    protected function executeCreateOKRPeriod()
    {
        return $this->participant->createOKRPeriod($this->okrPeriodId, $this->okrPeriodData);
    }
    public function test_createOKRPeriod_returnOKRPeriod()
    {
        $this->assertInstanceOf(OKRPeriod::class, $this->executeCreateOKRPeriod());
    }
    public function test_createOKRPeriod_inactiveParticipant_forbidden()
    {
        $this->participant->active = false;
        $this->assertInactiveParticipantError(function (){
            $this->executeCreateOKRPeriod();
        });
    }
    public function test_createOKRPeriod_hasOKRPeriodInConflictWithNewOKRPeriod_forbidden()
    {
        $this->okrPeriod->expects($this->once())
                ->method('inConflictWith')
                ->willReturn(true);
        $this->participant->okrPeriods->add($this->okrPeriod);
        $operation = function (){
            $this->executeCreateOKRPeriod();
        };
        $this->assertRegularExceptionThrowed($operation, 'Conflict', 'conflict: okr period in conflict with existing okr period');
    }
    
    protected function executeUpdate()
    {
        $this->setAssetManageable($this->okrPeriod);
        $this->participant->updateOKRPeriod($this->okrPeriod, $this->okrPeriodData);
    }
    public function test_update_updateOKRPeriod()
    {
        $this->okrPeriod->expects($this->once())
                ->method('update')
                ->with($this->okrPeriodData);
        $this->executeUpdate();
    }
    public function test_update_inactiveParticipant_forbidden()
    {
        $this->participant->active = false;
        $this->assertInactiveParticipantError(function (){
            $this->executeUpdate();
        });
    }
    public function test_update_unmanageableOKRPeriod_forbidden()
    {
        $this->setAssetUnmanageable($this->okrPeriod);
        $this->assertUnmanageableByParticipantError(function (){
            $this->executeUpdate();
        }, 'okr period');
    }
    public function test_updateOKRPeriod_updateOKRPeriodConflictedWithExisting_forbidden()
    {
        $existingOKRPeriod = clone $this->okrPeriod;
        $existingOKRPeriod->expects($this->once())
                ->method('inConflictWith')
                ->willReturn(true);
        $this->participant->okrPeriods->add($existingOKRPeriod);
        $operation = function (){
            $this->executeUpdate();
        };
        $this->assertRegularExceptionThrowed($operation, 'Conflict', 'conflict: okr period in conflict with existing okr period');
    }
    
    protected function executeCancel()
    {
        $this->setAssetManageable($this->okrPeriod);
        $this->participant->cancelOKRPeriod($this->okrPeriod);
    }
    public function test_cancel_disableOKRPeriod()
    {
        $this->okrPeriod->expects($this->once())
                ->method('cancel');
        $this->executeCancel();
    }
    public function test_cancel_inactiveParticipant_forbidden()
    {
        $this->participant->active = false;
        $this->assertInactiveParticipantError(function (){
            $this->executeCancel();
        });
    }
    public function test_cancel_unamanageableOKRPeriod_forbidden()
    {
        $this->setAssetUnmanageable($this->okrPeriod);
        $this->assertUnmanageableByParticipantError(function (){
            $this->executeCancel();
        }, 'okr period');
    }
}
