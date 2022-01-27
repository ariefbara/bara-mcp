<?php

namespace Tests\Controllers\Personnel;

use DateTimeImmutable;
use SharedContext\Domain\ValueObject\MentoringRequestStatus;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMentoringRequest;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFeedbackForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;

class MentoringSlotControllerTest extends MentorTestCase
{
    protected $showAllUri;
    protected $consultationSetupOne;
    protected $consultationSetupTwo;
    protected $mentoringSlotOne;
    protected $mentoringSlotTwo;
    protected $mentoringSlot_12_m1;
    protected $mentoringRequestOne;

    protected $createMultiSlotRequest;
    protected $updateRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        
        $this->showAllUri = $this->personnelUri . "/mentoring-slots";
        
        $firm = $this->personnel->firm;
        
        $programOne = $this->mentorOne->program;
        $programTwo = $this->mentorOne->program;
        
        $formOne = new RecordOfForm('1');
        
        $FeedbackFormOne = new RecordOfFeedbackForm($firm, $formOne);
        
        $this->consultationSetupOne = new RecordOfConsultationSetup($programOne, $FeedbackFormOne, $FeedbackFormOne, '1');
        $this->consultationSetupTwo = new RecordOfConsultationSetup($programTwo, $FeedbackFormOne, $FeedbackFormOne, '2');
                
        $this->mentoringSlotOne = new RecordOfMentoringSlot($this->mentorOne, $this->consultationSetupOne, '1');
        $this->mentoringSlotTwo = new RecordOfMentoringSlot($this->mentorTwo, $this->consultationSetupTwo, '2');
        $this->mentoringSlot_12_m1 = new RecordOfMentoringSlot($this->mentorOne, $this->consultationSetupOne, '12');
        $this->mentoringSlot_12_m1->startTime = new \DateTimeImmutable('+48 hours');
        $this->mentoringSlot_12_m1->endTime = new \DateTimeImmutable('+49 hours');
        
        $participantOne = new RecordOfParticipant($programOne, '1');
        
        $this->mentoringRequestOne = new RecordOfMentoringRequest(
                $participantOne, $this->mentorOne, $this->consultationSetupOne, '1');
        $this->mentoringRequestOne->startTime = (new \DateTimeImmutable('+48 hours'));
        $this->mentoringRequestOne->endTime = (new \DateTimeImmutable('+49 hours'));
        
        $this->createMultiSlotRequest = [
            'consultationSetupId' => $this->consultationSetupOne->id,
            'mentoringSlots' => [
                [
                    'startTime' => (new DateTimeImmutable('+24 hours'))->format('Y-m-d H:i:s'),
                    'endTime' => (new DateTimeImmutable('+25 hours'))->format('Y-m-d H:i:s'),
                    'mediaType' => 'offline',
                    'location' => 'rumah tawa',
                    'capacity' => 15
                ],
                [
                    'startTime' => (new DateTimeImmutable('+48 hours'))->format('Y-m-d H:i:s'),
                    'endTime' => (new DateTimeImmutable('+49 hours'))->format('Y-m-d H:i:s'),
                    'mediaType' => 'online',
                    'location' => 'zoom',
                    'capacity' => 8
                ],
            ],
        ];
        
        $this->updateRequest = [
            'startTime' => (new DateTimeImmutable('+48 hours'))->format('Y-m-d H:i:s'),
            'endTime' => (new DateTimeImmutable('+49 hours'))->format('Y-m-d H:i:s'),
            'mediaType' => 'online',
            'location' => 'zoom',
            'capacity' => 8
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
    }
    
    protected function createMultiSlot()
    {
        $this->mentorOne->program->insert($this->connection);
        $this->mentorOne->insert($this->connection);
        
        $this->consultationSetupOne->consultantFeedbackForm->insert($this->connection);
        $this->consultationSetupOne->insert($this->connection);
        
        $uri = $this->personnelUri . "/program-consultation/{$this->mentorOne->id}/mentoring-slots/create-multiple-slot";
        $this->post($uri, $this->createMultiSlotRequest, $this->personnel->token);
    }
    public function test_createMultiSlot_200()
    {
        $this->createMultiSlot();
        $this->seeStatusCode(200);
        
        $mentoringSlotOneRecord = [
            'Mentor_id' => $this->mentorOne->id,
            'ConsultationSetup_id' => $this->consultationSetupOne->id,
            'startTime' => $this->createMultiSlotRequest['mentoringSlots'][0]['startTime'],
            'endTime' => $this->createMultiSlotRequest['mentoringSlots'][0]['endTime'],
            'mediaType' => $this->createMultiSlotRequest['mentoringSlots'][0]['mediaType'],
            'location' => $this->createMultiSlotRequest['mentoringSlots'][0]['location'],
            'capacity' => $this->createMultiSlotRequest['mentoringSlots'][0]['capacity'],
        ];
        $this->seeInDatabase('MentoringSlot', $mentoringSlotOneRecord);
        
        $mentoringSlotTwoRecord = [
            'Mentor_id' => $this->mentorOne->id,
            'ConsultationSetup_id' => $this->consultationSetupOne->id,
            'startTime' => $this->createMultiSlotRequest['mentoringSlots'][1]['startTime'],
            'endTime' => $this->createMultiSlotRequest['mentoringSlots'][1]['endTime'],
            'mediaType' => $this->createMultiSlotRequest['mentoringSlots'][1]['mediaType'],
            'location' => $this->createMultiSlotRequest['mentoringSlots'][1]['location'],
            'capacity' => $this->createMultiSlotRequest['mentoringSlots'][1]['capacity'],
        ];
        $this->seeInDatabase('MentoringSlot', $mentoringSlotTwoRecord);
    }
    public function test_createMultiSlot_pastSchedule_400()
    {
        $this->createMultiSlotRequest['mentoringSlots'][0]['startTime'] = (new DateTimeImmutable('-1 hours'))->format('Y-m-d H:i:s');
        $this->createMultiSlot();
        $this->seeStatusCode(400);
    }
    public function test_createMultiSlot_unsuableConsultationSetup_removed_403()
    {
        $this->consultationSetupOne->removed = true;
        $this->createMultiSlot();
        $this->seeStatusCode(403);
    }
    public function test_createMultiSlot_unsuableConsultationSetup_differentProgram_403()
    {
        $this->mentorTwo->program->insert($this->connection);
        
        $this->consultationSetupOne->program = $this->mentorTwo->program;
        $this->createMultiSlot();
        $this->seeStatusCode(403);
    }
    public function test_createMultiSlot_inactiveMentor_403()
    {
        $this->mentorOne->active = false;
        $this->createMultiSlot();
        $this->seeStatusCode(403);
    }
    public function test_createMultiSlot_inConflictWithExistingRequest_Approved_403()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::APPROVED_BY_MENTOR;
        $this->mentoringRequestOne->participant->insert($this->connection);
        $this->mentoringRequestOne->insert($this->connection);
        
        $this->createMultiSlot();
        $this->seeStatusCode(403);
    }
    public function test_createMultiSlot_inConflictWithExistingRequest_Accepted_403()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::ACCEPTED_BY_PARTICIPANT;
        $this->mentoringRequestOne->participant->insert($this->connection);
        $this->mentoringRequestOne->insert($this->connection);
        
        $this->createMultiSlot();
        $this->seeStatusCode(403);
    }
    public function test_createMultiSlot_inConflictWithExistingRequest_offered_403()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::OFFERED;
        $this->mentoringRequestOne->participant->insert($this->connection);
        $this->mentoringRequestOne->insert($this->connection);
        
        $this->createMultiSlot();
        $this->seeStatusCode(403);
    }
    public function test_createMultiSlot_inConflictWithExistingRequest_requested_200()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::REQUESTED;
        $this->mentoringRequestOne->participant->insert($this->connection);
        $this->mentoringRequestOne->insert($this->connection);
        
        $this->createMultiSlot();
        $this->seeStatusCode(200);
    }
    public function test_createMultiSlot_inConflictWithExistingRequest_cancelled_200()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::CANCELLED;
        $this->mentoringRequestOne->participant->insert($this->connection);
        $this->mentoringRequestOne->insert($this->connection);
        
        $this->createMultiSlot();
        $this->seeStatusCode(200);
    }
    public function test_createMultiSlot_inConflictWithExistingRequest_rejected_200()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::REJECTED;
        $this->mentoringRequestOne->participant->insert($this->connection);
        $this->mentoringRequestOne->insert($this->connection);
        
        $this->createMultiSlot();
        $this->seeStatusCode(200);
    }
    public function test_createMultiSlot_inConflictWithMentoringSlot_403()
    {
        $this->mentoringSlotOne->insert($this->connection);
        $this->createMultiSlot();
        $this->seeStatusCode(403);
    }
    public function test_createMultiSlot_inConflictWithInactiveMentoringSlot_200()
    {
        $this->mentoringSlotOne->cancelled = true;
        $this->mentoringSlotOne->insert($this->connection);
        $this->createMultiSlot();
        $this->seeStatusCode(200);
    }
    
    protected function update()
    {
        $this->mentorOne->program->insert($this->connection);
        $this->mentorOne->insert($this->connection);
        
        $this->consultationSetupOne->consultantFeedbackForm->insert($this->connection);
        $this->consultationSetupOne->insert($this->connection);
        
        $this->mentoringSlotOne->insert($this->connection);
        
        $uri = $this->personnelUri . "/program-consultation/{$this->mentorOne->id}/mentoring-slots/{$this->mentoringSlotOne->id}";
        $this->patch($uri, $this->updateRequest, $this->personnel->token);
    }
    public function test_update_200()
    {
        $this->update();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->mentoringSlotOne->id,
            'cancelled' => false,
            'startTime' => $this->updateRequest['startTime'],
            'endTime' => $this->updateRequest['endTime'],
            'mediaType' => $this->updateRequest['mediaType'],
            'location' => $this->updateRequest['location'],
            'capacity' => $this->updateRequest['capacity'],
            'consultationSetup' => [
                'id' => $this->mentoringSlotOne->consultationSetup->id,
                'name' => $this->mentoringSlotOne->consultationSetup->name,
                'mentorFeedbackForm' => [
                    'id' => $this->consultationSetupOne->consultantFeedbackForm->id,
                    'name' => $this->consultationSetupOne->consultantFeedbackForm->form->name,
                    'description' => $this->consultationSetupOne->consultantFeedbackForm->form->description,
                    'stringFields' => [],
                    'integerFields' => [],
                    'textAreaFields' => [],
                    'attachmentFields' => [],
                    'singleSelectFields' => [],
                    'multiSelectFields' => [],
                ],
            ],
            'consultant' => [
                'id' => $this->mentoringSlotOne->consultant->id,
                'program' => [
                    'id' => $this->mentoringSlotOne->consultant->program->id,
                    'name' => $this->mentoringSlotOne->consultant->program->name,
                ],
                
            ],
        ];
        $this->seeJsonContains($response);
        
        $mentoringSlotRecord = [
            'id' => $this->mentoringSlotOne->id,
            'cancelled' => false,
            'startTime' => $this->updateRequest['startTime'],
            'endTime' => $this->updateRequest['endTime'],
            'mediaType' => $this->updateRequest['mediaType'],
            'location' => $this->updateRequest['location'],
            'capacity' => $this->updateRequest['capacity'],
        ];
        $this->seeInDatabase('MentoringSlot', $mentoringSlotRecord);
    }
    public function test_update_newScheduleNotAnUpcoming_400()
    {
        $this->updateRequest['startTime'] = (new DateTimeImmutable('-24 hours'))->format('Y-m-d H:i:s');
        $this->update();
        $this->seeStatusCode(400);
    }
    public function test_update_updatingNonUpcomingSchedule_403()
    {
        $this->mentoringSlotOne->startTime = new DateTimeImmutable('-25 hours');
        $this->mentoringSlotOne->endTime = new DateTimeImmutable('-24 hours');
        $this->update();
        $this->seeStatusCode(403);
    }
    public function test_update_inConflictWithExistingRequest_Approved_403()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::APPROVED_BY_MENTOR;
        $this->mentoringRequestOne->participant->insert($this->connection);
        $this->mentoringRequestOne->insert($this->connection);
        
        $this->update();
        $this->seeStatusCode(403);
    }
    public function test_update_inConflictWithExistingRequest_Accepted_403()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::ACCEPTED_BY_PARTICIPANT;
        $this->mentoringRequestOne->participant->insert($this->connection);
        $this->mentoringRequestOne->insert($this->connection);
        
        $this->update();
        $this->seeStatusCode(403);
    }
    public function test_update_inConflictWithExistingRequest_offered_403()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::OFFERED;
        $this->mentoringRequestOne->participant->insert($this->connection);
        $this->mentoringRequestOne->insert($this->connection);
        
        $this->update();
        $this->seeStatusCode(403);
    }
    public function test_update_inConflictWithExistingRequest_requested_200()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::REQUESTED;
        $this->mentoringRequestOne->participant->insert($this->connection);
        $this->mentoringRequestOne->insert($this->connection);
        
        $this->update();
        $this->seeStatusCode(200);
    }
    public function test_update_inConflictWithExistingRequest_cancelled_200()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::CANCELLED;
        $this->mentoringRequestOne->participant->insert($this->connection);
        $this->mentoringRequestOne->insert($this->connection);
        
        $this->update();
        $this->seeStatusCode(200);
    }
    public function test_update_inConflictWithExistingRequest_rejected_200()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::REJECTED;
        $this->mentoringRequestOne->participant->insert($this->connection);
        $this->mentoringRequestOne->insert($this->connection);
        
        $this->update();
        $this->seeStatusCode(200);
    }
    public function test_update_inConflictWithMentoringSlot_403()
    {
        $this->mentoringSlot_12_m1->insert($this->connection);
        $this->update();
        $this->seeStatusCode(403);
    }
    public function test_update_inConflictWithInactiveMentoringSlot_200()
    {
        $this->mentoringSlot_12_m1->cancelled = true;
        $this->mentoringSlot_12_m1->insert($this->connection);
        $this->update();
        $this->seeStatusCode(200);
    }
    
    protected function cancel()
    {
        $this->mentorOne->program->insert($this->connection);
        $this->mentorOne->insert($this->connection);
        
        $this->consultationSetupOne->consultantFeedbackForm->insert($this->connection);
        $this->consultationSetupOne->insert($this->connection);
        
        $this->mentoringSlotOne->insert($this->connection);
        
        $uri = $this->personnelUri . "/program-consultation/{$this->mentorOne->id}/mentoring-slots/{$this->mentoringSlotOne->id}";
        $this->delete($uri, [], $this->personnel->token);
    }
    public function test_cancel_200()
    {
        $this->cancel();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->mentoringSlotOne->id,
            'cancelled' => true,
        ];
        $this->seeJsonContains($response);
        
        $mentoringSlotRecord = [
            'id' => $this->mentoringSlotOne->id,
            'cancelled' => true,
        ];
        $this->seeInDatabase('MentoringSlot', $mentoringSlotRecord);
    }
    public function test_cancel_cancellingNonUpcomingSchedule_403()
    {
        $this->mentoringSlotOne->startTime = new DateTimeImmutable('-25 hours');
        $this->mentoringSlotOne->endTime = new DateTimeImmutable('-24 hours');
        $this->cancel();
        $this->seeStatusCode(403);
    }
    
    protected function show()
    {
        $this->mentorOne->program->insert($this->connection);
        $this->mentorOne->insert($this->connection);
        
        $this->consultationSetupOne->consultantFeedbackForm->insert($this->connection);
        $this->consultationSetupOne->insert($this->connection);
        
        $this->mentoringSlotOne->insert($this->connection);
        
        $uri = $this->personnelUri . "/mentoring-slots/{$this->mentoringSlotOne->id}";
        $this->get($uri, $this->personnel->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->mentoringSlotOne->id,
            'cancelled' => false,
            'startTime' => $this->mentoringSlotOne->startTime->format('Y-m-d H:i:s'),
            'endTime' => $this->mentoringSlotOne->endTime->format('Y-m-d H:i:s'),
            'mediaType' => $this->mentoringSlotOne->mediaType,
            'location' => $this->mentoringSlotOne->location,
            'capacity' => $this->mentoringSlotOne->capacity,
            'consultationSetup' => [
                'id' => $this->mentoringSlotOne->consultationSetup->id,
                'name' => $this->mentoringSlotOne->consultationSetup->name,
                'mentorFeedbackForm' => [
                    'id' => $this->consultationSetupOne->consultantFeedbackForm->id,
                    'name' => $this->consultationSetupOne->consultantFeedbackForm->form->name,
                    'description' => $this->consultationSetupOne->consultantFeedbackForm->form->description,
                    'stringFields' => [],
                    'integerFields' => [],
                    'textAreaFields' => [],
                    'attachmentFields' => [],
                    'singleSelectFields' => [],
                    'multiSelectFields' => [],
                ],
            ],
            'consultant' => [
                'id' => $this->mentoringSlotOne->consultant->id,
                'program' => [
                    'id' => $this->mentoringSlotOne->consultant->program->id,
                    'name' => $this->mentoringSlotOne->consultant->program->name,
                ],
                
            ],
        ];
        $this->seeJsonContains($response);
    }
    
    protected function showAll()
    {
        $this->mentorOne->program->insert($this->connection);
        $this->mentorTwo->program->insert($this->connection);
        
        $this->mentorOne->insert($this->connection);
        $this->mentorTwo->insert($this->connection);
        
        $this->consultationSetupOne->consultantFeedbackForm->insert($this->connection);
        
        $this->consultationSetupOne->insert($this->connection);
        $this->consultationSetupTwo->insert($this->connection);
        
        $this->mentoringSlotOne->insert($this->connection);
        $this->mentoringSlotTwo->insert($this->connection);
        
        $this->get($this->showAllUri, $this->personnel->token);
    }
    public function test_showAll_200()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        $response = [
            'total' => 2,
            'list' => [
                [
                    'id' => $this->mentoringSlotOne->id,
                    'cancelled' => false,
                    'startTime' => $this->mentoringSlotOne->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->mentoringSlotOne->endTime->format('Y-m-d H:i:s'),
                    'mediaType' => $this->mentoringSlotOne->mediaType,
                    'location' => $this->mentoringSlotOne->location,
                    'capacity' => $this->mentoringSlotOne->capacity,
                    'consultationSetup' => [
                        'id' => $this->mentoringSlotOne->consultationSetup->id,
                        'name' => $this->mentoringSlotOne->consultationSetup->name,
                    ],
                    'consultant' => [
                        'id' => $this->mentoringSlotOne->consultant->id,
                        'program' => [
                            'id' => $this->mentoringSlotOne->consultant->program->id,
                            'name' => $this->mentoringSlotOne->consultant->program->name,
                        ],

                    ],
                ],
                [
                    'id' => $this->mentoringSlotTwo->id,
                    'cancelled' => false,
                    'startTime' => $this->mentoringSlotTwo->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->mentoringSlotTwo->endTime->format('Y-m-d H:i:s'),
                    'mediaType' => $this->mentoringSlotTwo->mediaType,
                    'location' => $this->mentoringSlotTwo->location,
                    'capacity' => $this->mentoringSlotTwo->capacity,
                    'consultationSetup' => [
                        'id' => $this->mentoringSlotTwo->consultationSetup->id,
                        'name' => $this->mentoringSlotTwo->consultationSetup->name,
                    ],
                    'consultant' => [
                        'id' => $this->mentoringSlotTwo->consultant->id,
                        'program' => [
                            'id' => $this->mentoringSlotTwo->consultant->program->id,
                            'name' => $this->mentoringSlotTwo->consultant->program->name,
                        ],

                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_200_orderByStartTimeAscending()
    {
        $this->mentoringSlotOne->startTime = new DateTimeImmutable('+200 hours');
        $this->mentoringSlotOne->startTime = new DateTimeImmutable('+202 hours');
        
        $this->showAllUri .= "?page=1&pageSize=1";
        $this->showAll();
        $this->seeStatusCode(200);
        $response = [
            'total' => 2,
            'list' => [
                [
                    'id' => $this->mentoringSlotTwo->id,
                    'cancelled' => false,
                    'startTime' => $this->mentoringSlotTwo->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->mentoringSlotTwo->endTime->format('Y-m-d H:i:s'),
                    'mediaType' => $this->mentoringSlotTwo->mediaType,
                    'location' => $this->mentoringSlotTwo->location,
                    'capacity' => $this->mentoringSlotTwo->capacity,
                    'consultationSetup' => [
                        'id' => $this->mentoringSlotTwo->consultationSetup->id,
                        'name' => $this->mentoringSlotTwo->consultationSetup->name,
                    ],
                    'consultant' => [
                        'id' => $this->mentoringSlotTwo->consultant->id,
                        'program' => [
                            'id' => $this->mentoringSlotTwo->consultant->program->id,
                            'name' => $this->mentoringSlotTwo->consultant->program->name,
                        ],

                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    
}
