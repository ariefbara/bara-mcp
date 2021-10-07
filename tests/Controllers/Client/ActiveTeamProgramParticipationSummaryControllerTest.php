<?php

namespace Tests\Controllers\Client;

use DateTime;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\ConsultationSession\RecordOfConsultantFeedback;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfConsultationSession;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet\RecordOfCompletedMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFirmFileInfo;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFileInfo;

class ActiveTeamProgramParticipationSummaryControllerTest extends ClientTestCase
{
    protected $activeTeamProgramParticipationSummaryUri;
    
    protected $programOne;
    protected $programTwo;
    protected $programThree;
    
    protected $mission_11;
    protected $mission_12;
    protected $mission_13_unpublished;
    protected $mission_21;
    protected $mission_22;
    protected $mission_31;
    
    protected $teamOne;
    protected $teamTwo;
    
    protected $teamMember_11;
    protected $teamMember_21;


    protected $programParticipation_11;
    protected $programParticipation_12;
    protected $programParticipation_21;
    
    protected $completedMission_111;
    protected $completedMission_112;
    protected $completedMission_121;
    protected $completedMission_211;
    
    protected $consultantFeedback_111;
    protected $consultantFeedback_112;
    protected $consultantFeedback_121;
    protected $consultantFeedback_211;

    protected function setUp(): void
    {
        parent::setUp();
        $this->activeTeamProgramParticipationSummaryUri = $this->clientUri . '/active-team-program-participation-summaries';
        
        $this->connection->table('Program')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('CompletedMission')->truncate();
        $this->connection->table('ConsultationSession')->truncate();
        $this->connection->table('ConsultantFeedback')->truncate();
        $this->connection->table('FirmFileInfo')->truncate();
        $this->connection->table('FileInfo')->truncate();
        
        $firm = $this->client->firm;
        
        $fileInfoTwo = new RecordOfFileInfo("2");
        $fileInfoTwo->folders = "firms,2";
        $firmFileInfoTwo = new RecordOfFirmFileInfo($firm, $fileInfoTwo);
        $firmFileInfoTwo->insert($this->connection);
        
        $this->programOne = new RecordOfProgram($firm, '1');
        $this->programTwo = new RecordOfProgram($firm, '2');
        $this->programTwo->illustration = $firmFileInfoTwo;
        $this->programThree = new RecordOfProgram($firm, '3');
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
        
        $this->teamOne = new RecordOfTeam($firm, $this->client, '1');
        $this->teamTwo = new RecordOfTeam($firm, $this->client, '2');
        $this->connection->table('Team')->insert($this->teamOne->toArrayForDbEntry());
        $this->connection->table('Team')->insert($this->teamTwo->toArrayForDbEntry());
        
        $this->teamMember_11 = new RecordOfMember($this->teamOne, $this->client, '11');
        $this->teamMember_21 = new RecordOfMember($this->teamTwo, $this->client, '21');
        $this->connection->table('T_Member')->insert($this->teamMember_11->toArrayForDbEntry());
        $this->connection->table('T_Member')->insert($this->teamMember_21->toArrayForDbEntry());
        
        $participant_11 = new RecordOfParticipant($this->programOne, '11');
        $participant_12 = new RecordOfParticipant($this->programTwo, '12');
        $participant_21 = new RecordOfParticipant($this->programOne, '21');
        $this->connection->table('Participant')->insert($participant_11->toArrayForDbEntry());
        $this->connection->table('Participant')->insert($participant_12->toArrayForDbEntry());
        $this->connection->table('Participant')->insert($participant_21->toArrayForDbEntry());
        
        $this->programParticipation_11 = new RecordOfTeamProgramParticipation($this->teamOne, $participant_11);
        $this->programParticipation_12 = new RecordOfTeamProgramParticipation($this->teamOne, $participant_12);
        $this->programParticipation_21 = new RecordOfTeamProgramParticipation($this->teamTwo, $participant_21);
        $this->connection->table('TeamParticipant')->insert($this->programParticipation_11->toArrayForDbEntry());
        $this->connection->table('TeamParticipant')->insert($this->programParticipation_12->toArrayForDbEntry());
        $this->connection->table('TeamParticipant')->insert($this->programParticipation_21->toArrayForDbEntry());
        
        $this->completedMission_111 = new RecordOfCompletedMission($participant_11, $this->mission_11, '111');
        $this->completedMission_111->completedTime = (new DateTime('-8 days'))->format('Y-m-d H:i:s');
        $this->completedMission_112 = new RecordOfCompletedMission($participant_11, $this->mission_12, '112');
        $this->completedMission_112->completedTime = (new DateTime('-16 days'))->format('Y-m-d H:i:s');
        $this->completedMission_211 = new RecordOfCompletedMission($participant_21, $this->mission_11, '211');
        $this->completedMission_211->completedTime = (new DateTime('-4 days'))->format('Y-m-d H:i:s');
        $this->completedMission_121 = new RecordOfCompletedMission($participant_12, $this->mission_21, '121');
        $this->completedMission_121->completedTime = (new DateTime('-2 days'))->format('Y-m-d H:i:s');
        $this->connection->table('CompletedMission')->insert($this->completedMission_111->toArrayForDbEntry());
        $this->connection->table('CompletedMission')->insert($this->completedMission_112->toArrayForDbEntry());
        $this->connection->table('CompletedMission')->insert($this->completedMission_211->toArrayForDbEntry());
        $this->connection->table('CompletedMission')->insert($this->completedMission_121->toArrayForDbEntry());
        
        $consultationSession_11_1 = new RecordOfConsultationSession(null, $participant_11, null, '111');
        $consultationSession_11_2 = new RecordOfConsultationSession(null, $participant_11, null, '112');
        $consultationSession_12_1 = new RecordOfConsultationSession(null, $participant_12, null, '121');
        $consultationSession_21_1 = new RecordOfConsultationSession(null, $participant_21, null, '211');
        $this->connection->table('ConsultationSession')->insert($consultationSession_11_1->toArrayForDbEntry());
        $this->connection->table('ConsultationSession')->insert($consultationSession_11_2->toArrayForDbEntry());
        $this->connection->table('ConsultationSession')->insert($consultationSession_12_1->toArrayForDbEntry());
        $this->connection->table('ConsultationSession')->insert($consultationSession_21_1->toArrayForDbEntry());
                
        $this->consultantFeedback_11_1 = new RecordOfConsultantFeedback($consultationSession_11_1, null, "111");
        $this->consultantFeedback_11_1->participantRating = 2;
        $this->consultantFeedback_11_2 = new RecordOfConsultantFeedback($consultationSession_11_2, null, "112");
        $this->consultantFeedback_11_2->participantRating = 3;
        $this->consultantFeedback_12_1 = new RecordOfConsultantFeedback($consultationSession_12_1, null, "121");
        $this->consultantFeedback_12_1->participantRating = 4;
        $this->consultantFeedback_21_1 = new RecordOfConsultantFeedback($consultationSession_21_1, null, "211");
        $this->consultantFeedback_21_1->participantRating = 5;
        $this->connection->table('ConsultantFeedback')->insert($this->consultantFeedback_11_1->toArrayForDbEntry());
        $this->connection->table('ConsultantFeedback')->insert($this->consultantFeedback_11_2->toArrayForDbEntry());
        $this->connection->table('ConsultantFeedback')->insert($this->consultantFeedback_12_1->toArrayForDbEntry());
        $this->connection->table('ConsultantFeedback')->insert($this->consultantFeedback_21_1->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('CompletedMission')->truncate();
        $this->connection->table('ConsultationSession')->truncate();
        $this->connection->table('ConsultantFeedback')->truncate();
        $this->connection->table('FirmFileInfo')->truncate();
        $this->connection->table('FileInfo')->truncate();
    }
    
    public function test_showAll_200()
    {
        $this->get($this->activeTeamProgramParticipationSummaryUri, $this->client->token);
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 2];
        $this->seeJsonContains($totalResponse);
        
        $teamOneResponse = [
            "id" => $this->teamMember_11->id,
            "position" => $this->teamMember_11->position,
            'team' => [
                "id" => $this->teamMember_11->team->id,
                "name" => $this->teamMember_11->team->name,
                "programParticipationSummaries" => [
                    'total' => 2,
                    'list' => [
                        [
                            'programId' => $this->programOne->id,
                            'programName' => $this->programOne->name,
                            'programStrictMissionOrder' => strval(intval($this->programOne->strictMissionOrder)),
                            'participantId' => $this->programParticipation_11->id,
                            "programIllustration" => "",
                            'participantRating' => '2.5000',
                            'totalCompletedMission' => '2',
                            'totalMission' => '2',
                            'lastCompletedTime' => $this->completedMission_111->completedTime,
                            'lastMissionId' => $this->completedMission_111->mission->id,
                            'lastMissionName' => $this->completedMission_111->mission->name,
                            'achievement' => null,
                            'completedMetric' => null,
                            'totalAssignedMetric' => null,
                            'reportId' => null,
                        ],
                        [
                            'programId' => $this->programTwo->id,
                            'programName' => $this->programTwo->name,
                            'programStrictMissionOrder' => strval(intval($this->programTwo->strictMissionOrder)),
                            'participantId' => $this->programParticipation_12->id,
                            'programIllustration' => "firms/2/{$this->programTwo->illustration->fileInfo->name}",
                            'participantRating' => '4.0000',
                            'totalCompletedMission' => '1',
                            'totalMission' => '2',
                            'lastCompletedTime' => $this->completedMission_121->completedTime,
                            'lastMissionId' => $this->completedMission_121->mission->id,
                            'lastMissionName' => $this->completedMission_121->mission->name,
                            'achievement' => null,
                            'completedMetric' => null,
                            'totalAssignedMetric' => null,
                            'reportId' => null,
                        ],
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($teamOneResponse);
        $teamTwoResponse = [
            "id" => $this->teamMember_21->id,
            "position" => $this->teamMember_21->position,
            'team' => [
                "id" => $this->teamMember_21->team->id,
                "name" => $this->teamMember_21->team->name,
                "programParticipationSummaries" => [
                    'total' => 1,
                    'list' => [
                        [
                            'programId' => $this->programOne->id,
                            'programName' => $this->programOne->name,
                            'programStrictMissionOrder' => strval(intval($this->programOne->strictMissionOrder)),
                            'participantId' => $this->programParticipation_21->id,
                            "programIllustration" => "",
                            'participantRating' => '5.0000',
                            'totalCompletedMission' => '1',
                            'totalMission' => '2',
                            'lastCompletedTime' => $this->completedMission_211->completedTime,
                            'lastMissionId' => $this->completedMission_211->mission->id,
                            'lastMissionName' => $this->completedMission_211->mission->name,
                            'achievement' => null,
                            'completedMetric' => null,
                            'totalAssignedMetric' => null,
                            'reportId' => null,
                        ],
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($teamTwoResponse);
    }
}
