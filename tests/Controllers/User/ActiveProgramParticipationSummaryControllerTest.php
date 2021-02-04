<?php

namespace Tests\Controllers\User;

use DateTime;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\ConsultationSession\RecordOfConsultantFeedback;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfConsultationSession;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet\RecordOfCompletedMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\RecordOfFirm;
use Tests\Controllers\RecordPreparation\RecordOfUser;
use Tests\Controllers\RecordPreparation\User\RecordOfUserParticipant;

class ActiveProgramParticipationSummaryControllerTest extends UserTestCase
{
    protected $activeProgramParticipationSummaryUri;
    
    protected $programOne;
    protected $programTwo;
    protected $programThree;
    
    protected $mission_11;
    protected $mission_12;
    protected $mission_13_unpublished;
    protected $mission_21;
    protected $mission_22;
    protected $mission_31;
    
    protected $programParticipationOne;
    protected $programParticipationTwo;
    
    protected $completedMission_11;
    protected $completedMission_12;
    protected $completedMission_21;
    
    protected $consultantFeedback_11;
    protected $consultantFeedback_12;
    protected $consultantFeedback_21;

    protected function setUp(): void
    {
        parent::setUp();
        $this->activeProgramParticipationSummaryUri = $this->userUri . '/active-program-participation-summaries';
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        $this->connection->table('CompletedMission')->truncate();
        $this->connection->table('ConsultationSession')->truncate();
        $this->connection->table('ConsultantFeedback')->truncate();
        
        $userTwo = new RecordOfUser('2');
        $this->connection->table('User')->insert($userTwo->toArrayForDbEntry());
        
        $firm = new RecordOfFirm(0);
        $this->connection->table('Firm')->insert($firm->toArrayForDbEntry());
        
        $this->programOne = new RecordOfProgram($firm, 1);
        $this->programTwo = new RecordOfProgram($firm, 2);
        $this->programThree = new RecordOfProgram($firm, 3);
        $this->connection->table('Program')->insert($this->programOne->toArrayForDbEntry());
        $this->connection->table('Program')->insert($this->programTwo->toArrayForDbEntry());
        $this->connection->table('Program')->insert($this->programThree->toArrayForDbEntry());
        
        $this->mission_11 = new RecordOfMission($this->programOne, null, '11', null);
        $this->mission_11->published = true;
        $this->mission_12 = new RecordOfMission($this->programOne, null, '12', null);
        $this->mission_12->published = true;
        $this->mission_13_unpublished = new RecordOfMission($this->programOne, null, '13', null);
        $this->mission_21 = new RecordOfMission($this->programTwo, null, '21', null);
        $this->mission_21->published = true;
        $this->mission_22 = new RecordOfMission($this->programTwo, null, '22', null);
        $this->mission_22->published = true;
        $this->mission_31 = new RecordOfMission($this->programThree, null, '31', null);
        $this->mission_31->published = true;
        $this->connection->table('Mission')->insert($this->mission_11->toArrayForDbEntry());
        $this->connection->table('Mission')->insert($this->mission_12->toArrayForDbEntry());
        $this->connection->table('Mission')->insert($this->mission_13_unpublished->toArrayForDbEntry());
        $this->connection->table('Mission')->insert($this->mission_21->toArrayForDbEntry());
        $this->connection->table('Mission')->insert($this->mission_22->toArrayForDbEntry());
        $this->connection->table('Mission')->insert($this->mission_31->toArrayForDbEntry());
        
        $participantOne = new RecordOfParticipant($this->programOne, '1');
        $participantTwo = new RecordOfParticipant($this->programTwo, '2');
        $participantThree = new RecordOfParticipant($this->programThree, '3');
        $participantThree->active = false;
        $participant_21 = new RecordOfParticipant($this->programOne, '21');
        $this->connection->table('Participant')->insert($participantOne->toArrayForDbEntry());
        $this->connection->table('Participant')->insert($participantTwo->toArrayForDbEntry());
        $this->connection->table('Participant')->insert($participantThree->toArrayForDbEntry());
        $this->connection->table('Participant')->insert($participant_21->toArrayForDbEntry());
        
        $this->programParticipationOne = new RecordOfUserParticipant($this->user, $participantOne);
        $this->programParticipationTwo = new RecordOfUserParticipant($this->user, $participantTwo);
        $programParticipationThree = new RecordOfUserParticipant($this->user, $participantThree);
        $programParticipation_21 = new RecordOfUserParticipant($userTwo, $participant_21);
        $this->connection->table('UserParticipant')->insert($this->programParticipationOne->toArrayForDbEntry());
        $this->connection->table('UserParticipant')->insert($this->programParticipationTwo->toArrayForDbEntry());
        $this->connection->table('UserParticipant')->insert($programParticipationThree->toArrayForDbEntry());
        $this->connection->table('UserParticipant')->insert($programParticipation_21->toArrayForDbEntry());
        
        $this->completedMission_11 = new RecordOfCompletedMission($participantOne, $this->mission_11, '11');
        $this->completedMission_11->completedTime = (new DateTime('-12 hours'))->format('Y-m-d H:i:s');
        $this->completedMission_12 = new RecordOfCompletedMission($participantOne, $this->mission_12, '12');
        $this->completedMission_12->completedTime = (new DateTime('-24 hours'))->format('Y-m-d H:i:s');
        $this->completedMission_21 = new RecordOfCompletedMission($participantTwo, $this->mission_21, '21');
        $this->completedMission_21->completedTime = (new DateTime('-8 hours'))->format('Y-m-d H:i:s');
        $this->completedMission_211 = new RecordOfCompletedMission($participant_21, $this->mission_11, '211');
        $this->completedMission_211->completedTime = (new DateTime('-4 hours'))->format('Y-m-d H:i:s');
        $this->connection->table('CompletedMission')->insert($this->completedMission_11->toArrayForDbEntry());
        $this->connection->table('CompletedMission')->insert($this->completedMission_12->toArrayForDbEntry());
        $this->connection->table('CompletedMission')->insert($this->completedMission_21->toArrayForDbEntry());
        $this->connection->table('CompletedMission')->insert($this->completedMission_211->toArrayForDbEntry());
        
        $consultationSession_11 = new RecordOfConsultationSession(null, $participantOne, null, '11');
        $consultationSession_12 = new RecordOfConsultationSession(null, $participantOne, null, '12');
        $consultationSession_21 = new RecordOfConsultationSession(null, $participantTwo, null, '21');
        $this->connection->table('ConsultationSession')->insert($consultationSession_11->toArrayForDbEntry());
        $this->connection->table('ConsultationSession')->insert($consultationSession_12->toArrayForDbEntry());
        $this->connection->table('ConsultationSession')->insert($consultationSession_21->toArrayForDbEntry());
        
        $this->consultantFeedback_11 = new RecordOfConsultantFeedback($consultationSession_11, null, "11");
        $this->consultantFeedback_11->participantRating = 2;
        $this->consultantFeedback_12 = new RecordOfConsultantFeedback($consultationSession_12, null, "12");
        $this->consultantFeedback_12->participantRating = 3;
        $this->consultantFeedback_21 = new RecordOfConsultantFeedback($consultationSession_21, null, "21");
        $this->consultantFeedback_21->participantRating = 2;
        $this->connection->table('ConsultantFeedback')->insert($this->consultantFeedback_11->toArrayForDbEntry());
        $this->connection->table('ConsultantFeedback')->insert($this->consultantFeedback_12->toArrayForDbEntry());
        $this->connection->table('ConsultantFeedback')->insert($this->consultantFeedback_21->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        $this->connection->table('CompletedMission')->truncate();
        $this->connection->table('ConsultationSession')->truncate();
        $this->connection->table('ConsultantFeedback')->truncate();
    }
    
    public function test_showAll_200()
    {
        $this->get($this->activeProgramParticipationSummaryUri, $this->user->token);
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 2];
        $this->seeJsonContains($totalResponse);
        
        $programOneResponse = [
            'programId' => $this->programOne->id,
            'programName' => $this->programOne->name,
            'participantId' => $this->programParticipationOne->id,
            'participantRating' => '2.5000',
            'totalCompletedMission' => '2',
            'totalMission' => '2',
            'lastCompletedTime' => $this->completedMission_11->completedTime,
            'lastMissionId' => $this->completedMission_11->mission->id,
            'lastMissionName' => $this->completedMission_11->mission->name,
        ];
        $this->seeJsonContains($programOneResponse);
        
        $programTwoResponse = [
            'programId' => $this->programTwo->id,
            'programName' => $this->programTwo->name,
            'participantId' => $this->programParticipationTwo->id,
            'participantRating' => '2.0000',
            'totalCompletedMission' => '1',
            'totalMission' => '2',
            'lastCompletedTime' => $this->completedMission_21->completedTime,
            'lastMissionId' => $this->completedMission_21->mission->id,
            'lastMissionName' => $this->completedMission_21->mission->name,
        ];
        $this->seeJsonContains($programTwoResponse);
    }
}
