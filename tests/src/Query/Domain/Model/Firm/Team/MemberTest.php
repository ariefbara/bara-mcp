<?php

namespace Query\Domain\Model\Firm\Team;

use Query\Domain\Event\LearningMaterialViewedByParticipantEvent;
use Query\Domain\Event\LearningMaterialViewedByTeamMemberEvent;
use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\Team;
use Query\Domain\Service\Firm\ClientFinder;
use Query\Domain\Service\Firm\Program\MentorRepository;
use Query\Domain\Service\Firm\Program\Mission\MissionCommentRepository;
use Query\Domain\Service\Firm\Team\TeamProgramParticipationFinder;
use Query\Domain\Service\LearningMaterialFinder;
use Query\Domain\Service\TeamProgramParticipationFinder as TeamProgramParticipationFinder2;
use Tests\TestBase;

class MemberTest extends TestBase
{
    protected $member;
    protected $client;

    protected $clientFinder, $clientEmail = "client@email.org";
    protected $teamProgramParticipationFinder, $teamProgramParticipationId = "teamProgramParticipationid";
    
    protected $teamProgramParticipation;
    protected $programParticipationFinder, $programId = "programId";
    protected $learningMaterialFinder, $learningMaterialId = "learningMaterialId";
    protected $page = 1, $pageSize = 25;
    protected $missionCommentRepository, $missionId = 'missionId', $missionCommentId = 'missionCommentId';
    protected $mentorRepository, $mentorId = 'mentorId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->member = new TestableMember();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->member->client = $this->client;
        $this->member->team = $this->buildMockOfClass(Team::class);
        
        $this->teamProgramParticipationFinder = $this->buildMockOfClass(TeamProgramParticipationFinder::class);
        
        $this->clientFinder = $this->buildMockOfClass(ClientFinder::class);
        $this->teamProgramParticipation = $this->buildMockOfClass(TeamProgramParticipation::class);
        $this->programParticipationFinder = $this->buildMockOfClass(TeamProgramParticipationFinder2::class);
        $this->programParticipationFinder->expects($this->any())
                ->method("execute")
                ->with($this->member->team, $this->programId)
                ->willReturn($this->teamProgramParticipation);
        
        $this->learningMaterialFinder = $this->buildMockOfClass(LearningMaterialFinder::class);
        $this->missionCommentRepository = $this->buildMockOfInterface(MissionCommentRepository::class);
        $this->mentorRepository = $this->buildMockOfInterface(MentorRepository::class);
    }
    protected function assertNotAdminForbiddenError(callable $operation): void
    {
        $errorDetail = "forbidden: only team admin can make this requests";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    protected function assertInactiveForbiddenError(callable $operation): void
    {
        $errorDetail = "forbidden: only active team member can make this requests";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    protected function assertUnmanageTeamParticipant(callable $operation): void
    {
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: can only manage asset of your team');
    }
    
    protected function setManageableTeamParticipant(): void
    {
        $this->teamProgramParticipation->expects($this->any())
                ->method('teamEquals')
                ->with($this->member->team)
                ->willReturn(true);
    }
    protected function setUnmanageableTeamParticipant(): void
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method('teamEquals')
                ->with($this->member->team)
                ->willReturn(false);
    }
    
    protected function executeViewClientByEmail()
    {
        return $this->member->viewClientByEmail($this->clientFinder, $this->clientEmail);
    }
    public function test_viewClientByEmail_returnClientFindersFindByEmailResult()
    {
        $this->clientFinder->expects($this->once())
                ->method("findByEmail")
                ->with($this->anything(), $this->clientEmail);
        $this->executeViewClientByEmail();
    }
    public function test_viewClientByEmail_notAdmin_forbiddenError()
    {
        $this->member->anAdmin = false;
        $this->assertNotAdminForbiddenError(function (){
            $this->executeViewClientByEmail();
        });
    }
    public function test_viewClientByEmail_inactiveMember_forbiddenError()
    {
        $this->member->active = false;
        $this->assertInactiveForbiddenError(function (){
            $this->executeViewClientByEmail();
        });
    }
    
    public function test_viewTeamProgramParticipation_returnTeamProgramParticipationFinderFindByIdResult()
    {
        $this->teamProgramParticipationFinder->expects($this->once())
                ->method("findProgramParticipationBelongsToTeam")
                ->with($this->member->team, $this->teamProgramParticipationId);
        $this->member->viewTeamProgramParticipation($this->teamProgramParticipationFinder, $this->teamProgramParticipationId);
    }
    public function test_viewTeamprogramParticipation_inactiveMember_forbiddenError()
    {
        $this->member->active = false;
        $this->assertInactiveForbiddenError(function (){
            $this->member->viewTeamProgramParticipation($this->teamProgramParticipationFinder, $this->teamProgramParticipationId);
        });
    }
    
    protected function executeViewLearningMaterial()
    {
        $this->member->viewLearningMaterial($this->programParticipationFinder, $this->programId, $this->learningMaterialFinder, $this->learningMaterialId);
    }
    public function test_viewLearningMaterial_returnTeamProgramParticipationsViewLearningMaterialResult()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method("viewLearningMaterial")
                ->with($this->learningMaterialFinder, $this->learningMaterialId);
        $this->executeViewLearningMaterial();
    }
    public function test_viewLearningMaterial_recordEvent()
    {
        $learningMaterialViewedByParticipantEvent = $this->buildMockOfClass(LearningMaterialViewedByParticipantEvent::class);
        $this->teamProgramParticipation->expects($this->once())
                ->method("pullRecordedEvents")
                ->willReturn([$learningMaterialViewedByParticipantEvent]);
        $event = new LearningMaterialViewedByTeamMemberEvent($this->member->id, $learningMaterialViewedByParticipantEvent);
        $this->executeViewLearningMaterial();
        $this->assertEquals($event, $this->member->pullRecordedEvents()[0]);
    }
    public function test_viewLearningMaterial_inactiveMember_forbiddenError()
    {
        $this->member->active = false;
        $this->assertInactiveForbiddenError(function (){
            $this->executeViewLearningMaterial();
        });
    }
    
    protected function executeViewMissionComment()
    {
        $this->setManageableTeamParticipant();
        return $this->member->viewMissionComment(
                $this->teamProgramParticipation, $this->missionCommentRepository, $this->missionCommentId);
    }
    public function test_viewMissionComment_returnTeamParticipantsViewMissionCommentResult()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method('viewMissionComment')
                ->with($this->missionCommentRepository, $this->missionCommentId);
        $this->executeViewMissionComment();
    }
    public function test_viewMissionComment_inactiveMember_forbidden()
    {
        $this->member->active = false;
        $this->assertInactiveForbiddenError(function (){
            $this->executeViewMissionComment();
        });
    }
    public function test_viewMissionComment_unamangeTeamParticipant_forbidden()
    {
        $this->setUnmanageableTeamParticipant();
        $this->assertUnmanageTeamParticipant(function(){
            $this->executeViewMissionComment();
        });
    }
    
    protected function executeViewAllMissionComment()
    {
        $this->setManageableTeamParticipant();
        return $this->member->viewAllMissionComments(
                $this->teamProgramParticipation, $this->missionCommentRepository, $this->missionId, $this->page, $this->pageSize);
    }
    public function test_viewAllMissionComment_returnTeamParticipantsViewAllMissionCommentsResult()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method('viewAllMissionComments')
                ->with($this->missionCommentRepository, $this->missionId, $this->page, $this->pageSize);
        $this->executeViewAllMissionComment();
    }
    public function test_viewAllMissionComment_inactiveMember_forbidden()
    {
        $this->member->active = false;
        $this->assertInactiveForbiddenError(function (){
            $this->executeViewAllMissionComment();
        });
    }
    public function test_viewAllMissionComment_unamangeTeamParticipant_forbidden()
    {
        $this->setUnmanageableTeamParticipant();
        $this->assertUnmanageTeamParticipant(function(){
            $this->executeViewAllMissionComment();
        });
    }
    
    protected function executeViewAllMentors()
    {
        $this->setManageableTeamParticipant();
        $this->member->viewAllMentors($this->teamProgramParticipation, $this->mentorRepository, $this->page, $this->pageSize);
    }
    public function test_viewAllMentors_returnTeamParticipantsViewAllMentorsResult()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method('viewAllMentors')
                ->with($this->mentorRepository, $this->page, $this->pageSize);
        $this->executeViewAllMentors();
    }
    public function test_viewAllMentor_inactiveMembers_forbidden()
    {
        $this->member->active = false;
        $this->assertInactiveForbiddenError(function (){
            $this->executeViewAllMentors();
        });
    }
    public function test_viewAllMentor_unmanagedTeamParticipant_forbidden()
    {
        $this->setUnmanageableTeamParticipant();
        $this->assertUnmanageTeamParticipant(function (){
            $this->executeViewAllMentors();
        });
    }
    
    protected function executeViewMentors()
    {
        $this->setManageableTeamParticipant();
        $this->member->viewMentor($this->teamProgramParticipation, $this->mentorRepository, $this->mentorId);
    }
    public function test_viewMentors_returnTeamParticipantsViewMentorsResult()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method('viewMentor')
                ->with($this->mentorRepository, $this->mentorId);
        $this->executeViewMentors();
    }
    public function test_viewMentor_inactiveMembers_forbidden()
    {
        $this->member->active = false;
        $this->assertInactiveForbiddenError(function (){
            $this->executeViewMentors();
        });
    }
    public function test_viewMentor_unmanagedTeamParticipant_forbidden()
    {
        $this->setUnmanageableTeamParticipant();
        $this->assertUnmanageTeamParticipant(function (){
            $this->executeViewMentors();
        });
    }
    
    protected function isActiveMemberCorrespondWithClient()
    {
        return $this->member->isActiveMemberCorrespondWithClient($this->client);
    }
    public function test_isActiveMemberCorrespondWithClient_activeMemberCorrespondToSameClient_returnTrue()
    {
        $this->assertTrue($this->isActiveMemberCorrespondWithClient());
    }
    public function test_isActiveMemberCorrespondWithClient_inactiveMember_returnFalse()
    {
        $this->member->active = false;
        $this->assertFalse($this->isActiveMemberCorrespondWithClient());
    }
    public function test_isActiveMemberCorrespondWithClient_differentClient_returnFalse()
    {
        $this->member->client = $this->buildMockOfClass(Client::class);
        $this->assertFalse($this->isActiveMemberCorrespondWithClient());
    }
    
    public function test_getClientName_returnClientFullName()
    {
        $this->client->expects($this->once())
                ->method('getFullName');
        $this->member->getClientName();
    }
    
}

class TestableMember extends Member
{
    public $team;
    public $id = "memberId";
    public $client;
    public $position;
    public $anAdmin = true;
    public $active = true;
    public $joinTime;
    
    function __construct()
    {
        parent::__construct();
    }
}
