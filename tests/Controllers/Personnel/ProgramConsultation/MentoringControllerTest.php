<?php

namespace Tests\Controllers\Personnel\ProgramConsultation;

use DateTime;
use SharedContext\Domain\ValueObject\DeclaredMentoringStatus;
use SharedContext\Domain\ValueObject\MentoringRequestStatus;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\MentoringSlot\RecordOfBookedMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MentoringRequest\RecordOfNegotiatedMentoring;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfDeclaredMentoring;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMentoringRequest;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Shared\RecordOfMentoring;

class MentoringControllerTest extends ExtendedConsultantTestCase
{
    protected $mentoringUri;
    protected $mentoringRequestOne;
    protected $negotiatedMentoringTwo;
    protected $bookedMentoringSlotThree;
    protected $declaredMentoringFour;
    protected $mentoringRequestFive_p2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mentoringUri = $this->consultantUri . "/mentorings";
        
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
        $this->connection->table('DeclaredMentoring')->truncate();
        
        $program = $this->consultant->program;
        
        $personnelOne = new RecordOfPersonnel($program->firm, 1);
        
        $participantOne = new RecordOfParticipant($program, 1);
        $participantTwo = new RecordOfParticipant($program, 2);
        
        $mentorOne = new RecordOfConsultant($program, $personnelOne, 1);
        
        $consultationSetupOne = new RecordOfConsultationSetup($program, null, null, 1);

        $mentoringTwo = new RecordOfMentoring(2);
        $mentoringThree = new RecordOfMentoring(3);
        $mentoringFour = new RecordOfMentoring(4);


        $this->mentoringRequestOne = new RecordOfMentoringRequest($participantOne, $mentorOne, $consultationSetupOne, 1);
        $mentoringRequestTwo = new RecordOfMentoringRequest($participantOne, $mentorOne, $consultationSetupOne, 2);
        $mentoringRequestTwo->requestStatus = MentoringRequestStatus::ACCEPTED_BY_PARTICIPANT;
        $this->mentoringRequestFive_p2 = new RecordOfMentoringRequest($participantTwo, $mentorOne, $consultationSetupOne, 5);

        $this->negotiatedMentoringTwo = new RecordOfNegotiatedMentoring($mentoringRequestTwo, $mentoringTwo);

        $mentoringSlotOne = new RecordOfMentoringSlot($mentorOne, $consultationSetupOne, 1);
        $this->bookedMentoringSlotThree = new RecordOfBookedMentoringSlot($mentoringSlotOne, $mentoringThree, $participantOne);

        $this->declaredMentoringFour = new RecordOfDeclaredMentoring($mentorOne, $participantOne, $consultationSetupOne, $mentoringFour);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
        $this->connection->table('DeclaredMentoring')->truncate();
    }
    
    protected function viewAll()
    {
        $this->persistConsultantDependency();
        
        $this->mentoringRequestOne->participant->insert($this->connection);
        $this->mentoringRequestFive_p2->participant->insert($this->connection);
        
        $this->mentoringRequestOne->mentor->personnel->insert($this->connection);
        $this->mentoringRequestOne->mentor->insert($this->connection);
        
        $this->mentoringRequestOne->consultationSetup->insert($this->connection);
        
        $this->mentoringRequestOne->insert($this->connection);
        $this->negotiatedMentoringTwo->mentoringRequest->insert($this->connection);
        $this->mentoringRequestFive_p2->insert($this->connection);
        
        $this->negotiatedMentoringTwo->insert($this->connection);
        
        $this->bookedMentoringSlotThree->mentoringSlot->insert($this->connection);
        $this->bookedMentoringSlotThree->insert($this->connection);
        
        $this->declaredMentoringFour->insert($this->connection);
        
        $this->get($this->mentoringUri, $this->consultant->personnel->token);
    }
    public function test_viewAll_200()
    {
$this->disableExceptionHandling();
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $response = [
'!printMe' => 'printme',
            'total' => '5',
            'list' => [
                [
                    'startTime' => $this->mentoringRequestOne->startTime,
                    'endTime' => $this->mentoringRequestOne->endTime,
                    'mentorId' => $this->mentoringRequestOne->mentor->id,
                    'mentorName' => $this->mentoringRequestOne->mentor->personnel->getFullName(),
                    'consultationSetupId' => $this->mentoringRequestOne->consultationSetup->id,
                    'consultationSetupName' => $this->mentoringRequestOne->consultationSetup->name,
                    'bookedMentoringId' => null,
                    'mentoringRequestId' => $this->mentoringRequestOne->id,
                    'negotiatedMentoringId' => null,
                    'mentoringRequestStatus' => 'requested by participant',
                    'declaredMentoringId' => null,
                    'declaredMentoringStatus' => null,
                    'participantReportId' => null,
                ],
                [
                    'startTime' => $this->negotiatedMentoringTwo->mentoringRequest->startTime,
                    'endTime' => $this->negotiatedMentoringTwo->mentoringRequest->endTime,
                    'mentorId' => $this->negotiatedMentoringTwo->mentoringRequest->mentor->id,
                    'mentorName' => $this->negotiatedMentoringTwo->mentoringRequest->mentor->personnel->getFullName(),
                    'consultationSetupId' => $this->negotiatedMentoringTwo->mentoringRequest->consultationSetup->id,
                    'consultationSetupName' => $this->negotiatedMentoringTwo->mentoringRequest->consultationSetup->name,
                    'bookedMentoringId' => null,
                    'mentoringRequestId' => $this->negotiatedMentoringTwo->mentoringRequest->id,
                    'negotiatedMentoringId' => $this->negotiatedMentoringTwo->id,
                    'mentoringRequestStatus' => 'accepted by participant',
                    'declaredMentoringId' => null,
                    'declaredMentoringStatus' => null,
                    'participantReportId' => null,
                ],
                [
                    'startTime' => $this->bookedMentoringSlotThree->mentoringSlot->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->bookedMentoringSlotThree->mentoringSlot->endTime->format('Y-m-d H:i:s'),
                    'mentorId' => $this->bookedMentoringSlotThree->mentoringSlot->consultant->id,
                    'mentorName' => $this->bookedMentoringSlotThree->mentoringSlot->consultant->personnel->getFullName(),
                    'consultationSetupId' => $this->bookedMentoringSlotThree->mentoringSlot->consultationSetup->id,
                    'consultationSetupName' => $this->bookedMentoringSlotThree->mentoringSlot->consultationSetup->name,
                    'bookedMentoringId' => $this->bookedMentoringSlotThree->id,
                    'mentoringRequestId' => null,
                    'negotiatedMentoringId' => null,
                    'mentoringRequestStatus' => null,
                    'declaredMentoringId' => null,
                    'declaredMentoringStatus' => null,
                    'participantReportId' => null,
                ],
                [
                    'startTime' => $this->declaredMentoringFour->startTime,
                    'endTime' => $this->declaredMentoringFour->endTime,
                    'mentorId' => $this->declaredMentoringFour->mentor->id,
                    'mentorName' => $this->declaredMentoringFour->mentor->personnel->getFullName(),
                    'consultationSetupId' => $this->declaredMentoringFour->consultationSetup->id,
                    'consultationSetupName' => $this->declaredMentoringFour->consultationSetup->name,
                    'bookedMentoringId' => null,
                    'mentoringRequestId' => null,
                    'negotiatedMentoringId' => null,
                    'mentoringRequestStatus' => null,
                    'declaredMentoringId' => $this->declaredMentoringFour->id,
                    'declaredMentoringStatus' => 'declared by mentor',
                    'participantReportId' => null,
                ],
                [
                    'startTime' => $this->mentoringRequestFive_p2->startTime,
                    'endTime' => $this->mentoringRequestFive_p2->endTime,
                    'mentorId' => $this->mentoringRequestFive_p2->mentor->id,
                    'mentorName' => $this->mentoringRequestFive_p2->mentor->personnel->getFullName(),
                    'consultationSetupId' => $this->mentoringRequestFive_p2->consultationSetup->id,
                    'consultationSetupName' => $this->mentoringRequestFive_p2->consultationSetup->name,
                    'bookedMentoringId' => null,
                    'mentoringRequestId' => $this->mentoringRequestFive_p2->id,
                    'negotiatedMentoringId' => null,
                    'mentoringRequestStatus' => 'requested by participant',
                    'declaredMentoringId' => null,
                    'declaredMentoringStatus' => null,
                    'participantReportId' => null,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewAll_participantIdFilter()
    {
        $this->mentoringUri .= "?participantId={$this->mentoringRequestOne->participant->id}";
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '4']);
        $this->seeJsonContains(['mentoringRequestId' => $this->mentoringRequestOne->id]);
        $this->seeJsonContains(['negotiatedMentoringId' => $this->negotiatedMentoringTwo->id]);
        $this->seeJsonContains(['bookedMentoringId' => $this->bookedMentoringSlotThree->id]);
        $this->seeJsonContains(['declaredMentoringId' => $this->declaredMentoringFour->id]);
        $this->seeJsonDoesntContains(['mentoringRequestId' => $this->mentoringRequestFive_p2->id]);
    }
    public function test_viewAll_fromFilter()
    {
        $this->mentoringRequestOne->startTime = (new DateTime('+72 hours'));
        $this->mentoringRequestOne->endTime = (new DateTime('+73 hours'));
        $fromFilter = (new DateTime('+48 hours'))->format('Y-m-d H:i:s');
        $this->mentoringUri .= "?from={$fromFilter}";
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['mentoringRequestId' => $this->mentoringRequestOne->id]);
        $this->seeJsonDoesntContains(['negotiatedMentoringId' => $this->negotiatedMentoringTwo->id]);
        $this->seeJsonDoesntContains(['bookedMentoringId' => $this->bookedMentoringSlotThree->id]);
        $this->seeJsonDoesntContains(['declaredMentoringId' => $this->declaredMentoringFour->id]);
        $this->seeJsonDoesntContains(['mentoringRequestId' => $this->mentoringRequestFive_p2->id]);
    }
    public function test_viewAll_toFilter()
    {
        $toFilter = (new DateTime())->format('Y-m-d H:i:s');
        $this->mentoringUri .= "?to={$toFilter}";
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['mentoringRequestId' => $this->mentoringRequestOne->id]);
        $this->seeJsonDoesntContains(['negotiatedMentoringId' => $this->negotiatedMentoringTwo->id]);
        $this->seeJsonDoesntContains(['bookedMentoringId' => $this->bookedMentoringSlotThree->id]);
        $this->seeJsonContains(['declaredMentoringId' => $this->declaredMentoringFour->id]);
        $this->seeJsonDoesntContains(['mentoringRequestId' => $this->mentoringRequestFive_p2->id]);
    }
    public function test_viewAll_useOrder()
    {
        $this->mentoringUri .= "?order=DESC&pageSize=1";
        $this->negotiatedMentoringTwo->mentoringRequest->startTime = (new DateTime('+360 hours'));
        $this->negotiatedMentoringTwo->mentoringRequest->endTime = (new DateTime('+362 hours'));
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '5']);
        $this->seeJsonDoesntContains(['mentoringRequestId' => $this->mentoringRequestOne->id]);
        $this->seeJsonContains(['negotiatedMentoringId' => $this->negotiatedMentoringTwo->id]);
        $this->seeJsonDoesntContains(['bookedMentoringId' => $this->bookedMentoringSlotThree->id]);
        $this->seeJsonDoesntContains(['declaredMentoringId' => $this->declaredMentoringFour->id]);
        $this->seeJsonDoesntContains(['mentoringRequestId' => $this->mentoringRequestFive_p2->id]);
    }
    public function test_viewAll_unmanagedMentoring_belongsToParticipantOfOtherProgram()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->mentoringRequestFive_p2->participant->program = $otherProgram;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '4']);
        $this->seeJsonContains(['mentoringRequestId' => $this->mentoringRequestOne->id]);
        $this->seeJsonContains(['negotiatedMentoringId' => $this->negotiatedMentoringTwo->id]);
        $this->seeJsonContains(['bookedMentoringId' => $this->bookedMentoringSlotThree->id]);
        $this->seeJsonContains(['declaredMentoringId' => $this->declaredMentoringFour->id]);
        $this->seeJsonDoesntContains(['mentoringRequestId' => $this->mentoringRequestFive_p2->id]);
    }
    public function test_viewAll_excludeInvalidMentoringRequest()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::CANCELLED;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '4']);
        $this->seeJsonDoesntContains(['mentoringRequestId' => $this->mentoringRequestOne->id]);
        $this->seeJsonContains(['negotiatedMentoringId' => $this->negotiatedMentoringTwo->id]);
        $this->seeJsonContains(['bookedMentoringId' => $this->bookedMentoringSlotThree->id]);
        $this->seeJsonContains(['declaredMentoringId' => $this->declaredMentoringFour->id]);
        $this->seeJsonContains(['mentoringRequestId' => $this->mentoringRequestFive_p2->id]);
    }
    public function test_viewAll_excludeInvalidBookedMentoring()
    {
        $this->bookedMentoringSlotThree->cancelled = true;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '4']);
        $this->seeJsonContains(['mentoringRequestId' => $this->mentoringRequestOne->id]);
        $this->seeJsonContains(['negotiatedMentoringId' => $this->negotiatedMentoringTwo->id]);
        $this->seeJsonDoesntContains(['bookedMentoringId' => $this->bookedMentoringSlotThree->id]);
        $this->seeJsonContains(['declaredMentoringId' => $this->declaredMentoringFour->id]);
        $this->seeJsonContains(['mentoringRequestId' => $this->mentoringRequestFive_p2->id]);
    }
    public function test_viewAll_excludeInvalidDeclaredMentoring()
    {
        $this->declaredMentoringFour->declaredStatus = DeclaredMentoringStatus::DENIED_BY_MENTOR;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '4']);
        $this->seeJsonContains(['mentoringRequestId' => $this->mentoringRequestOne->id]);
        $this->seeJsonContains(['negotiatedMentoringId' => $this->negotiatedMentoringTwo->id]);
        $this->seeJsonContains(['bookedMentoringId' => $this->bookedMentoringSlotThree->id]);
        $this->seeJsonDoesntContains(['declaredMentoringId' => $this->declaredMentoringFour->id]);
        $this->seeJsonContains(['mentoringRequestId' => $this->mentoringRequestFive_p2->id]);
    }
    
    protected function viewMentoringRequestDetail()
    {
        $this->persistConsultantDependency();
        
        $this->mentoringRequestOne->mentor->personnel->insert($this->connection);
        $this->mentoringRequestOne->mentor->insert($this->connection);
        $this->mentoringRequestOne->consultationSetup->insert($this->connection);
        $this->mentoringRequestOne->participant->insert($this->connection);
        $this->mentoringRequestOne->insert($this->connection);
        
        $uri = $this->consultantUri . "/mentoring-requests/{$this->mentoringRequestOne->id}";
        $this->get($uri, $this->consultant->personnel->token);
    }
    public function test_viewMentoringRequestDetail_200()
    {
        $this->viewMentoringRequestDetail();
        $this->seeStatusCode(200);
        
        $respose = [
            'id' => $this->mentoringRequestOne->id,
            'startTime' => $this->mentoringRequestOne->startTime,
            'endTime' => $this->mentoringRequestOne->endTime,
            'mediaType' => $this->mentoringRequestOne->mediaType,
            'location' => $this->mentoringRequestOne->location,
            'requestStatus' => 'requested by participant',
            'mentor' => [
                'id' => $this->mentoringRequestOne->mentor->id,
                'personnel' => [
                    'id' => $this->mentoringRequestOne->mentor->personnel->id,
                    'name' => $this->mentoringRequestOne->mentor->personnel->getFullName(),
                ],
            ],
            'consultationSetup' => [
                'id' => $this->mentoringRequestOne->consultationSetup->id,
                'name' => $this->mentoringRequestOne->consultationSetup->name,
            ],
        ];
        $this->seeJsonContains($respose);
    }
    public function test_viewMentoringRequestDetail_mentoringRequestBelongsToParticipantOfOtherProgram_404()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->mentoringRequestOne->participant->program = $otherProgram;
        
        $this->viewMentoringRequestDetail();
        $this->seeStatusCode(404);
    }
    public function test_viewMentoringRequestDetail_inactiveConsultant_403()
    {
        $this->consultant->active = false;
        
        $this->viewMentoringRequestDetail();
        $this->seeStatusCode(403);
    }
    
    protected function viewNegotiatedMentoringDetail()
    {
        $this->persistConsultantDependency();
        
        $this->negotiatedMentoringTwo->mentoringRequest->mentor->personnel->insert($this->connection);
        $this->negotiatedMentoringTwo->mentoringRequest->mentor->insert($this->connection);
        $this->negotiatedMentoringTwo->mentoringRequest->consultationSetup->insert($this->connection);
        $this->negotiatedMentoringTwo->mentoringRequest->participant->insert($this->connection);
        $this->negotiatedMentoringTwo->mentoringRequest->insert($this->connection);
        $this->negotiatedMentoringTwo->insert($this->connection);
        
        $uri = $this->consultantUri . "/negotiated-mentorings/{$this->negotiatedMentoringTwo->id}";
        $this->get($uri, $this->consultant->personnel->token);
    }
    public function test_viewNegotiatedMentoringDetail_200()
    {
        $this->viewNegotiatedMentoringDetail();
        $this->seeStatusCode(200);
        
        $respose = [
            'id' => $this->negotiatedMentoringTwo->id,
            'mentoringRequest' => [
                'id' => $this->negotiatedMentoringTwo->mentoringRequest->id,
                'startTime' => $this->negotiatedMentoringTwo->mentoringRequest->startTime,
                'endTime' => $this->negotiatedMentoringTwo->mentoringRequest->endTime,
                'mediaType' => $this->negotiatedMentoringTwo->mentoringRequest->mediaType,
                'location' => $this->negotiatedMentoringTwo->mentoringRequest->location,
                'requestStatus' => 'accepted by participant',
                'mentor' => [
                    'id' => $this->negotiatedMentoringTwo->mentoringRequest->mentor->id,
                    'personnel' => [
                        'id' => $this->negotiatedMentoringTwo->mentoringRequest->mentor->personnel->id,
                        'name' => $this->negotiatedMentoringTwo->mentoringRequest->mentor->personnel->getFullName(),
                    ],
                ],
                'consultationSetup' => [
                    'id' => $this->negotiatedMentoringTwo->mentoringRequest->consultationSetup->id,
                    'name' => $this->negotiatedMentoringTwo->mentoringRequest->consultationSetup->name,
                    'participantFeedbackForm' => null,
                    'mentorFeedbackForm' => null,
                ],
            ],
            'participantReport' => null,
            'mentorReport' => null,
        ];
        $this->seeJsonContains($respose);
    }
    public function test_viewNegotiatedMentoringDetail_mentoringRequestBelongsToParticipantOfOtherProgram_404()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->negotiatedMentoringTwo->mentoringRequest->participant->program = $otherProgram;
        
        $this->viewNegotiatedMentoringDetail();
        $this->seeStatusCode(404);
    }
    public function test_viewNegotiatedMentoringDetail_inactiveConsultant_403()
    {
        $this->consultant->active = false;
        
        $this->viewNegotiatedMentoringDetail();
        $this->seeStatusCode(403);
    }
    
    protected function viewBookedMentoringSlotDetail()
    {
        $this->persistConsultantDependency();
        
        $this->bookedMentoringSlotThree->mentoringSlot->consultant->personnel->insert($this->connection);
        $this->bookedMentoringSlotThree->mentoringSlot->consultant->insert($this->connection);
        $this->bookedMentoringSlotThree->mentoringSlot->consultationSetup->insert($this->connection);
        $this->bookedMentoringSlotThree->mentoringSlot->insert($this->connection);
        $this->bookedMentoringSlotThree->participant->insert($this->connection);
        $this->bookedMentoringSlotThree->insert($this->connection);
        
        $uri = $this->consultantUri . "/booked-mentoring-slots/{$this->bookedMentoringSlotThree->id}";
        $this->get($uri, $this->consultant->personnel->token);
    }
    public function test_viewBookedMentoringSlotDetail_200()
    {
$this->disableExceptionHandling();
        $this->viewBookedMentoringSlotDetail();
        $this->seeStatusCode(200);
        
        $respose = [
            'id' => $this->bookedMentoringSlotThree->id,
            'cancelled' => $this->bookedMentoringSlotThree->cancelled,
            'mentoringSlot' => [
                'id' => $this->bookedMentoringSlotThree->mentoringSlot->id,
                'cancelled' => $this->bookedMentoringSlotThree->mentoringSlot->cancelled,
                'capacity' => $this->bookedMentoringSlotThree->mentoringSlot->capacity,
                'startTime' => $this->bookedMentoringSlotThree->mentoringSlot->startTime->format('Y-m-d H:i:s'),
                'endTime' => $this->bookedMentoringSlotThree->mentoringSlot->endTime->format('Y-m-d H:i:s'),
                'mediaType' => $this->bookedMentoringSlotThree->mentoringSlot->mediaType,
                'location' => $this->bookedMentoringSlotThree->mentoringSlot->location,
                'mentor' => [
                    'id' => $this->bookedMentoringSlotThree->mentoringSlot->consultant->id,
                    'personnel' => [
                        'id' => $this->bookedMentoringSlotThree->mentoringSlot->consultant->personnel->id,
                        'name' => $this->bookedMentoringSlotThree->mentoringSlot->consultant->personnel->getFullName(),
                    ],
                ],
                'consultationSetup' => [
                    'id' => $this->bookedMentoringSlotThree->mentoringSlot->consultationSetup->id,
                    'name' => $this->bookedMentoringSlotThree->mentoringSlot->consultationSetup->name,
                    'participantFeedbackForm' => null,
                    'mentorFeedbackForm' => null,
                ],
            ],
            'participantReport' => null,
            'mentorReport' => null,
        ];
        $this->seeJsonContains($respose);
    }
    public function test_viewBookedMentoringSlotDetail_mentoringRequestBelongsToParticipantOfOtherProgram_404()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->bookedMentoringSlotThree->participant->program = $otherProgram;
        
        $this->viewBookedMentoringSlotDetail();
        $this->seeStatusCode(404);
    }
    public function test_viewBookedMentoringSlotDetail_inactiveConsultant_403()
    {
        $this->consultant->active = false;
        
        $this->viewBookedMentoringSlotDetail();
        $this->seeStatusCode(403);
    }
    
    protected function viewDeclaredMentoringDetail()
    {
        $this->persistConsultantDependency();
        
        $this->declaredMentoringFour->mentor->personnel->insert($this->connection);
        $this->declaredMentoringFour->mentor->insert($this->connection);
        $this->declaredMentoringFour->consultationSetup->insert($this->connection);
        $this->declaredMentoringFour->participant->insert($this->connection);
        $this->declaredMentoringFour->insert($this->connection);
        
        $uri = $this->consultantUri . "/declared-mentorings/{$this->declaredMentoringFour->id}";
        $this->get($uri, $this->consultant->personnel->token);
    }
    public function test_viewDeclaredMentoringDetail_200()
    {
$this->disableExceptionHandling();
        $this->viewDeclaredMentoringDetail();
        $this->seeStatusCode(200);
        
        $respose = [
            'id' => $this->declaredMentoringFour->id,
            'declaredStatus' => 'declared by mentor',
            'startTime' => $this->declaredMentoringFour->startTime,
            'endTime' => $this->declaredMentoringFour->endTime,
            'mediaType' => $this->declaredMentoringFour->mediaType,
            'location' => $this->declaredMentoringFour->location,
            'mentor' => [
                'id' => $this->declaredMentoringFour->mentor->id,
                'personnel' => [
                    'id' => $this->declaredMentoringFour->mentor->personnel->id,
                    'name' => $this->declaredMentoringFour->mentor->personnel->getFullName(),
                ],
            ],
            'consultationSetup' => [
                'id' => $this->declaredMentoringFour->consultationSetup->id,
                'name' => $this->declaredMentoringFour->consultationSetup->name,
                'participantFeedbackForm' => null,
                'mentorFeedbackForm' => null,
            ],
            'participantReport' => null,
            'mentorReport' => null,
        ];
        $this->seeJsonContains($respose);
    }
    public function test_viewDeclaredMentoringDetail_mentoringRequestBelongsToParticipantOfOtherProgram_404()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->declaredMentoringFour->participant->program = $otherProgram;
        
        $this->viewDeclaredMentoringDetail();
        $this->seeStatusCode(404);
    }
    public function test_viewDeclaredMentoringDetail_inactiveConsultant_403()
    {
        $this->consultant->active = false;
        
        $this->viewDeclaredMentoringDetail();
        $this->seeStatusCode(403);
    }

}
