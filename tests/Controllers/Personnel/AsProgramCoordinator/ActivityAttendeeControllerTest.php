<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

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
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfIntegerField;
use Tests\Controllers\RecordPreparation\Shared\FormRecord\RecordOfIntegerFieldRecord;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class ActivityAttendeeControllerTest extends AsProgramCoordinatorTestCase
{
    protected $showAllAttendeesUri;
    protected $showAttendeeUri;
    protected $activityOne;
    protected $feedbackFormOne;
    protected $integerFieldOne;
    protected $activityParticipantOne;
    protected $activityParticipantTwo;
    protected $activityParticipantThree;
    protected $activityParticipantFour;
    protected $clientParticipant;
    protected $managerAttendee;
    protected $coordinatorAttendee;
    protected $consultantAttendee;
    protected $participantAttendee;
    protected $participantAttendeeReport;
    protected $integerFieldRecord;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Coordinator')->truncate();
        $this->connection->table('ActivityType')->truncate();
        $this->connection->table('Activity')->truncate();
        $this->connection->table('Manager')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('IntegerField')->truncate();
        $this->connection->table('ActivityParticipant')->truncate();
        $this->connection->table('Invitee')->truncate();
        $this->connection->table('ManagerInvitee')->truncate();
        $this->connection->table('CoordinatorInvitee')->truncate();
        $this->connection->table('ConsultantInvitee')->truncate();
        $this->connection->table('ParticipantInvitee')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('InviteeReport')->truncate();
        $this->connection->table('IntegerFieldRecord')->truncate();
        
        $program = $this->coordinator->program;
        $firm = $program->firm;

        $activityTypeOne = new RecordOfActivityType($program, '1');

        $this->activityOne = new RecordOfActivity($activityTypeOne, '1');

        $managerOne = new RecordOfManager($firm, '1');
        
        $personnelOne = new RecordOfPersonnel($firm, '1');
        $personnelTwo = new RecordOfPersonnel($firm, '2');
        
        $clientOne = new RecordOfClient($firm, '1');
        
        $coordinatorOne = new RecordOfCoordinator($program, $personnelOne, '1');
        
        $consultantOne = new RecordOfConsultant($program, $personnelTwo, '1');
        
        $participantOne = new RecordOfParticipant($program, '1');
        
        $this->clientParticipant = new RecordOfClientParticipant($clientOne, $participantOne);
        
        $formOne = new RecordOfForm('1');
        
        $this->feedbackFormOne = new RecordOfFeedbackForm($firm, $formOne);
        
        $this->integerFieldOne = new RecordOfIntegerField($formOne, '1');
        
        $this->activityParticipantOne = new RecordOfActivityParticipant($activityTypeOne, $this->feedbackFormOne, '1');
        $this->activityParticipantOne->participantType = 'manager';
        $this->activityParticipantTwo = new RecordOfActivityParticipant($activityTypeOne, $this->feedbackFormOne, '2');
        $this->activityParticipantTwo->participantType = 'coordinator';
        $this->activityParticipantThree = new RecordOfActivityParticipant($activityTypeOne, $this->feedbackFormOne, '3');
        $this->activityParticipantThree->participantType = 'consultant';
        $this->activityParticipantFour = new RecordOfActivityParticipant($activityTypeOne, $this->feedbackFormOne, '4');
        $this->activityParticipantFour->participantType = 'participant';
        
        $inviteeOne = new RecordOfInvitee($this->activityOne, $this->activityParticipantOne, '1');
        $inviteeTwo = new RecordOfInvitee($this->activityOne, $this->activityParticipantTwo, '2');
        $inviteeThree = new RecordOfInvitee($this->activityOne, $this->activityParticipantThree, '3');
        $inviteeFour = new RecordOfInvitee($this->activityOne, $this->activityParticipantFour, '4');

        $this->managerAttendee = new RecordOfManagerInvitee($managerOne, $inviteeOne);
        
        $this->coordinatorAttendee = new RecordOfCoordinatorInvitee($coordinatorOne, $inviteeTwo);
        
        $this->consultantAttendee = new RecordOfConsultantInvitee($consultantOne, $inviteeThree);
        
        $this->participantAttendee = new RecordOfParticipantInvitee($participantOne, $inviteeFour);
        
        $formRecordFour = new RecordOfFormRecord($this->feedbackFormOne->form, '4');
        
        $this->participantAttendeeReport = new RecordOfInviteeReport($this->participantAttendee->invitee, $formRecordFour);
        
        $this->integerFieldRecord = new RecordOfIntegerFieldRecord($formRecordFour, $this->integerFieldOne, '1');
        
        $this->showAllAttendeesUri = $this->asProgramCoordinatorUri . "/activities/{$this->activityOne->id}/attendees";
        $this->showAttendeeUri = $this->asProgramCoordinatorUri . "/attendees/{$this->participantAttendee->invitee->id}";
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Coordinator')->truncate();
        $this->connection->table('ActivityType')->truncate();
        $this->connection->table('Activity')->truncate();
        $this->connection->table('Manager')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('IntegerField')->truncate();
        $this->connection->table('ActivityParticipant')->truncate();
        $this->connection->table('Invitee')->truncate();
        $this->connection->table('ManagerInvitee')->truncate();
        $this->connection->table('CoordinatorInvitee')->truncate();
        $this->connection->table('ConsultantInvitee')->truncate();
        $this->connection->table('ParticipantInvitee')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('InviteeReport')->truncate();
        $this->connection->table('IntegerFieldRecord')->truncate();
    }
    
    protected function showAll()
    {
        $this->coordinator->insert($this->connection);
        
        $this->activityOne->activityType->insert($this->connection);
        $this->activityOne->insert($this->connection);
        
        $this->feedbackFormOne->insert($this->connection);
        $this->integerFieldOne->insert($this->connection);
        
        $this->activityParticipantOne->insert($this->connection);
        $this->activityParticipantTwo->insert($this->connection);
        $this->activityParticipantThree->insert($this->connection);
        $this->activityParticipantFour->insert($this->connection);
        
        $this->coordinatorAttendee->coordinator->personnel->insert($this->connection);
        $this->consultantAttendee->consultant->personnel->insert($this->connection);
        $this->clientParticipant->client->insert($this->connection);
        
        $this->managerAttendee->manager->insert($this->connection);
        $this->coordinatorAttendee->coordinator->insert($this->connection);
        $this->consultantAttendee->consultant->insert($this->connection);
        $this->clientParticipant->insert($this->connection);
        
        $this->managerAttendee->insert($this->connection);
        $this->coordinatorAttendee->insert($this->connection);
        $this->consultantAttendee->insert($this->connection);
        $this->participantAttendee->insert($this->connection);
        
        $this->participantAttendeeReport->insert($this->connection);
        $this->integerFieldRecord->insert($this->connection);
        
        $this->get($this->showAllAttendeesUri, $this->coordinator->personnel->token);
    }
    public function test_showAll_200()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        
        $managerAttendeeResponse = [
            'id' => $this->managerAttendee->invitee->id,
            'anInitiator' => $this->managerAttendee->invitee->anInitiator,
            'manager' => [
                'id' => $this->managerAttendee->manager->id,
                'name' => $this->managerAttendee->manager->name,
            ],
            'coordinator' => null,
            'consultant' => null,
            'participant' => null,
            'report' => null,
        ];
        $this->seeJsonContains($managerAttendeeResponse);
        
        $coordinatorAttendeeResponse = [
            'id' => $this->coordinatorAttendee->invitee->id,
            'anInitiator' => $this->coordinatorAttendee->invitee->anInitiator,
            'manager' => null,
            'coordinator' => [
                'id' => $this->coordinatorAttendee->coordinator->id,
                'name' => $this->coordinatorAttendee->coordinator->personnel->getFullName(),
            ],
            'consultant' => null,
            'participant' => null,
            'report' => null,
        ];
        $this->seeJsonContains($coordinatorAttendeeResponse);
        
        $consultantAttendeeResponse = [
            'id' => $this->consultantAttendee->invitee->id,
            'anInitiator' => $this->consultantAttendee->invitee->anInitiator,
            'manager' => null,
            'coordinator' => null,
            'consultant' => [
                'id' => $this->consultantAttendee->consultant->id,
                'name' => $this->consultantAttendee->consultant->personnel->getFullName(),
            ],
            'participant' => null,
            'report' => null,
        ];
        $this->seeJsonContains($consultantAttendeeResponse);
        
        $participantAttendeeResponse = [
            'id' => $this->participantAttendee->invitee->id,
            'anInitiator' => $this->participantAttendee->invitee->anInitiator,
            'manager' => null,
            'coordinator' => null,
            'consultant' => null,
            'participant' => [
                'id' => $this->participantAttendee->participant->id,
                'name' => $this->clientParticipant->client->getFullName(),
            ],
            'report' => [
                'submitTime' => $this->participantAttendeeReport->formRecord->submitTime,
                'stringFieldRecords' => [],
                'integerFieldRecords' => [
                    [
                        'id' => $this->integerFieldRecord->id,
                        'value' => $this->integerFieldRecord->value,
                        'integerField' => [
                            'id' => $this->integerFieldRecord->integerField->id,
                            'name' => $this->integerFieldRecord->integerField->name,
                            'position' => $this->integerFieldRecord->integerField->position,
                        ],
                    ],
                ],
                'textAreaFieldRecords' => [],
                'attachmentFieldRecords' => [],
                'singleSelectFieldRecords' => [],
                'multiSelectFieldRecords' => [],
            ],
        ];
        $this->seeJsonContains($participantAttendeeResponse);
    }
    public function test_showAll_inactiveCoordinator_403()
    {
        $this->coordinator->active = false;
        $this->showAll();
        $this->seeStatusCode(403);
    }
    
    protected function show()
    {
        $this->coordinator->insert($this->connection);
        
        $this->activityOne->activityType->insert($this->connection);
        $this->activityOne->insert($this->connection);
        
        $this->feedbackFormOne->insert($this->connection);
        $this->integerFieldOne->insert($this->connection);
        
        $this->activityParticipantFour->insert($this->connection);
        
        $this->clientParticipant->client->insert($this->connection);
        
        $this->clientParticipant->insert($this->connection);
        
        $this->participantAttendee->insert($this->connection);
        
        $this->participantAttendeeReport->insert($this->connection);
        $this->integerFieldRecord->insert($this->connection);
        
        $this->get($this->showAllAttendeesUri, $this->coordinator->personnel->token);
        
        $this->get($this->showAttendeeUri, $this->coordinator->personnel->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->participantAttendee->invitee->id,
            'anInitiator' => $this->participantAttendee->invitee->anInitiator,
            'manager' => null,
            'coordinator' => null,
            'consultant' => null,
            'participant' => [
                'id' => $this->participantAttendee->participant->id,
                'name' => $this->clientParticipant->client->getFullName(),
            ],
            'report' => [
                'submitTime' => $this->participantAttendeeReport->formRecord->submitTime,
                'stringFieldRecords' => [],
                'integerFieldRecords' => [
                    [
                        'id' => $this->integerFieldRecord->id,
                        'value' => $this->integerFieldRecord->value,
                        'integerField' => [
                            'id' => $this->integerFieldRecord->integerField->id,
                            'name' => $this->integerFieldRecord->integerField->name,
                            'position' => $this->integerFieldRecord->integerField->position,
                        ],
                    ],
                ],
                'textAreaFieldRecords' => [],
                'attachmentFieldRecords' => [],
                'singleSelectFieldRecords' => [],
                'multiSelectFieldRecords' => [],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_show_inactiveCoordinator_403()
    {
        $this->coordinator->active = false;
        $this->show();
        $this->seeStatusCode(403);
    }

}
