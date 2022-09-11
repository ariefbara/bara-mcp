<?php

namespace Tests\Controllers\Personnel;

use Tests\Controllers\RecordPreparation\Firm\Program\Activity\Invitee\RecordOfInviteeReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Activity\RecordOfInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\ActivityType\RecordOfActivityParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfConsultantInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivity;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfActivityType;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFeedbackForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class ConsultantInviteeControllerTest extends PersonnelTestCase
{
    protected $consultantInviteeOne;
    protected $consultantInviteeTwo;
    protected $allWithPendingReportUri;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('Activity')->truncate();
        $this->connection->table('ActivityType')->truncate();
        $this->connection->table('ActivityParticipant')->truncate();
        $this->connection->table('Invitee')->truncate();
        $this->connection->table('ConsultantInvitee')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('InviteeReport')->truncate();
        
        $firm = $this->personnel->firm;

        $programOne = new RecordOfProgram($firm, '1');
        $programTwo = new RecordOfProgram($firm, '2');

        $mentorOne = new RecordOfConsultant($programOne, $this->personnel, '1');
        $mentorTwo = new RecordOfConsultant($programTwo, $this->personnel, '2');

        $activityTypeOne = new RecordOfActivityType($programOne, '1');
        $activityTypeTwo = new RecordOfActivityType($programTwo, '2');

        $activityOne = new RecordOfActivity($activityTypeOne, '1');
        $activityTwo = new RecordOfActivity($activityTypeTwo, '2');

        $formOne = new RecordOfForm('1');
        $formTwo = new RecordOfForm('2');
        
        $feedbackFormOne = new RecordOfFeedbackForm($firm, $formOne);
        $feedbackFormTwo = new RecordOfFeedbackForm($firm, $formTwo);

        $activityParticipantOne = new RecordOfActivityParticipant($activityTypeOne, $feedbackFormOne, '1');
        $activityParticipantTwo = new RecordOfActivityParticipant($activityTypeTwo, $feedbackFormTwo, '2');

        $inviteeOne = new RecordOfInvitee($activityOne, $activityParticipantOne, '1');
        $inviteeTwo = new RecordOfInvitee($activityTwo, $activityParticipantTwo, '2');

        $this->consultantInviteeOne = new RecordOfConsultantInvitee($mentorOne, $inviteeOne);
        $this->consultantInviteeTwo = new RecordOfConsultantInvitee($mentorTwo, $inviteeTwo);
        
        $this->allWithPendingReportUri = $this->personnelUri . "/consultant-invitees/all-with-pending-report";
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('Activity')->truncate();
        $this->connection->table('ActivityType')->truncate();
        $this->connection->table('ActivityParticipant')->truncate();
        $this->connection->table('Invitee')->truncate();
        $this->connection->table('ConsultantInvitee')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('InviteeReport')->truncate();
    }
    
    protected function allWithPendingReport()
    {
        
        $this->consultantInviteeOne->consultant->program->insert($this->connection);
        $this->consultantInviteeTwo->consultant->program->insert($this->connection);
        
        $this->consultantInviteeOne->consultant->insert($this->connection);
        $this->consultantInviteeTwo->consultant->insert($this->connection);
        
        $this->consultantInviteeOne->invitee->activity->activityType->insert($this->connection);
        $this->consultantInviteeTwo->invitee->activity->activityType->insert($this->connection);
        
        $this->consultantInviteeOne->invitee->activity->insert($this->connection);
        $this->consultantInviteeTwo->invitee->activity->insert($this->connection);
        
        $this->consultantInviteeOne->invitee->activityParticipant->feedbackForm->insert($this->connection);
        $this->consultantInviteeTwo->invitee->activityParticipant->feedbackForm->insert($this->connection);
        
        $this->consultantInviteeOne->invitee->activityParticipant->insert($this->connection);
        $this->consultantInviteeTwo->invitee->activityParticipant->insert($this->connection);
        
        $this->consultantInviteeOne->insert($this->connection);
        $this->consultantInviteeTwo->insert($this->connection);
        
        $this->get($this->allWithPendingReportUri, $this->personnel->token);
    }
    public function test_allWithPendingReport_200()
    {
$this->disableExceptionHandling();
        $this->allWithPendingReport();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 2,
            'list' => [
                [
                    'id' => $this->consultantInviteeOne->invitee->id,
                    'anInitiator' => $this->consultantInviteeOne->invitee->anInitiator,
                    'consultant' => [
                        'id' => $this->consultantInviteeOne->consultant->id,
                        'program' => [
                            'id' => $this->consultantInviteeOne->consultant->program->id,
                            'name' => $this->consultantInviteeOne->consultant->program->name,
                        ],
                    ],
                    'activity' => [
                        'id' => $this->consultantInviteeOne->invitee->activity->id,
                        'name' => $this->consultantInviteeOne->invitee->activity->name,
                        'startTime' => $this->consultantInviteeOne->invitee->activity->startDateTime,
                        'endTime' => $this->consultantInviteeOne->invitee->activity->endDateTime,
                    ],
                ],
                [
                    'id' => $this->consultantInviteeTwo->invitee->id,
                    'anInitiator' => $this->consultantInviteeTwo->invitee->anInitiator,
                    'consultant' => [
                        'id' => $this->consultantInviteeTwo->consultant->id,
                        'program' => [
                            'id' => $this->consultantInviteeTwo->consultant->program->id,
                            'name' => $this->consultantInviteeTwo->consultant->program->name,
                        ],
                    ],
                    'activity' => [
                        'id' => $this->consultantInviteeTwo->invitee->activity->id,
                        'name' => $this->consultantInviteeTwo->invitee->activity->name,
                        'startTime' => $this->consultantInviteeTwo->invitee->activity->startDateTime,
                        'endTime' => $this->consultantInviteeTwo->invitee->activity->endDateTime,
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_allWithPendingReport_excludeCompleteInvitation()
    {
        $formRecord = new RecordOfFormRecord($this->consultantInviteeOne->invitee->activityParticipant->feedbackForm->form, '1');
        $inviteeReport = new RecordOfInviteeReport($this->consultantInviteeOne->invitee, $formRecord);
        $inviteeReport->insert($this->connection);
        
        $this->allWithPendingReport();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 1]);
        $this->seeJsonDoesntContains(['id' => $this->consultantInviteeOne->invitee->id]);
        $this->seeJsonContains(['id' => $this->consultantInviteeTwo->invitee->id]);
    }
    public function test_allWithPendingReport_excludeInvitationToOtherPersonnel()
    {
        $program = $this->consultantInviteeOne->consultant->program;
        $otherPersonnel = new RecordOfPersonnel($this->personnel->firm, 'other');
        $otherPersonnel->insert($this->connection);
        
        $otherConsultant = new RecordOfConsultant($program, $otherPersonnel, 'other');
        $this->consultantInviteeOne->consultant = $otherConsultant;
        
        $this->allWithPendingReport();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 1]);
        $this->seeJsonDoesntContains(['id' => $this->consultantInviteeOne->invitee->id]);
        $this->seeJsonContains(['id' => $this->consultantInviteeTwo->invitee->id]);
    }
    public function test_allWithPendingReport_excludeCancelledInvitation()
    {
        $this->consultantInviteeOne->invitee->cancelled = true;
        
        $this->allWithPendingReport();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 1]);
        $this->seeJsonDoesntContains(['id' => $this->consultantInviteeOne->invitee->id]);
        $this->seeJsonContains(['id' => $this->consultantInviteeTwo->invitee->id]);
    }
    public function test_allWithPendingReport_fromFilter()
    {
        $this->consultantInviteeOne->invitee->activity->startDateTime = (new \DateTime('+72 hours'))->format('Y-m-d H:i:s');
        $this->consultantInviteeOne->invitee->activity->endDateTime = (new \DateTime('+73 hours'))->format('Y-m-d H:i:s');
        
        $from = (new \DateTime('+70 hours'))->format('Y-m-d H:i:s');
        $this->allWithPendingReportUri .= "?from=$from";
        
        $this->allWithPendingReport();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 1]);
        $this->seeJsonContains(['id' => $this->consultantInviteeOne->invitee->id]);
        $this->seeJsonDoesntContains(['id' => $this->consultantInviteeTwo->invitee->id]);
    }
    public function test_allWithPendingReport_toFilter()
    {
        $this->consultantInviteeOne->invitee->activity->startDateTime = (new \DateTime('-73 hours'))->format('Y-m-d H:i:s');
        $this->consultantInviteeOne->invitee->activity->endDateTime = (new \DateTime('-72 hours'))->format('Y-m-d H:i:s');
        
        $to = (new \DateTime('-72 hours'))->format('Y-m-d H:i:s');
        $this->allWithPendingReportUri .= "?to=$to";
        
        $this->allWithPendingReport();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 1]);
        $this->seeJsonContains(['id' => $this->consultantInviteeOne->invitee->id]);
        $this->seeJsonDoesntContains(['id' => $this->consultantInviteeTwo->invitee->id]);
    }

}
