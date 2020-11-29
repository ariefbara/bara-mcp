<?php

namespace Tests\Controllers\Manager\Program;

use Tests\Controllers\ {
    Manager\ProgramTestCase,
    RecordPreparation\Firm\Program\Activity\RecordOfInvitee,
    RecordPreparation\Firm\Program\ActivityType\RecordOfActivityParticipant,
    RecordPreparation\Firm\Program\Consultant\RecordOfActivityInvitation,
    RecordPreparation\Firm\Program\Participant\RecordOfConsultationRequest,
    RecordPreparation\Firm\Program\Participant\RecordOfConsultationSession,
    RecordPreparation\Firm\Program\RecordOfActivity,
    RecordPreparation\Firm\Program\RecordOfActivityType,
    RecordPreparation\Firm\Program\RecordOfConsultant,
    RecordPreparation\Firm\Program\RecordOfConsultationSetup,
    RecordPreparation\Firm\Program\RecordOfParticipant,
    RecordPreparation\Firm\RecordOfPersonnel
};

class ConsultantControllerTest extends ProgramTestCase
{

    protected $consultantUri;
    protected $consultant, $consultant1;
    protected $invitee;
    protected $consultationRequest;
    protected $consultationSession;
    protected $personnel, $personnel1, $personnel2;
    protected $consultantInput;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultantUri = $this->programUri . "/{$this->program->id}/consultants";

        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();

        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();
        
        $this->connection->table("ConsultationSetup")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("ConsultationRequest")->truncate();
        $this->connection->table("ConsultationSession")->truncate();

        $this->personnel = new RecordOfPersonnel($this->firm, 0, 'personnel@email.org', 'password123');
        $this->personnel1 = new RecordOfPersonnel($this->firm, 1, 'personnel1@email.org', 'password123');
        $this->personnel2 = new RecordOfPersonnel($this->firm, 2, 'personnel2@email.org', 'password123');
        $this->connection->table('Personnel')->insert($this->personnel->toArrayForDbEntry());
        $this->connection->table('Personnel')->insert($this->personnel1->toArrayForDbEntry());
        $this->connection->table('Personnel')->insert($this->personnel2->toArrayForDbEntry());

        $this->consultant = new RecordOfConsultant($this->program, $this->personnel, 0);
        $this->consultant1 = new RecordOfConsultant($this->program, $this->personnel1, 1);
        $this->connection->table('Consultant')->insert($this->consultant->toArrayForDbEntry());
        $this->connection->table('Consultant')->insert($this->consultant1->toArrayForDbEntry());

        $activityType = new RecordOfActivityType($this->program, 0);
        $this->connection->table("ActivityType")->insert($activityType->toArrayForDbEntry());

        $activity = new RecordOfActivity($activityType, 0);
        $this->connection->table("Activity")->insert($activity->toArrayForDbEntry());

        $activityParticipant = new RecordOfActivityParticipant($activityType, null, 0);
        $this->connection->table("ActivityParticipant")->insert($activityParticipant->toArrayForDbEntry());

        $this->invitee = new RecordOfInvitee($activity, $activityParticipant, 0);
        $this->connection->table("Invitee")->insert($this->invitee->toArrayForDbEntry());

        $consultantInvitee = new RecordOfActivityInvitation($this->consultant, $this->invitee);
        $this->connection->table("ConsultantInvitee")->insert($consultantInvitee->toArrayForDbEntry());

        $consultationSetup = new RecordOfConsultationSetup($this->program, null, null, 0);
        $this->connection->table("ConsultationSetup")->insert($consultationSetup->toArrayForDbEntry());
        
        $participant = new RecordOfParticipant($this->program, 0);
        $this->connection->table("Participant")->insert($participant->toArrayForDbEntry());

        $this->consultationRequest = new RecordOfConsultationRequest($consultationSetup, $participant, $this->consultant, 0);
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest->toArrayForDbEntry());
        
        $this->consultationSession = new RecordOfConsultationSession($consultationSetup, $participant, $this->consultant, 0);
        $this->connection->table("ConsultationSession")->insert($this->consultationSession->toArrayForDbEntry());

        $this->consultantInput = [
            "personnelId" => $this->personnel2->id,
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();

        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();
        
        $this->connection->table("ConsultationSetup")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("ConsultationRequest")->truncate();
        $this->connection->table("ConsultationSession")->truncate();
    }

    public function test_assign()
    {
        $response = [
            "personnel" => [
                "id" => $this->personnel2->id,
                "name" => $this->personnel2->getFullName(),
            ],
        ];

        $this->put($this->consultantUri, $this->consultantInput, $this->manager->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);

        $consultantRecord = [
            "Program_id" => $this->program->id,
            "Personnel_id" => $this->personnel2->id,
            "active" => true,
        ];
        $this->seeInDatabase('Consultant', $consultantRecord);
    }

    public function test_assign_userNotManager_error401()
    {
        $this->put($this->consultantUri, $this->consultantInput, $this->removedManager->token)
                ->seeStatusCode(401);
    }

    public function test_disable()
    {
        $uri = $this->consultantUri . "/{$this->consultant->id}";
        $this->delete($uri, [], $this->manager->token)
                ->seeStatusCode(200);

        $consultantRecord = [
            "id" => $this->consultant->id,
            "active" => false,
        ];
        $this->seeInDatabase('Consultant', $consultantRecord);
    }

    public function test_disable_disableAggregatedInvitation()
    {
        $uri = $this->consultantUri . "/{$this->consultant->id}";
        $this->delete($uri, [], $this->manager->token)
                ->seeStatusCode(200);

        $inviteeEntry = [
            "id" => $this->invitee->id,
            "cancelled" => true,
        ];
        $this->seeInDatabase("Invitee", $inviteeEntry);
    }
    public function test_disable_disableAggregatedConsultationRequest()
    {
        $uri = $this->consultantUri . "/{$this->consultant->id}";
        $this->delete($uri, [], $this->manager->token)
                ->seeStatusCode(200);

        $consultationRequestEntry = [
            "id" => $this->consultationRequest->id,
            "concluded" => true,
            "status" => "inactive consultant",
        ];
        $this->seeInDatabase("ConsultationRequest", $consultationRequestEntry);
    }
    public function test_disable_disableAggregatedConsultationSession()
    {
        $uri = $this->consultantUri . "/{$this->consultant->id}";
        $this->delete($uri, [], $this->manager->token)
                ->seeStatusCode(200);

        $consultationSessionEntry = [
            "id" => $this->consultationSession->id,
            "cancelled" => true,
            "note" => "inactive consultant",
        ];
        $this->seeInDatabase("ConsultationSession", $consultationSessionEntry);
    }

    public function test_show()
    {
        $response = [
            "id" => $this->consultant->id,
            "personnel" => [
                "id" => $this->consultant->personnel->id,
                "name" => $this->consultant->personnel->getFullName(),
            ],
        ];
        $uri = $this->consultantUri . "/{$this->consultant->id}";
        $this->get($uri, $this->manager->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }

    public function test_show_userNotManager_error401()
    {
        $uri = $this->consultantUri . "/{$this->consultant->id}";
        $this->get($uri, $this->removedManager->token)
                ->seeStatusCode(401);
    }

    public function test_showAll()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->consultant->id,
                    "personnel" => [
                        "id" => $this->consultant->personnel->id,
                        "name" => $this->consultant->personnel->getFullName(),
                    ],
                ],
                [
                    "id" => $this->consultant1->id,
                    "personnel" => [
                        "id" => $this->consultant1->personnel->id,
                        "name" => $this->consultant1->personnel->getFullName(),
                    ],
                ],
            ],
        ];
        $this->get($this->consultantUri, $this->manager->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }

    public function test_showAll_userNotManager_error401()
    {
        $this->get($this->consultantUri, $this->removedManager->token)
                ->seeStatusCode(401);
    }

}
