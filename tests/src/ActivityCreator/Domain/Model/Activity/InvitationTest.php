<?php

namespace ActivityCreator\Domain\Model\Activity;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Manager,
    DependencyModel\Firm\Personnel\Consultant,
    DependencyModel\Firm\Personnel\Coordinator,
    DependencyModel\Firm\Program\Participant,
    Model\Activity,
    service\ActivityDataProvider
};
use Tests\TestBase;

class InvitationTest extends TestBase
{

    protected $activity;
    protected $invitation;
    protected $managerInvitation;
    protected $coordinatorInvitation;
    protected $consultantInvitation;
    protected $participantInvitation;
    protected $manager;
    protected $coordinator;
    protected $consultant;
    protected $participant;
    protected $id = "newId";
    protected $activityDataProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->activity = $this->buildMockOfClass(Activity::class);
        $this->manager = $this->buildMockOfClass(Manager::class);
        
        $this->invitation = TestableInvitation::inviteManager($this->activity, "id", $this->manager);
        $this->managerInvitation = $this->buildMockOfClass(ManagerInvitation::class);
        $this->invitation->managerInvitation = $this->managerInvitation;
        
        $this->coordinatorInvitation = $this->buildMockOfClass(CoordinatorInvitation::class);
        $this->consultantInvitation = $this->buildMockOfClass(ConsultantInvitation::class);
        $this->participantInvitation = $this->buildMockOfClass(ParticipantInvitation::class);
        
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->activityDataProvider = $this->buildMockOfClass(ActivityDataProvider::class);
    }
    
    public function test_executeInviteManager_setProperties()
    {
        $invitation = TestableInvitation::inviteManager($this->activity, $this->id, $this->manager);
        $this->assertEquals($this->activity, $invitation->activity);
        $this->assertEquals($this->id, $invitation->id);
        
        $managerInvitation = new ManagerInvitation($invitation, $this->id, $this->manager);
        $this->assertEquals($managerInvitation, $invitation->managerInvitation);
        
        $this->assertNull($invitation->willAttend);
        $this->assertNull($invitation->attended);
        $this->assertFalse($invitation->removed);
        
        $this->assertNull($invitation->coordinatorInvitation);
        $this->assertNull($invitation->consultantInvitation);
        $this->assertNull($invitation->participantInvitation);
    }
    public function test_executeInviteCooordinator_setCoordinatorInvitation()
    {
        $invitation = TestableInvitation::inviteCoordinator($this->activity, $this->id, $this->coordinator);
        $coordinatorInvitation = new CoordinatorInvitation($invitation, $this->id, $this->coordinator);
        $this->assertEquals($coordinatorInvitation, $invitation->coordinatorInvitation);
    }
    public function test_executeInviteConsultant_setConsultantInvitation()
    {
        $invitation = TestableInvitation::inviteConsultant($this->activity, $this->id, $this->consultant);
        $consultantInvitation = new ConsultantInvitation($invitation, $this->id, $this->consultant);
        $this->assertEquals($consultantInvitation, $invitation->consultantInvitation);
    }
    public function test_executeInviteParticipant_setParticipantInvitation()
    {
        $invitation = TestableInvitation::inviteParticipant($this->activity, $this->id, $this->participant);
        $ParticipantInvitation = new ParticipantInvitation($invitation, $this->id, $this->participant);
        $this->assertEquals($ParticipantInvitation, $invitation->participantInvitation);
    }
    
    protected function executeRemoveIfNotAppearInList()
    {
        $this->invitation->removeIfNotAppearInList($this->activityDataProvider);
    }
    public function test_removeIfNotAppearInList_executeManagerInvitationsRemoveIfNotAppearInList()
    {
        $this->managerInvitation->expects($this->once())
                ->method("removeIfNotApprearInList")
                ->with($this->activityDataProvider);
        $this->executeRemoveIfNotAppearInList();
    }
    public function test_removeIfNotAppearInList_aCoordinatorInvitation_executeCoordinatorInvitationsRemoveIfNotAppearInList()
    {
        $this->invitation->managerInvitation = null;
        $this->invitation->coordinatorInvitation = $this->coordinatorInvitation;
        
        $this->coordinatorInvitation->expects($this->once())
                ->method("removeIfNotApprearInList")
                ->with($this->activityDataProvider);
        $this->executeRemoveIfNotAppearInList();
    }
    public function test_removeIfNotAppearInList_aConsultantInvitation_executeConsultantInvitationsRemoveIfNotAppearInList()
    {
        $this->invitation->managerInvitation = null;
        $this->invitation->consultantInvitation = $this->consultantInvitation;
        
        $this->consultantInvitation->expects($this->once())
                ->method("removeIfNotApprearInList")
                ->with($this->activityDataProvider);
        $this->executeRemoveIfNotAppearInList();
    }
    public function test_removeIfNotAppearInList_aParticipantInvitation_executeParticipantInvitationsRemoveIfNotAppearInList()
    {
        $this->invitation->managerInvitation = null;
        $this->invitation->participantInvitation = $this->participantInvitation;
        
        $this->participantInvitation->expects($this->once())
                ->method("removeIfNotApprearInList")
                ->with($this->activityDataProvider);
        $this->executeRemoveIfNotAppearInList();
    }
    
    protected function executeIsNonRemovedInvitationCorrespondWithManager()
    {
        $this->managerInvitation->expects($this->any())
                ->method("managerEquals")
                ->willReturn(true);
        return $this->invitation->isNonRemovedInvitationCorrespondWithManager($this->manager);
    }
    public function test_isNonRemovedInvitationCorrespondWithManager_returnManagerInvitations_managerEqualsResult()
    {
        $this->managerInvitation->expects($this->once())
                ->method("managerEquals")
                ->with($this->manager)
                ->willReturn(true);
        $this->assertTrue($this->executeIsNonRemovedInvitationCorrespondWithManager());
    }
    public function test_isNonRemovedInvitationCorrespondWithManager_notManagerInvitation_returnFalse(){
        $this->invitation->managerInvitation = null;
        $this->assertFalse($this->executeIsNonRemovedInvitationCorrespondWithManager());
    }
    public function test_isNonRemovedInvitationCorrespondWithManager_alreadyRemoved_returnFalse(){
        $this->invitation->removed = true;
        $this->assertFalse($this->executeIsNonRemovedInvitationCorrespondWithManager());
    }
    
    protected function executeIsNonRemovedInvitationCorrespondWithCoordinator()
    {
        $this->coordinatorInvitation->expects($this->any())
                ->method("coordinatorEquals")
                ->willReturn(true);
        return $this->invitation->isNonRemovedInvitationCorrespondWithCoordinator($this->coordinator);
    }
    public function test_isNonRemovedInvitationCorrespondWithCoordinator_returnCoordinatorInvitations_coordinatorEqualsResult()
    {
        $this->invitation->coordinatorInvitation = $this->coordinatorInvitation;
        $this->coordinatorInvitation->expects($this->once())
                ->method("coordinatorEquals")
                ->with($this->coordinator)
                ->willReturn(true);
        $this->assertTrue($this->executeIsNonRemovedInvitationCorrespondWithCoordinator());
    }
    public function test_isNonRemovedInvitationCorrespondWithCoordinator_notCoordinatorInvitation_returnFalse(){
        $this->assertFalse($this->executeIsNonRemovedInvitationCorrespondWithCoordinator());
    }
    public function test_isNonRemovedInvitationCorrespondWithCoordinator_alreadyRemoved_returnFalse(){
        $this->invitation->removed = true;
        $this->invitation->coordinatorInvitation = $this->coordinatorInvitation;
        $this->assertFalse($this->executeIsNonRemovedInvitationCorrespondWithCoordinator());
    }
    
    protected function executeIsNonRemovedInvitationCorrespondWithConsultant()
    {
        $this->consultantInvitation->expects($this->any())
                ->method("consultantEquals")
                ->willReturn(true);
        return $this->invitation->isNonRemovedInvitationCorrespondWithConsultant($this->consultant);
    }
    public function test_isNonRemovedInvitationCorrespondWithConsultant_returnConsultantInvitations_consultantEqualsResult()
    {
        $this->invitation->consultantInvitation = $this->consultantInvitation;
        $this->consultantInvitation->expects($this->once())
                ->method("consultantEquals")
                ->with($this->consultant)
                ->willReturn(true);
        $this->assertTrue($this->executeIsNonRemovedInvitationCorrespondWithConsultant());
    }
    public function test_isNonRemovedInvitationCorrespondWithConsultant_notConsultantInvitation_returnFalse(){
        $this->assertFalse($this->executeIsNonRemovedInvitationCorrespondWithConsultant());
    }
    public function test_isNonRemovedInvitationCorrespondWithConsultant_alreadyRemoved_returnFalse(){
        $this->invitation->removed = true;
        $this->invitation->consultantInvitation = $this->consultantInvitation;
        $this->assertFalse($this->executeIsNonRemovedInvitationCorrespondWithConsultant());
    }
    
    protected function executeIsNonRemovedInvitationCorrespondWithParticipant()
    {
        $this->participantInvitation->expects($this->any())
                ->method("participantEquals")
                ->willReturn(true);
        return $this->invitation->isNonRemovedInvitationCorrespondWithParticipant($this->participant);
    }
    public function test_isNonRemovedInvitationCorrespondWithParticipant_returnParticipantInvitations_participantEqualsResult()
    {
        $this->invitation->participantInvitation = $this->participantInvitation;
        $this->participantInvitation->expects($this->once())
                ->method("participantEquals")
                ->with($this->participant)
                ->willReturn(true);
        $this->assertTrue($this->executeIsNonRemovedInvitationCorrespondWithParticipant());
    }
    public function test_isNonRemovedInvitationCorrespondWithParticipant_notParticipantInvitation_returnFalse(){
        $this->assertFalse($this->executeIsNonRemovedInvitationCorrespondWithParticipant());
    }
    public function test_isNonRemovedInvitationCorrespondWithParticipant_alreadyRemoved_returnFalse(){
        $this->invitation->removed = true;
        $this->invitation->participantInvitation = $this->participantInvitation;
        $this->assertFalse($this->executeIsNonRemovedInvitationCorrespondWithParticipant());
    }
    
    public function test_remove_setRemovedStatusTrue()
    {
        $this->invitation->remove();
        $this->assertTrue($this->invitation->removed);
    }

}

class TestableInvitation extends Invitation
{

    public $activity;
    public $id;
    public $willAttend;
    public $attended;
    public $removed;
    public $managerInvitation;
    public $coordinatorInvitation;
    public $consultantInvitation;
    public $participantInvitation;

}
