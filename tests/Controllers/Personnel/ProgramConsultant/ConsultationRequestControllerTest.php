<?php

namespace Tests\Controllers\Personnel\ProgramConsultant;

use DateTime;
use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Participant\RecordOfConsultationRequest,
    Firm\Program\RecordOfConsultationSetup,
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfFeedbackForm,
    RecordOfUser,
    Shared\RecordOfForm,
    User\RecordOfUserParticipant
};

class ConsultationRequestControllerTest extends ProgramConsultantTestCase
{

    protected $consultationRequestUri;
    protected $consultationRequest;
    protected $consultationRequest_concluded;
    protected $userParticipant;
    protected $participant;
    protected $offerInput;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequestUri = $this->programConsultantUri . "/consultation-requests";
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        $this->connection->table('ConsultationRequest')->truncate();
        $this->connection->table('ConsultationSession')->truncate();

        $form = new RecordOfForm(0);
        $this->connection->table('Form')->insert($form->toArrayForDbEntry());
        
        $feedbackForm = new RecordOfFeedbackForm($this->programConsultant->program->firm, $form);
        $this->connection->table('FeedbackForm')->insert($feedbackForm->toArrayForDbEntry());
        
        $consultationSetup = new RecordOfConsultationSetup(
                $this->programConsultant->program, $feedbackForm, $feedbackForm, 0);
        $this->connection->table('ConsultationSetup')->insert($consultationSetup->toArrayForDbEntry());
        
        $user = new RecordOfUser(0);
        $this->connection->table('User')->insert($user->toArrayForDbEntry());
        
        $this->participant = new RecordOfParticipant($this->programConsultant->program, 0);
        $this->connection->table('Participant')->insert($this->participant->toArrayForDbEntry());
        
        $this->userParticipant = new RecordOfUserParticipant($user, $this->participant);
        $this->connection->table('UserParticipant')->insert($this->userParticipant->toArrayForDbEntry());
        
        $this->consultationRequest = new RecordOfConsultationRequest(
                $consultationSetup, $this->participant, $this->programConsultant, 0);
        $this->consultationRequest_concluded = new RecordOfConsultationRequest(
                $consultationSetup, $this->participant, $this->programConsultant, 1);
        $this->consultationRequest_concluded->concluded = true;
        $this->consultationRequest_concluded->status = "rejected";
        $this->connection->table('ConsultationRequest')->insert($this->consultationRequest->toArrayForDbEntry());
        $this->connection->table('ConsultationRequest')->insert($this->consultationRequest_concluded->toArrayForDbEntry());
        
        $this->offerInput = [
            "startTime" => (new DateTime('+5 hours'))->format('Y-m-d H:i:s'),
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        $this->connection->table('ConsultationRequest')->truncate();
        $this->connection->table('ConsultationSession')->truncate();
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
            "endTime" => (new DateTime('+6 hours'))->format("Y-m-d H:i:s"),
            "status" => "offered",
        ];
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/offer";
        $this->patch($uri, $this->offerInput, $this->programConsultant->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
        
        $consultationRequestEntry = [
            "id" => $this->consultationRequest->id,
            "startDateTime" => $this->offerInput['startTime'],
            "endDateTime" => (new DateTime('+6 hours'))->format("Y-m-d H:i:s"),
            "status" => "offered",
        ];
        $this->seeInDatabase("ConsultationRequest", $consultationRequestEntry);
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
    public function test_accept_persistConsultationSession()
    {
        $this->connection->table('ConsultationSession')->truncate();
        
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/accept";
        $this->patch($uri, $this->offerInput, $this->programConsultant->personnel->token)
                ->seeStatusCode(200);
        $consulattionSessionEntry = [
            "Participant_id" => $this->consultationRequest->participant->id,
            "Consultant_id" => $this->consultationRequest->consultant->id,
            "ConsultationSetup_id" => $this->consultationRequest->consultationSetup->id,
            "startDateTime" => $this->consultationRequest->startDateTime,
            "endDateTime" => $this->consultationRequest->endDateTime,
        ];
        $this->seeInDatabase("ConsultationSession", $consulattionSessionEntry);
        
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
    
/*
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
                "clientParticipant" => null,
                "userParticipant" => [
                    "id" => $this->userParticipant->user->id,
                    "name" => $this->userParticipant->user->getFullName(),
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
 * 
 */
}
