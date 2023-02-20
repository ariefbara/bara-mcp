<?php

namespace Tests\Controllers\Personnel\Coordinator;

use DateTime;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Manager\RecordOfManagerInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\Activity\Invitee\RecordOfInviteeReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Activity\RecordOfInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\ActivityType\RecordOfActivityParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfConsultantInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\Coordinator\RecordOfCoordinatorInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfParticipantInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivity;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivityType;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFeedbackForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfManager;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\RecordOfUser;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;
use Tests\Controllers\RecordPreparation\User\RecordOfUserParticipant;

class ActivityControllerTest extends ExtendedCoordinatorTestCase
{

    protected $allValidParticipantInvitationUri;
    protected $participantInviteeOne;
    protected $participantInviteeTwo;
    //
    protected $activityOne;
    protected $activityParticipant;
    protected $managerInviteeOneA;
    protected $coordinatorInviteeOneB;
    protected $consultantInviteeOneC;
    protected $clientParticipantInviteeOneD;
    protected $teamParticipantInviteeOneE;
    protected $userParticipantInviteeOneF;
    //
    protected $inviteeReportOneB;
    protected $inviteeReportOneD;
    //
    protected $clientParticipantOne;
    protected $teamParticipantTwo;
    protected $userParticipantThree;
    //
    protected $uri;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        
        $this->connection->table('ActivityType')->truncate();
        $this->connection->table('Activity')->truncate();
        $this->connection->table('ActivityParticipant')->truncate();
        //
        $this->connection->table('Manager')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        //
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Participant')->truncate();
        //
        $this->connection->table('Invitee')->truncate();
        $this->connection->table('ManagerInvitee')->truncate();
        $this->connection->table('CoordinatorInvitee')->truncate();
        $this->connection->table('ConsultantInvitee')->truncate();
        $this->connection->table('ParticipantInvitee')->truncate();
        //
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('InviteeReport')->truncate();
        
        $program = $this->coordinator->program;
        
        $participantOne = new RecordOfParticipant($program, 1);
        $participantTwo = new RecordOfParticipant($program, 2);
        $participantThree = new RecordOfParticipant($program, 3);
        
        $activityTypeOne = new RecordOfActivityType($program, 1);
        
        $this->activityOne = new RecordOfActivity($activityTypeOne, 1);
        $activityTwo = new RecordOfActivity($activityTypeOne, 2);
        
        $activityParticipantOne = new RecordOfActivityParticipant($activityTypeOne, null, 1);
        
        $inviteeOne = new RecordOfInvitee($this->activityOne, $activityParticipantOne, 1);
        $inviteeTwo = new RecordOfInvitee($activityTwo, $activityParticipantOne, 2);
        
        $this->participantInviteeOne = new RecordOfParticipantInvitee($participantOne, $inviteeOne);
        $this->participantInviteeTwo = new RecordOfParticipantInvitee($participantOne, $inviteeTwo);
        
        $this->allValidParticipantInvitationUri = $this->coordinatorUri . "/program-participants/{$participantOne->id}/valid-activity-invitations";
        //
        $firm = $program->firm;
        
        $form = new RecordOfForm('main');
        $feedbackForm = new RecordOfFeedbackForm($firm, $form);
        $this->activityParticipant = new RecordOfActivityParticipant($activityTypeOne, $feedbackForm, 'main');
        
        $inviteeOneA = new RecordOfInvitee($this->activityOne, $this->activityParticipant, '1a');
        $inviteeOneB = new RecordOfInvitee($this->activityOne, $this->activityParticipant, '1b');
        $inviteeOneC = new RecordOfInvitee($this->activityOne, $this->activityParticipant, '1c');
        $inviteeOneD = new RecordOfInvitee($this->activityOne, $this->activityParticipant, '1d');
        $inviteeOneE = new RecordOfInvitee($this->activityOne, $this->activityParticipant, '1e');
        $inviteeOneF = new RecordOfInvitee($this->activityOne, $this->activityParticipant, '1f');
        $inviteeOneG = new RecordOfInvitee($this->activityOne, $this->activityParticipant, '1g');
        //
        $formRecordOneB = new RecordOfFormRecord($form, '1b');
        $formRecordOneD = new RecordOfFormRecord($form, '1d');
        //
        $this->inviteeReportOneB = new RecordOfInviteeReport($inviteeOneB, $formRecordOneB);
        $this->inviteeReportOneD = new RecordOfInviteeReport($inviteeOneD, $formRecordOneD);
        
        $managerOne = new RecordOfManager($firm, 1);
        
        $personnelOne = new RecordOfPersonnel($firm, 1);
        $personnelTwo = new RecordOfPersonnel($firm, 2);
        
        $coordinatorOne = new RecordOfCoordinator($program, $personnelOne, 1);
        $consultantOne = new RecordOfConsultant($program, $personnelTwo, 2);
        
        $this->managerInviteeOneA = new RecordOfManagerInvitee($managerOne, $inviteeOneA);
        $this->coordinatorInviteeOneB = new RecordOfCoordinatorInvitee($coordinatorOne, $inviteeOneB);
        $this->consultantInviteeOneC = new RecordOfConsultantInvitee($consultantOne, $inviteeOneC);
        $this->clientParticipantInviteeOneD = new RecordOfParticipantInvitee($participantOne, $inviteeOneD);
        $this->teamParticipantInviteeOneE = new RecordOfParticipantInvitee($participantTwo, $inviteeOneE);
        $this->userParticipantInviteeOneF = new RecordOfParticipantInvitee($participantThree, $inviteeOneF);
        
        //
        $clientOne = new RecordOfClient($firm, 1);
        $this->clientParticipantOne = new RecordOfClientParticipant($clientOne, $participantOne);
        
        $teamOne = new RecordOfTeam($firm, $clientOne, 1);
        $this->teamParticipantTwo = new RecordOfTeamProgramParticipation($teamOne, $participantTwo);
        
        $userOne = new RecordOfUser(1);
        $this->userParticipantThree = new RecordOfUserParticipant($userOne, $participantThree);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        
        $this->connection->table('ActivityType')->truncate();
        $this->connection->table('Activity')->truncate();
        $this->connection->table('ActivityParticipant')->truncate();
        //
        $this->connection->table('Manager')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        //
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Participant')->truncate();
        //
        $this->connection->table('Invitee')->truncate();
        $this->connection->table('ManagerInvitee')->truncate();
        $this->connection->table('CoordinatorInvitee')->truncate();
        $this->connection->table('ConsultantInvitee')->truncate();
        $this->connection->table('ParticipantInvitee')->truncate();
        //
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('InviteeReport')->truncate();
    }
    
    protected function viewAllValidInvitationToParticipant()
    {
        $this->persistCoordinatorDependency();
        
        $this->participantInviteeOne->participant->insert($this->connection);
        
        $this->participantInviteeOne->invitee->activity->activityType->insert($this->connection);
        
        $this->participantInviteeOne->invitee->activity->insert($this->connection);
        $this->participantInviteeTwo->invitee->activity->insert($this->connection);
        
        $this->participantInviteeOne->invitee->activityParticipant->insert($this->connection);
        
        $this->participantInviteeOne->insert($this->connection);
        $this->participantInviteeTwo->insert($this->connection);
        
        $this->get($this->allValidParticipantInvitationUri, $this->coordinator->personnel->token);
    }
    public function test_viewAllValidInvitationToParticipant_200()
    {
$this->disableExceptionHandling();
        $this->viewAllValidInvitationToParticipant();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 2,
            'list' => [
                [
                    'id' => $this->participantInviteeOne->invitee->id,
                    'anInitiator' => $this->participantInviteeOne->invitee->anInitiator,
                    'activity' => [
                        'id' => $this->participantInviteeOne->invitee->activity->id,
                        'name' => $this->participantInviteeOne->invitee->activity->name,
                        'description' => $this->participantInviteeOne->invitee->activity->description,
                        'startTime' => $this->participantInviteeOne->invitee->activity->startDateTime,
                        'endTime' => $this->participantInviteeOne->invitee->activity->endDateTime,
                        'location' => $this->participantInviteeOne->invitee->activity->location,
                        'note' => $this->participantInviteeOne->invitee->activity->note,
                        'cancelled' => $this->participantInviteeOne->invitee->activity->cancelled,
                    ],
                ],
                [
                    'id' => $this->participantInviteeTwo->invitee->id,
                    'anInitiator' => $this->participantInviteeTwo->invitee->anInitiator,
                    'activity' => [
                        'id' => $this->participantInviteeTwo->invitee->activity->id,
                        'name' => $this->participantInviteeTwo->invitee->activity->name,
                        'description' => $this->participantInviteeTwo->invitee->activity->description,
                        'startTime' => $this->participantInviteeTwo->invitee->activity->startDateTime,
                        'endTime' => $this->participantInviteeTwo->invitee->activity->endDateTime,
                        'location' => $this->participantInviteeTwo->invitee->activity->location,
                        'note' => $this->participantInviteeTwo->invitee->activity->note,
                        'cancelled' => $this->participantInviteeTwo->invitee->activity->cancelled,
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewAllValidInvitationToParticipant_excludeCancelledInvitation()
    {
        $this->participantInviteeOne->invitee->cancelled = true;
        
        $this->viewAllValidInvitationToParticipant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 1]);
        $this->seeJsonDoesntContains(['id' => $this->participantInviteeOne->invitee->id]);
        $this->seeJsonContains(['id' => $this->participantInviteeTwo->invitee->id]);
    }
    public function test_viewAllValidInvitationToParticipant_excludeInvitationToOtherParticipant()
    {
        $otherParticipant= new RecordOfParticipant($this->coordinator->program, 'other');
        $otherParticipant->insert($this->connection);
        $this->participantInviteeTwo->participant = $otherParticipant;
        
        $this->viewAllValidInvitationToParticipant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 1]);
        $this->seeJsonContains(['id' => $this->participantInviteeOne->invitee->id]);
        $this->seeJsonDoesntContains(['id' => $this->participantInviteeTwo->invitee->id]);
    }
    public function test_viewAllValidInvitationToParticipant_unmanagedParticipant_belongsInOtherProgram_returnEmptySet()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->participantInviteeOne->participant->program = $otherProgram;
        
        $this->viewAllValidInvitationToParticipant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 0]);
        $this->seeJsonDoesntContains(['id' => $this->participantInviteeOne->invitee->id]);
        $this->seeJsonDoesntContains(['id' => $this->participantInviteeTwo->invitee->id]);
    }
    public function test_viewAllValidInvitationToParticipant_fromFilter()
    {
        $this->participantInviteeOne->invitee->activity->startDateTime = (new DateTime('-25 hours'))->format('Y-m-d H:i:s');
        $this->participantInviteeOne->invitee->activity->endDateTime = (new DateTime('-24 hours'))->format('Y-m-d H:i:s');
        
        $time = (new DateTime())->format('Y-m-d H:i:s');
        $this->allValidParticipantInvitationUri .= "?from=$time";
        
        $this->viewAllValidInvitationToParticipant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 1]);
        $this->seeJsonDoesntContains(['id' => $this->participantInviteeOne->invitee->id]);
        $this->seeJsonContains(['id' => $this->participantInviteeTwo->invitee->id]);
    }
    public function test_viewAllValidInvitationToParticipant_toFilter()
    {
        $this->participantInviteeOne->invitee->activity->startDateTime = (new DateTime('-25 hours'))->format('Y-m-d H:i:s');
        $this->participantInviteeOne->invitee->activity->endDateTime = (new DateTime('-24 hours'))->format('Y-m-d H:i:s');
        
        $time = (new DateTime())->format('Y-m-d H:i:s');
        $this->allValidParticipantInvitationUri .= "?to=$time";
        
        $this->viewAllValidInvitationToParticipant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 1]);
        $this->seeJsonContains(['id' => $this->participantInviteeOne->invitee->id]);
        $this->seeJsonDoesntContains(['id' => $this->participantInviteeTwo->invitee->id]);
    }
    public function test_viewAllValidInvitationToParticipant_setOrder()
    {
        $this->participantInviteeOne->invitee->activity->startDateTime = (new DateTime('-25 hours'))->format('Y-m-d H:i:s');
        $this->participantInviteeOne->invitee->activity->endDateTime = (new DateTime('-24 hours'))->format('Y-m-d H:i:s');
        
        $this->allValidParticipantInvitationUri .= "?order=ASC&pageSize=1";
        
        $this->viewAllValidInvitationToParticipant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 2]);
        $this->seeJsonContains(['id' => $this->participantInviteeOne->invitee->id]);
        $this->seeJsonDoesntContains(['id' => $this->participantInviteeTwo->invitee->id]);
    }
    public function test_viewAllValidInvitationToParticipant_inactiveCoordinator_403()
    {
        $this->coordinator->active = false;
        
        $this->viewAllValidInvitationToParticipant();
        $this->seeStatusCode(403);
    }
    
    protected function viewParticipantInvitationDetail()
    {
        $this->persistCoordinatorDependency();
        
        $this->participantInviteeOne->participant->insert($this->connection);
        $this->participantInviteeOne->invitee->activity->activityType->insert($this->connection);
        $this->participantInviteeOne->invitee->activity->insert($this->connection);
        $this->participantInviteeOne->invitee->activityParticipant->insert($this->connection);
        $this->participantInviteeOne->insert($this->connection);
        
        $uri = $this->coordinatorUri . "/participant-activity-invitations/{$this->participantInviteeOne->invitee->id}";
        $this->get($uri, $this->coordinator->personnel->token);
    }
    public function test_viewParticipantInvitationDetail_200()
    {
$this->disableExceptionHandling();
        $this->viewParticipantInvitationDetail();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->participantInviteeOne->invitee->id,
            'anInitiator' => $this->participantInviteeOne->invitee->anInitiator,
            'activity' => [
                'id' => $this->participantInviteeOne->invitee->activity->id,
                'name' => $this->participantInviteeOne->invitee->activity->name,
                'description' => $this->participantInviteeOne->invitee->activity->description,
                'startTime' => $this->participantInviteeOne->invitee->activity->startDateTime,
                'endTime' => $this->participantInviteeOne->invitee->activity->endDateTime,
                'location' => $this->participantInviteeOne->invitee->activity->location,
                'note' => $this->participantInviteeOne->invitee->activity->note,
                'cancelled' => $this->participantInviteeOne->invitee->activity->cancelled,
            ],
            'activityParticipant' => [
                'id' => $this->participantInviteeOne->invitee->activityParticipant->id,
                'reportForm' => null,
            ],
            'report' => null,
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewParticipantInvitationDetail_inactiveCoordinator_403()
    {
        $this->coordinator->active = false;
        
        $this->viewParticipantInvitationDetail();
        $this->seeStatusCode(403);
    }
    public function test_viewParticipantInvitationDetail_unmanagedInvitation_participantBelongsToOtherProgram_404()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->participantInviteeOne->participant->program = $otherProgram;
        
        $this->viewParticipantInvitationDetail();
        $this->seeStatusCode(404);
    }
    
    //
    protected function viewActivityDetail()
    {
        $this->persistCoordinatorDependency();
        
        $this->activityOne->activityType->insert($this->connection);
        $this->activityOne->insert($this->connection);
        
        //
        $this->activityParticipant->feedbackForm->insert($this->connection);
        $this->activityParticipant->insert($this->connection);
        
        $this->coordinatorInviteeOneB->coordinator->personnel->insert($this->connection);
        $this->consultantInviteeOneC->consultant->personnel->insert($this->connection);
        
        $this->managerInviteeOneA->manager->insert($this->connection);
        $this->coordinatorInviteeOneB->coordinator->insert($this->connection);
        $this->consultantInviteeOneC->consultant->insert($this->connection);
        
        $this->managerInviteeOneA->insert($this->connection);
        $this->coordinatorInviteeOneB->insert($this->connection);
        $this->consultantInviteeOneC->insert($this->connection);
        $this->clientParticipantInviteeOneD->insert($this->connection);
        $this->teamParticipantInviteeOneE->insert($this->connection);
        $this->userParticipantInviteeOneF->insert($this->connection);
        //
        $this->inviteeReportOneB->insert($this->connection);
        $this->inviteeReportOneD->insert($this->connection);
        //
        $this->clientParticipantOne->client->insert($this->connection);
        $this->teamParticipantTwo->team->insert($this->connection);
        $this->userParticipantThree->user->insert($this->connection);
        
        $this->clientParticipantOne->insert($this->connection);
        $this->teamParticipantTwo->insert($this->connection);
        $this->userParticipantThree->insert($this->connection);
        
        $uri = $this->coordinatorUri . "/activities/{$this->activityOne->id}";
        $this->get($uri, $this->coordinator->personnel->token);
//echo $uri;
//$this->seeJsonContains(['print']);
    }
    public function test_viewActivityDetal_200()
    {
$this->disableExceptionHandling();
        $this->viewActivityDetail();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains([
            'id' => $this->activityOne->id,
            'cancelled' => $this->activityOne->cancelled,
            'name' => $this->activityOne->name,
            'description' => $this->activityOne->description,
            'startTime' => $this->activityOne->startDateTime,
            'endTime' => $this->activityOne->endDateTime,
            'location' => $this->activityOne->location,
            'note' => $this->activityOne->note,
            'createdTime' => $this->activityOne->createdTime,
            'inviteeList' => [
                [
                    'id' => $this->managerInviteeOneA->invitee->id,
                    'cancelled' => $this->managerInviteeOneA->invitee->cancelled,
                    'anInitiator' => $this->managerInviteeOneA->invitee->anInitiator,
                    'reportForm' => [
                        "name" => $this->activityParticipant->feedbackForm->form->name,
                        "description" => $this->activityParticipant->feedbackForm->form->description,
                        "stringFields" => [],
                        "integerFields" => [],
                        "textAreaFields" => [],
                        "attachmentFields" => [],
                        "singleSelectFields" => [],
                        "multiSelectFields" => [],
                        "sections" => [],
                    ],
                    'report' => null,
                    'manager' => [
                        'id' => $this->managerInviteeOneA->manager->id,
                        'name' => $this->managerInviteeOneA->manager->name,
                    ],
                    'coordinator' => null,
                    'consultant' => null,
                    'participant' => null,
                ],
                [
                    'id' => $this->coordinatorInviteeOneB->invitee->id,
                    'cancelled' => $this->coordinatorInviteeOneB->invitee->cancelled,
                    'anInitiator' => $this->coordinatorInviteeOneB->invitee->anInitiator,
                    'reportForm' => [
                        "name" => $this->activityParticipant->feedbackForm->form->name,
                        "description" => $this->activityParticipant->feedbackForm->form->description,
                        "stringFields" => [],
                        "integerFields" => [],
                        "textAreaFields" => [],
                        "attachmentFields" => [],
                        "singleSelectFields" => [],
                        "multiSelectFields" => [],
                        "sections" => [],
                    ],
                    'report' => [
                        "submitTime" => $this->inviteeReportOneB->formRecord->submitTime,
                        "stringFieldRecords" => [],
                        "integerFieldRecords" => [],
                        "textAreaFieldRecords" => [],
                        "attachmentFieldRecords" => [],
                        "singleSelectFieldRecords" => [],
                        "multiSelectFieldRecords" => [],
                    ],
                    'manager' => null,
                    'coordinator' => [
                        'id' => $this->coordinatorInviteeOneB->coordinator->id,
                        'personnel' => [
                            'id' => $this->coordinatorInviteeOneB->coordinator->personnel->id,
                            'name' => $this->coordinatorInviteeOneB->coordinator->personnel->getFullName(),
                        ],
                    ],
                    'consultant' => null,
                    'participant' => null,
                ],
                [
                    'id' => $this->consultantInviteeOneC->invitee->id,
                    'cancelled' => $this->consultantInviteeOneC->invitee->cancelled,
                    'anInitiator' => $this->consultantInviteeOneC->invitee->anInitiator,
                    'reportForm' => [
                        "name" => $this->activityParticipant->feedbackForm->form->name,
                        "description" => $this->activityParticipant->feedbackForm->form->description,
                        "stringFields" => [],
                        "integerFields" => [],
                        "textAreaFields" => [],
                        "attachmentFields" => [],
                        "singleSelectFields" => [],
                        "multiSelectFields" => [],
                        "sections" => [],
                    ],
                    'report' => null,
                    'manager' => null,
                    'coordinator' => null,
                    'consultant' => [
                        'id' => $this->consultantInviteeOneC->consultant->id,
                        'personnel' => [
                            'id' => $this->consultantInviteeOneC->consultant->personnel->id,
                            'name' => $this->consultantInviteeOneC->consultant->personnel->getFullName(),
                        ],
                    ],
                    'participant' => null,
                ],
                [
                    'id' => $this->clientParticipantInviteeOneD->invitee->id,
                    'cancelled' => $this->clientParticipantInviteeOneD->invitee->cancelled,
                    'anInitiator' => $this->clientParticipantInviteeOneD->invitee->anInitiator,
                    'reportForm' => [
                        "name" => $this->activityParticipant->feedbackForm->form->name,
                        "description" => $this->activityParticipant->feedbackForm->form->description,
                        "stringFields" => [],
                        "integerFields" => [],
                        "textAreaFields" => [],
                        "attachmentFields" => [],
                        "singleSelectFields" => [],
                        "multiSelectFields" => [],
                        "sections" => [],
                    ],
                    'report' => [
                        "submitTime" => $this->inviteeReportOneB->formRecord->submitTime,
                        "stringFieldRecords" => [],
                        "integerFieldRecords" => [],
                        "textAreaFieldRecords" => [],
                        "attachmentFieldRecords" => [],
                        "singleSelectFieldRecords" => [],
                        "multiSelectFieldRecords" => [],
                    ],
                    'manager' => null,
                    'coordinator' => null,
                    'consultant' => null,
                    'participant' => [
                        'id' => $this->clientParticipantInviteeOneD->participant->id,
                        'client' => [
                            'id' => $this->clientParticipantOne->client->id,
                            'name' => $this->clientParticipantOne->client->getFullName(),
                        ],
                        'team' => null,
                        'user' => null,
                    ],
                ],
                [
                    'id' => $this->teamParticipantInviteeOneE->invitee->id,
                    'cancelled' => $this->teamParticipantInviteeOneE->invitee->cancelled,
                    'anInitiator' => $this->teamParticipantInviteeOneE->invitee->anInitiator,
                    'reportForm' => [
                        "name" => $this->activityParticipant->feedbackForm->form->name,
                        "description" => $this->activityParticipant->feedbackForm->form->description,
                        "stringFields" => [],
                        "integerFields" => [],
                        "textAreaFields" => [],
                        "attachmentFields" => [],
                        "singleSelectFields" => [],
                        "multiSelectFields" => [],
                        "sections" => [],
                    ],
                    'report' => null,
                    'manager' => null,
                    'coordinator' => null,
                    'consultant' => null,
                    'participant' => [
                        'id' => $this->teamParticipantInviteeOneE->participant->id,
                        'client' => null,
                        'team' => [
                            'id' => $this->teamParticipantTwo->team->id,
                            'name' => $this->teamParticipantTwo->team->name,
                        ],
                        'user' => null,
                    ],
                ],
                [
                    'id' => $this->userParticipantInviteeOneF->invitee->id,
                    'cancelled' => $this->userParticipantInviteeOneF->invitee->cancelled,
                    'anInitiator' => $this->userParticipantInviteeOneF->invitee->anInitiator,
                    'reportForm' => [
                        "name" => $this->activityParticipant->feedbackForm->form->name,
                        "description" => $this->activityParticipant->feedbackForm->form->description,
                        "stringFields" => [],
                        "integerFields" => [],
                        "textAreaFields" => [],
                        "attachmentFields" => [],
                        "singleSelectFields" => [],
                        "multiSelectFields" => [],
                        "sections" => [],
                    ],
                    'report' => null,
                    'manager' => null,
                    'coordinator' => null,
                    'consultant' => null,
                    'participant' => [
                        'id' => $this->userParticipantInviteeOneF->participant->id,
                        'client' => null,
                        'team' => null,
                        'user' => [
                            'id' => $this->userParticipantThree->user->id,
                            'name' => $this->userParticipantThree->user->getFullName(),
                        ],
                    ],
                ],
            ],
        ]);
    }
    public function test_viewActivityDetail_excludeCancelledInvitationFromList()
    {
        $this->consultantInviteeOneC->invitee->cancelled = true;
        $this->viewActivityDetail();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['id' => $this->managerInviteeOneA->invitee->id]);
        $this->seeJsonContains(['id' => $this->coordinatorInviteeOneB->invitee->id]);
        $this->seeJsonDoesntContains(['id' => $this->consultantInviteeOneC->invitee->id]);
        $this->seeJsonContains(['id' => $this->clientParticipantInviteeOneD->invitee->id]);
        $this->seeJsonContains(['id' => $this->teamParticipantInviteeOneE->invitee->id]);
        $this->seeJsonContains(['id' => $this->userParticipantInviteeOneF->invitee->id]);
    }

}
