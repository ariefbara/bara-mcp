<?php

namespace Tests\Controllers\Personnel\ProgramConsultant;

use Tests\Controllers\RecordPreparation\{
    Firm\Program\Participant\RecordOfConsultationRequest,
    Firm\Program\RecordOfConsultationSetup,
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfConsultationFeedbackForm,
    RecordOfClient,
    Shared\RecordOfForm
};

class ConsultationRequestControllerTest extends ProgramConsultantTestCase
{

    protected $consultationRequestUri;
    protected $consultationRequest;
    protected $consultationRequest_concluded;
    protected $participant;
    protected $offerInput;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequestUri = $this->programConsultantUri . "/consultation-requests";
        $this->connection->table('Form')->truncate();
        $this->connection->table('ConsultationFeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ConsultationRequest')->truncate();
        $this->connection->table('ConsultationSession')->truncate();
        $this->connection->table('Notification')->truncate();
        $this->connection->table('ClientNotification')->truncate();
        $this->connection->table('ConsultationRequestNotificationForClient')->truncate();
        $this->connection->table('ConsultationSessionNotificationForClient')->truncate();

        $form = new RecordOfForm(0);
        $this->connection->table('Form')->insert($form->toArrayForDbEntry());
        
        $consultationFeedbackForm = new RecordOfConsultationFeedbackForm($this->programConsultant->program->firm, $form);
        $this->connection->table('ConsultationFeedbackForm')->insert($consultationFeedbackForm->toArrayForDbEntry());
        
        $consultationSetup = new RecordOfConsultationSetup(
                $this->programConsultant->program, $consultationFeedbackForm, $consultationFeedbackForm, 0);
        $this->connection->table('ConsultationSetup')->insert($consultationSetup->toArrayForDbEntry());
        
        $client = new RecordOfClient(0, 'adi@barapraja.com', 'password123');
        $this->connection->table('Client')->insert($client->toArrayForDbEntry());
        
        $this->participant = new RecordOfParticipant($this->programConsultant->program, $client, 0);
        $this->connection->table('Participant')->insert($this->participant->toArrayForDbEntry());
        
        $this->consultationRequest = new RecordOfConsultationRequest(
                $consultationSetup, $this->participant, $this->programConsultant, 0);
        $this->consultationRequest_concluded = new RecordOfConsultationRequest(
                $consultationSetup, $this->participant, $this->programConsultant, 1);
        $this->consultationRequest_concluded->concluded = true;
        $this->consultationRequest_concluded->status = "rejected";
        $this->connection->table('ConsultationRequest')->insert($this->consultationRequest->toArrayForDbEntry());
        $this->connection->table('ConsultationRequest')->insert($this->consultationRequest_concluded->toArrayForDbEntry());
        
        $this->offerInput = [
            "startTime" => (new \DateTime('+5 hours'))->format('Y-m-d H:i:s'),
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Form')->truncate();
        $this->connection->table('ConsultationFeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ConsultationRequest')->truncate();
        $this->connection->table('ConsultationSession')->truncate();
        $this->connection->table('Notification')->truncate();
        $this->connection->table('ClientNotification')->truncate();
        $this->connection->table('ConsultationRequestNotificationForClient')->truncate();
        $this->connection->table('ConsultationSessionNotificationForClient')->truncate();
    }
    
    public function test_reject()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/reject";
        $this->patch($uri, [], $this->programConsultant->personnel->token)
                ->seeStatusCode(200);
        
        $consultationRequestEntry = [
            "id" => $this->consultationRequest->id,
            "concluded" => true,
            "status" => "rejected",
        ];
        $this->seeInDatabase("ConsultationRequest", $consultationRequestEntry);
    }
    public function test_reject_consultationRequestAlreadyConcluded_error403()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest_concluded->id}/reject";
        $this->patch($uri, [], $this->programConsultant->personnel->token)
                ->seeStatusCode(403);
    }
    
    public function test_offer()
    {
        $response = [
            "id" => $this->consultationRequest->id,
            "startTime" => $this->offerInput['startTime'],
            "endTime" => (new \DateTime('+6 hours'))->format("Y-m-d H:i:s"),
            "status" => "offered",
        ];
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/offer";
        $this->patch($uri, $this->offerInput, $this->programConsultant->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
        
        $consultationRequestEntry = [
            "id" => $this->consultationRequest->id,
            "startDateTime" => $this->offerInput['startTime'],
            "endDateTime" => (new \DateTime('+6 hours'))->format("Y-m-d H:i:s"),
            "status" => "offered",
        ];
        $this->seeInDatabase("ConsultationRequest", $consultationRequestEntry);
    }
    public function test_offer_notifyClient()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/offer";
        $this->patch($uri, $this->offerInput, $this->programConsultant->personnel->token)
                ->seeStatusCode(200);
        
        $notificationEntry = [
            "message" => "consultant {$this->programConsultant->personnel->name} has offer new consultation time to you",
            "isRead" => false,
        ];
        $this->seeInDatabase("Notification", $notificationEntry);
        
        $clientNotificationEntry = [
            "Client_id" => $this->consultationRequest->participant->client->id,
        ];
        $this->seeInDatabase('ClientNotification', $clientNotificationEntry);
//see ConsultationRequestNotificationForClient dB manually to check notification persisted
    }
    public function test_offer_consultationRequestAlreadyConcluded_error403()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest_concluded->id}/offer";
        $this->patch($uri, $this->offerInput, $this->programConsultant->personnel->token)
                ->seeStatusCode(403);
    }
    
    public function test_accept()
    {
        $response = [
            "id" => $this->consultationRequest->id,
            "status" => "scheduled",
        ];
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/accept";
        $this->patch($uri, $this->offerInput, $this->programConsultant->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
        
        $consultationRequestEntry = [
            "id" => $this->consultationRequest->id,
            "status" => "scheduled",
        ];
        $this->seeInDatabase("ConsultationRequest", $consultationRequestEntry);
    }
    public function test_acceptNotifiyClient()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/accept";
        $this->patch($uri, $this->offerInput, $this->programConsultant->personnel->token)
                ->seeStatusCode(200);
        
        $notificationEntry = [
            "message" => "consultation with consultant {$this->programConsultant->personnel->name} has been scheduled",
            "isRead" => false,
        ];
        $this->seeInDatabase("Notification", $notificationEntry);
        
        $clientNotificationEntry = [
            "Client_id" => $this->consultationRequest->participant->client->id,
        ];
        $this->seeInDatabase('ClientNotification', $clientNotificationEntry);
//see ConsultationSessionNotificationForClient dB manually to check notification persisted
    }
    public function test_accept_statusNotProposed_error403()
    {
        $this->connection->table('ConsultationRequest')->truncate();
        $this->consultationRequest->status = 'offered';
        $this->connection->table('ConsultationRequest')->insert($this->consultationRequest->toArrayForDbEntry());
        
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/accept";
        $this->patch($uri, $this->offerInput, $this->programConsultant->personnel->token)
                ->seeStatusCode(403);
    }
    public function test_accept_consultationRequestAlreadyConcluded_error403()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest_concluded->id}/accept";
        $this->patch($uri, $this->offerInput, $this->programConsultant->personnel->token)
                ->seeStatusCode(403);
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->consultationRequest->id,
            "startTime" => $this->consultationRequest->startDateTime,
            "endTime" => $this->consultationRequest->endDateTime,
            "concluded" => $this->consultationRequest->concluded,
            "status" => $this->consultationRequest->status,
            "consultationSetup" => [
                "id" => $this->consultationRequest->consultationSetup->id,
                "name" => $this->consultationRequest->consultationSetup->name,
            ],
            "participant" => [
                "id" => $this->consultationRequest->participant->id,
                "client" => [
                    "id" => $this->consultationRequest->participant->client->id,
                    "name" => $this->consultationRequest->participant->client->name,
                ],
                
            ],
        ];
        
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}";
        $this->get($uri, $this->programConsultant->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    
    public function test_showAll()
    {
$this->disableExceptionHandling();
        $response = [
            "total" => 2, 
            "list" => [
                [
                    "id" => $this->consultationRequest->id,
                    "startTime" => $this->consultationRequest->startDateTime,
                    "endTime" => $this->consultationRequest->endDateTime,
                    "concluded" => $this->consultationRequest->concluded,
                    "status" => $this->consultationRequest->status,
                    "consultationSetup" => [
                        "id" => $this->consultationRequest->consultationSetup->id,
                        "name" => $this->consultationRequest->consultationSetup->name,
                    ],
                    "participant" => [
                        "id" => $this->consultationRequest->participant->id,
                        "client" => [
                            "id" => $this->consultationRequest->participant->client->id,
                            "name" => $this->consultationRequest->participant->client->name,
                        ],

                    ],
                ],
                [
                    "id" => $this->consultationRequest_concluded->id,
                    "startTime" => $this->consultationRequest_concluded->startDateTime,
                    "endTime" => $this->consultationRequest_concluded->endDateTime,
                    "concluded" => $this->consultationRequest_concluded->concluded,
                    "status" => $this->consultationRequest_concluded->status,
                    "consultationSetup" => [
                        "id" => $this->consultationRequest_concluded->consultationSetup->id,
                        "name" => $this->consultationRequest_concluded->consultationSetup->name,
                    ],
                    "participant" => [
                        "id" => $this->consultationRequest_concluded->participant->id,
                        "client" => [
                            "id" => $this->consultationRequest_concluded->participant->client->id,
                            "name" => $this->consultationRequest_concluded->participant->client->name,
                        ],

                    ],
                ],
            ],
        ];
        $this->get($this->consultationRequestUri, $this->programConsultant->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
}
