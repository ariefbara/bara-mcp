<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Manager\RecordOfManagerInvitee;
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
use Tests\Controllers\RecordPreparation\Firm\RecordOfManager;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;

class ActivityControllerTest extends AsProgramCoordinatorTestCase
{
    protected $activityUri;
    
    protected $activityOne;
    protected $activityTwo;
    
    protected $activityParticipantOne;
    protected $activityParticipantTwp;
    
    protected $managerInvitee_11_a1;
    protected $coordinatorInvitee_12_a1;
    protected $consultantInvitee_21_a2;
    protected $participantInvitee_22_a2;
    protected $clientParticipant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->activityUri = $this->asProgramCoordinatorUri . "/activities";
        
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
        $this->connection->table("Manager")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("Client")->truncate();
        
        $program = $this->coordinator->program;
        $firm = $program->firm;
        
        $activityTypeOne = new RecordOfActivityType($program, 1);
        $activityTypeTwo = new RecordOfActivityType($program, 2);
        
        $this->activityOne = new RecordOfActivity($activityTypeOne, 1);
        $this->activityTwo = new RecordOfActivity($activityTypeTwo, 2);
        
        $this->activityParticipantOne = new RecordOfActivityParticipant($activityTypeOne, null, '1');
        $this->activityParticipantTwo = new RecordOfActivityParticipant($activityTypeTwo, null, '2');
        
        $managerOne = new RecordOfManager($firm, '1');
        
        $personnelOne = new RecordOfPersonnel($firm, '1');
        $personnelTwo = new RecordOfPersonnel($firm, '2');
        
        $coordinatorOne = new RecordOfCoordinator($program, $personnelOne, '1');
        
        $consultantOne = new RecordOfConsultant($program, $personnelTwo, 1);
        
        $participantOne = new RecordOfParticipant($program, '1');
        
        $clientOne = new RecordOfClient($firm, '1'); 
        $this->clientParticipant = new RecordOfClientParticipant($clientOne, $participantOne);
        
        $invitee_11_a1 = new RecordOfInvitee($this->activityOne, $this->activityParticipantOne, '11');
        $invitee_11_a1->anInitiator = true;
        $invitee_12_a1 = new RecordOfInvitee($this->activityOne, $this->activityParticipantOne, '12');
        $invitee_21_a2 = new RecordOfInvitee($this->activityTwo, $this->activityParticipantTwo, '21');
        $invitee_21_a2->anInitiator = true;
        $invitee_22_a2 = new RecordOfInvitee($this->activityTwo, $this->activityParticipantTwo, '22');
        
        $this->managerInvitee_11_a1 = new RecordOfManagerInvitee($managerOne, $invitee_11_a1);
        $this->coordinatorInvitee_12_a1 = new RecordOfCoordinatorInvitee($coordinatorOne, $invitee_12_a1);
        $this->consultantInvitee_21_a2 = new RecordOfConsultantInvitee($consultantOne, $invitee_21_a2);
        $this->participantInvitee_22_a2 = new RecordOfParticipantInvitee($participantOne, $invitee_22_a2);
        
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
        $this->connection->table("Manager")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("Client")->truncate();
    }
    
    protected function show()
    {
        $this->activityOne->activityType->insert($this->connection);
        $this->activityOne->insert($this->connection);
        
        $uri = $this->activityUri . "/{$this->activityOne->id}";
        $this->get($uri, $this->personnel->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            "id" => $this->activityOne->id,
            "name" => $this->activityOne->name,
            "description" => $this->activityOne->description,
            "startTime" => $this->activityOne->startDateTime,
            "endTime" => $this->activityOne->endDateTime,
            "location" => $this->activityOne->location,
            "note" => $this->activityOne->note,
            "cancelled" => $this->activityOne->cancelled,
            "createdTime" => $this->activityOne->createdTime,
        ];
        $this->seeJsonContains($response);
    }
    
    protected function showAll()
    {
        $this->activityOne->activityType->insert($this->connection);
        $this->activityTwo->activityType->insert($this->connection);
        
        $this->activityOne->insert($this->connection);
        $this->activityTwo->insert($this->connection);
        
        $this->activityParticipantOne->insert($this->connection);
        $this->activityParticipantTwo->insert($this->connection);
        
        $this->managerInvitee_11_a1->manager->insert($this->connection);
        $this->managerInvitee_11_a1->insert($this->connection);
        
        $this->coordinatorInvitee_12_a1->coordinator->personnel->insert($this->connection);
        $this->coordinatorInvitee_12_a1->coordinator->insert($this->connection);
        $this->coordinatorInvitee_12_a1->insert($this->connection);
        
        $this->consultantInvitee_21_a2->consultant->personnel->insert($this->connection);
        $this->consultantInvitee_21_a2->consultant->insert($this->connection);
        $this->consultantInvitee_21_a2->insert($this->connection);
        
        $this->participantInvitee_22_a2->insert($this->connection);
        $this->clientParticipant->client->insert($this->connection);
        $this->clientParticipant->insert($this->connection);
        
        $this->get($this->activityUri, $this->personnel->token);
    }
    public function test_showAll_200()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 2,
            'list' => [
                [
                    'id' => $this->activityOne->id,
                    'name' => $this->activityOne->name,
                    'description' => $this->activityOne->description,
                    'location' => $this->activityOne->location,
                    'note' => $this->activityOne->note,
                    'cancelled' => strval(intval($this->activityOne->cancelled)),
                    'createdTime' => $this->activityOne->createdTime,
                    'startTime' => $this->activityOne->startDateTime,
                    'endTime' => $this->activityOne->endDateTime,
                    'activityTypeId' => $this->activityOne->activityType->id,
                    'activityTypeName' => $this->activityOne->activityType->name,
                    'managerId' => $this->managerInvitee_11_a1->manager->id,
                    'coordinatorId' => null,
                    'consultantId' => null,
                    'participantId' => null,
                    'initiatorName' => $this->managerInvitee_11_a1->manager->name,
                ],
                [
                    'id' => $this->activityTwo->id,
                    'name' => $this->activityTwo->name,
                    'description' => $this->activityTwo->description,
                    'location' => $this->activityTwo->location,
                    'note' => $this->activityTwo->note,
                    'cancelled' => strval(intval($this->activityTwo->cancelled)),
                    'createdTime' => $this->activityTwo->createdTime,
                    'startTime' => $this->activityTwo->startDateTime,
                    'endTime' => $this->activityTwo->endDateTime,
                    'activityTypeId' => $this->activityTwo->activityType->id,
                    'activityTypeName' => $this->activityTwo->activityType->name,
                    'managerId' => null,
                    'coordinatorId' => null,
                    'consultantId' => $this->consultantInvitee_21_a2->consultant->id,
                    'participantId' => null,
                    'initiatorName' => $this->consultantInvitee_21_a2->consultant->personnel->getFullName(),
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_filterFrom()
    {
        $this->activityOne->startDateTime = (new DateTimeImmutable('+24 hours'))->format('Y-m-d H:i:s');
        $this->activityOne->endDateTime = (new DateTimeImmutable('+25 hours'))->format('Y-m-d H:i:s');
        $this->activityTwo->startDateTime = (new DateTimeImmutable('-24 hours'))->format('Y-m-d H:i:s');
        $this->activityTwo->endDateTime = (new DateTimeImmutable('-23 hours'))->format('Y-m-d H:i:s');
        
        $this->activityUri .= "?from=" . (new DateTimeImmutable('+12 hours'))->format('Y-m-d H:i:s');
        $this->showAll();
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        
        $activityOneResponse = [
            'id' => $this->activityOne->id,
            'startTime' => $this->activityOne->startDateTime,
            'endTime' => $this->activityOne->endDateTime,
            'managerId' => $this->managerInvitee_11_a1->manager->id,
            'initiatorName' => $this->managerInvitee_11_a1->manager->name,
        ];
        $this->seeJsonContains($activityOneResponse);
        
        $activityTwoResponse = [
            'id' => $this->activityTwo->id,
            'startTime' => $this->activityTwo->startDateTime,
            'endTime' => $this->activityTwo->endDateTime,
            'consultantId' => $this->consultantInvitee_21_a2->consultant->id,
            'initiatorName' => $this->consultantInvitee_21_a2->consultant->personnel->getFullName(),
        ];
        $this->seeJsonDoesntContains($activityTwoResponse);
    }
    public function test_showAll_filterTo()
    {
        $this->activityOne->startDateTime = (new DateTimeImmutable('+24 hours'))->format('Y-m-d H:i:s');
        $this->activityOne->endDateTime = (new DateTimeImmutable('+25 hours'))->format('Y-m-d H:i:s');
        $this->activityTwo->startDateTime = (new DateTimeImmutable('-24 hours'))->format('Y-m-d H:i:s');
        $this->activityTwo->endDateTime = (new DateTimeImmutable('-23 hours'))->format('Y-m-d H:i:s');
        
        $this->activityUri .= "?to=" . (new DateTimeImmutable('+12 hours'))->format('Y-m-d H:i:s');
        $this->showAll();
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        
        $activityOneResponse = [
            'id' => $this->activityOne->id,
            'startTime' => $this->activityOne->startDateTime,
            'endTime' => $this->activityOne->endDateTime,
            'managerId' => $this->managerInvitee_11_a1->manager->id,
            'initiatorName' => $this->managerInvitee_11_a1->manager->name,
        ];
        $this->seeJsonDoesntContains($activityOneResponse);
        
        $activityTwoResponse = [
            'id' => $this->activityTwo->id,
            'startTime' => $this->activityTwo->startDateTime,
            'endTime' => $this->activityTwo->endDateTime,
            'consultantId' => $this->consultantInvitee_21_a2->consultant->id,
            'initiatorName' => $this->consultantInvitee_21_a2->consultant->personnel->getFullName(),
        ];
        $this->seeJsonContains($activityTwoResponse);
    }
    public function test_showAll_filterActivityTypeIdList()
    {
        $this->activityUri .= "?activityTypeIdList[]={$this->activityOne->activityType->id}";
        $this->showAll();
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        
        $activityOneResponse = [
            'id' => $this->activityOne->id,
            'managerId' => $this->managerInvitee_11_a1->manager->id,
            'initiatorName' => $this->managerInvitee_11_a1->manager->name,
        ];
        $this->seeJsonContains($activityOneResponse);
        
        $activityTwoResponse = [
            'id' => $this->activityTwo->id,
            'consultantId' => $this->consultantInvitee_21_a2->consultant->id,
            'initiatorName' => $this->consultantInvitee_21_a2->consultant->personnel->getFullName(),
        ];
        $this->seeJsonDoesntContains($activityTwoResponse);
    }
    public function test_showAll_filterCancelledStatus()
    {
        $this->activityTwo->cancelled = true;
        
        $this->activityUri .= "?cancelledStatus=false";
        $this->showAll();
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        
        $activityOneResponse = [
            'id' => $this->activityOne->id,
            'managerId' => $this->managerInvitee_11_a1->manager->id,
            'initiatorName' => $this->managerInvitee_11_a1->manager->name,
        ];
        $this->seeJsonContains($activityOneResponse);
        
        $activityTwoResponse = [
            'id' => $this->activityTwo->id,
            'consultantId' => $this->consultantInvitee_21_a2->consultant->id,
            'initiatorName' => $this->consultantInvitee_21_a2->consultant->personnel->getFullName(),
        ];
        $this->seeJsonDoesntContains($activityTwoResponse);
    }
    public function test_showAll_setOrder()
    {
        $this->activityOne->startDateTime = (new DateTimeImmutable('+24 hours'))->format('Y-m-d H:i:s');
        $this->activityOne->endDateTime = (new DateTimeImmutable('+25 hours'))->format('Y-m-d H:i:s');
        $this->activityTwo->startDateTime = (new DateTimeImmutable('-24 hours'))->format('Y-m-d H:i:s');
        $this->activityTwo->endDateTime = (new DateTimeImmutable('-23 hours'))->format('Y-m-d H:i:s');
        
        $this->activityUri .= "?order=DESC&page=1&pageSize=1";;
        $this->showAll();
        
        $totalResponse = ['total' => 2];
        $this->seeJsonContains($totalResponse);
        
        $activityOneResponse = [
            'id' => $this->activityOne->id,
            'managerId' => $this->managerInvitee_11_a1->manager->id,
            'initiatorName' => $this->managerInvitee_11_a1->manager->name,
        ];
        $this->seeJsonContains($activityOneResponse);
        
        $activityTwoResponse = [
            'id' => $this->activityTwo->id,
            'consultantId' => $this->consultantInvitee_21_a2->consultant->id,
            'initiatorName' => $this->consultantInvitee_21_a2->consultant->personnel->getFullName(),
        ];
        $this->seeJsonDoesntContains($activityTwoResponse);
    }
    public function test_showAll_usingInitiatorTypeFilter_manager_200()
    {
        $this->activityUri .= "?initiatorTypeList[]=manager";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        
        $activityOneResponse = [
            'id' => $this->activityOne->id,
            'managerId' => $this->managerInvitee_11_a1->manager->id,
            'initiatorName' => $this->managerInvitee_11_a1->manager->name,
        ];
        $this->seeJsonContains($activityOneResponse);
        
        $activityTwoResponse = [
            'id' => $this->activityTwo->id,
            'consultantId' => $this->consultantInvitee_21_a2->consultant->id,
            'initiatorName' => $this->consultantInvitee_21_a2->consultant->personnel->getFullName(),
        ];
        $this->seeJsonDoesntContains($activityTwoResponse);
    }
    public function test_showAll_usingInitiatorTypeFilter_consultant_200()
    {
        $this->activityUri .= "?initiatorTypeList[]=consultant";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        
        $activityOneResponse = [
            'id' => $this->activityOne->id,
            'managerId' => $this->managerInvitee_11_a1->manager->id,
            'initiatorName' => $this->managerInvitee_11_a1->manager->name,
        ];
        $this->seeJsonDoesntContains($activityOneResponse);
        
        $activityTwoResponse = [
            'id' => $this->activityTwo->id,
            'consultantId' => $this->consultantInvitee_21_a2->consultant->id,
            'initiatorName' => $this->consultantInvitee_21_a2->consultant->personnel->getFullName(),
        ];
        $this->seeJsonContains($activityTwoResponse);
    }
    public function test_showAll_usingInitiatorTypeFilter_coordinator_200()
    {
        $this->managerInvitee_11_a1->invitee->anInitiator = false;
        $this->coordinatorInvitee_12_a1->invitee->anInitiator = true;
        
        $this->activityUri .= "?initiatorTypeList[]=coordinator";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        
        $activityOneResponse = [
            'id' => $this->activityOne->id,
            'coordinatorId' => $this->coordinatorInvitee_12_a1->coordinator->id,
            'initiatorName' => $this->coordinatorInvitee_12_a1->coordinator->personnel->getFullName(),
        ];
        $this->seeJsonContains($activityOneResponse);
        
        $activityTwoResponse = [
            'id' => $this->activityTwo->id,
            'consultantId' => $this->consultantInvitee_21_a2->consultant->id,
            'initiatorName' => $this->consultantInvitee_21_a2->consultant->personnel->getFullName(),
        ];
        $this->seeJsonDoesntContains($activityTwoResponse);
    }
    public function test_showAll_usingInitiatorTypeFilter_participant_200()
    {
        $this->consultantInvitee_21_a2->invitee->anInitiator = false;
        $this->participantInvitee_22_a2->invitee->anInitiator = true;
        
        $this->activityUri .= "?initiatorTypeList[]=participant";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        
        $activityOneResponse = [
            'id' => $this->activityOne->id,
            'managerId' => $this->managerInvitee_11_a1->manager->id,
            'initiatorName' => $this->managerInvitee_11_a1->manager->name,
        ];
        $this->seeJsonDoesntContains($activityOneResponse);
        
        $activityTwoResponse = [
            'id' => $this->activityTwo->id,
            'participantId' => $this->participantInvitee_22_a2->participant->id,
            'initiatorName' => $this->clientParticipant->client->getFullName(),
        ];
        $this->seeJsonContains($activityTwoResponse);
    }
    public function test_showAll_usingInitiatorTypFilter_combined_200()
    {
        $this->activityUri .= "?initiatorTypeList[]=consultant&initiatorTypeList[]=manager";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 2];
        $this->seeJsonContains($totalResponse);
        
        $activityOneResponse = [
            'id' => $this->activityOne->id,
            'managerId' => $this->managerInvitee_11_a1->manager->id,
            'initiatorName' => $this->managerInvitee_11_a1->manager->name,
        ];
        $this->seeJsonContains($activityOneResponse);
        
        $activityTwoResponse = [
            'id' => $this->activityTwo->id,
            'consultantId' => $this->consultantInvitee_21_a2->consultant->id,
            'initiatorName' => $this->consultantInvitee_21_a2->consultant->personnel->getFullName(),
        ];
        $this->seeJsonContains($activityTwoResponse);
    }
    
}
