<?php

namespace Participant\Domain\DependencyModel\Firm\Client;

use Participant\Domain\Model\Participant\OKRPeriod;
use Participant\Domain\Model\Participant\OKRPeriodData;
use Participant\Domain\Model\TeamProgramParticipation;
use Tests\src\Participant\Domain\DependencyModel\Firm\Client\TeamMembershipTestBase;

class TeamMembershipTest_ManageOKRPeriod extends TeamMembershipTestBase
{
    protected $teamParticipant;
    protected $okrPeriod;
    protected $okrPeriodId = 'okrPeriodId';
    protected $okrPeriodData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->teamParticipant = $this->buildMockOfClass(TeamProgramParticipation::class);
        $this->okrPeriod = $this->buildMockOfClass(OKRPeriod::class);
        $this->okrPeriodData = $this->buildMockOfClass(OKRPeriodData::class);
    }
    
    protected function executeCreateOKRPeriodInTeamParticipant()
    {
        $this->setAssetBelongsToTeam($this->teamParticipant);
        return $this->teamMembership->createOKRPeriodInTeamParticipant(
                $this->teamParticipant, $this->okrPeriodId, $this->okrPeriodData);
    }
    public function test_createOKRPeriodInTeamParticipant_returnOKRPeriodCratedInTeamParticipant()
    {
        $this->teamParticipant->expects($this->once())
                ->method('createOKRPeriod')
                ->with($this->okrPeriodId, $this->okrPeriodData);
        $this->executeCreateOKRPeriodInTeamParticipant();
    }    
    public function test_createOKRPeriodInTeamParticipant_inactiveMember_forbidden()
    {
        $this->teamMembership->active = false;
        $this->assertInactiveTeamMemberError(function (){
            $this->executeCreateOKRPeriodInTeamParticipant();
        });
    }
    public function test_createOKRPeriodInTeamParticipant_unmanageTeamParticipant_forbidden()
    {
        $this->setAssetNotBelongsToTeam($this->teamParticipant);
        $this->assertAssetUnmanageableError(function (){
            $this->executeCreateOKRPeriodInTeamParticipant();
        });
    }
    
    protected function executeUpdateOKRPeriod()
    {
        $this->setAssetBelongsToTeam($this->teamParticipant);
        $this->teamMembership->updateOKRPeriod($this->teamParticipant, $this->okrPeriod, $this->okrPeriodData);
    }
    public function test_updateOKRPeriod_updateOKRPeriodInTeamParticipant()
    {
        $this->teamParticipant->expects($this->once())
                ->method('updateOKRPeriod')
                ->with($this->okrPeriod, $this->okrPeriodData);
        $this->executeUpdateOKRPeriod();
    }
    public function test_udpateOKRPeriod_inactiveMember_forbidden()
    {
        $this->teamMembership->active = false;
        $this->assertInactiveTeamMemberError(function (){
            $this->executeUpdateOKRPeriod();
        });
    }
    public function test_updateOKRPeriod_unmanageTeamParticipant_forbidden()
    {
        $this->setAssetNotBelongsToTeam($this->teamParticipant);
        $this->assertAssetUnmanageableError(function (){
            $this->executeUpdateOKRPeriod();
        });
    }
    
    protected function executeDisableOKRPeriod()
    {
        $this->setAssetBelongsToTeam($this->teamParticipant);
        $this->teamMembership->cancelOKRPeriod($this->teamParticipant, $this->okrPeriod);
    }
    public function test_disableOKRPeriod_disableOKRPeriod()
    {
        $this->teamParticipant->expects($this->once())
                ->method('cancelOKRPeriod')
                ->with($this->okrPeriod);
        $this->executeDisableOKRPeriod();
    }
    public function test_disableOKRPeriod_inactiveMember_forbidden()
    {
        $this->teamMembership->active = false;
        $this->assertInactiveTeamMemberError(function (){
            $this->executeDisableOKRPeriod();
        });
    }
    public function test_disableOKRPeriod_unmanageTeamParticipant_forbidden()
    {
        $this->setAssetNotBelongsToTeam($this->teamParticipant);
        $this->assertAssetUnmanageableError(function (){
            $this->executeDisableOKRPeriod();
        });
    }
}
