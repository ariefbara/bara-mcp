<?php

namespace Tests\Controllers\Client;

use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\DedicatedMentor\RecordOfMentorEvaluationReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfDedicatedMentor;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfEvaluationPlan;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFeedbackForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class EvaluationReportControllerTest extends ClientTestCase
{
    protected $evaluationReportUri;
    protected $evaluationReportOne;
    protected $evaluationReportTwo;
    protected $clientParticipantOne;
    protected $teamParticipantTwo;
    protected $teamMemberOne;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('EvaluationPlan')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('MentorEvaluationReport')->truncate();
        
        $this->evaluationReportUri = $this->clientUri . "/evaluation-reports";

        $firm = $this->client->firm;

        $programOne = new RecordOfProgram($firm, '1');
        $programTwo = new RecordOfProgram($firm, '2');

        $teamOne = new RecordOfTeam($firm, $this->client, '1');
        
        $this->teamMemberOne = new RecordOfMember($teamOne, $this->client, '1');
        
        $participantOne = new RecordOfParticipant($programOne, '1');
        $this->clientParticipantOne = new RecordOfClientParticipant($this->client, $participantOne);
        $participantTwo = new RecordOfParticipant($programTwo, '2');
        $this->teamParticipantTwo = new RecordOfTeamProgramParticipation($teamOne, $participantTwo);

        $personnelOne = new RecordOfPersonnel($firm, '1');
        $personnelTwo = new RecordOfPersonnel($firm, '2');

        $consultantOne = new RecordOfConsultant($programOne, $personnelOne, '1');
        $consultantTwo = new RecordOfConsultant($programTwo, $personnelTwo, '2');

        $dedicatedMentorOne = new RecordOfDedicatedMentor($participantOne, $consultantOne, '1');
        $dedicatedMentorTwo = new RecordOfDedicatedMentor($participantTwo, $consultantTwo, '2');

        $formOne = new RecordOfForm('1');
        $feedbackFormOne = new RecordOfFeedbackForm($firm, $formOne);
        $formTwo = new RecordOfForm('2');
        $feedbackFormTwo = new RecordOfFeedbackForm($firm, $formTwo);

        $evaluationPlanOne = new RecordOfEvaluationPlan($programOne, $feedbackFormOne, '1');
        $evaluationPlanTwo = new RecordOfEvaluationPlan($programTwo, $feedbackFormTwo, '2');

        $formRecordOne = new RecordOfFormRecord($formOne, '1');
        $this->evaluationReportOne = new RecordOfMentorEvaluationReport(
                $dedicatedMentorOne, $evaluationPlanOne, $formRecordOne);
        $formRecordTwo = new RecordOfFormRecord($formTwo, '2');
        $this->evaluationReportTwo = new RecordOfMentorEvaluationReport(
                $dedicatedMentorTwo, $evaluationPlanTwo, $formRecordTwo);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('EvaluationPlan')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('MentorEvaluationReport')->truncate();
    }
    
    protected function showAll()
    {
        $this->evaluationReportOne->evaluationPlan->program->insert($this->connection);
        $this->evaluationReportTwo->evaluationPlan->program->insert($this->connection);
        
        $this->evaluationReportOne->evaluationPlan->feedbackForm->insert($this->connection);
        $this->evaluationReportTwo->evaluationPlan->feedbackForm->insert($this->connection);
        
        $this->evaluationReportOne->evaluationPlan->insert($this->connection);
        $this->evaluationReportTwo->evaluationPlan->insert($this->connection);
        
        $this->evaluationReportOne->dedicatedMentor->consultant->personnel->insert($this->connection);
        $this->evaluationReportTwo->dedicatedMentor->consultant->personnel->insert($this->connection);
        
        $this->evaluationReportOne->dedicatedMentor->consultant->insert($this->connection);
        $this->evaluationReportTwo->dedicatedMentor->consultant->insert($this->connection);
        
        $this->clientParticipantOne->insert($this->connection);
        
        $this->teamParticipantTwo->team->insert($this->connection);
        $this->teamMemberOne->insert($this->connection);
        $this->teamParticipantTwo->insert($this->connection);
        
        $this->evaluationReportOne->dedicatedMentor->insert($this->connection);
        $this->evaluationReportTwo->dedicatedMentor->insert($this->connection);
        
        $this->evaluationReportOne->insert($this->connection);
        $this->evaluationReportTwo->insert($this->connection);
        
        $this->get($this->evaluationReportUri, $this->client->token);
    }
    public function test_showAll_200()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        
        $result = [
            'total' => 2,
            'list' => [
                [
                    'id' => $this->evaluationReportOne->formRecord->id,
                    'modifiedTime' => $this->evaluationReportOne->modifiedTime,
                    'evaluationPlan'=> [
                        'id' => $this->evaluationReportOne->evaluationPlan->id,
                        'name' => $this->evaluationReportOne->evaluationPlan->name,
                    ],
                    'mentor' => [
                        'id' => $this->evaluationReportOne->dedicatedMentor->consultant->id,
                        'personnel' => [
                            'id' => $this->evaluationReportOne->dedicatedMentor->consultant->personnel->id,
                            'name' => $this->evaluationReportOne->dedicatedMentor->consultant->personnel->getFullName(),
                        ],
                    ],
                    'team' => null,
                ],
                [
                    'id' => $this->evaluationReportTwo->formRecord->id,
                    'modifiedTime' => $this->evaluationReportTwo->modifiedTime,
                    'evaluationPlan'=> [
                        'id' => $this->evaluationReportTwo->evaluationPlan->id,
                        'name' => $this->evaluationReportTwo->evaluationPlan->name,
                    ],
                    'mentor' => [
                        'id' => $this->evaluationReportTwo->dedicatedMentor->consultant->id,
                        'personnel' => [
                            'id' => $this->evaluationReportTwo->dedicatedMentor->consultant->personnel->id,
                            'name' => $this->evaluationReportTwo->dedicatedMentor->consultant->personnel->getFullName(),
                        ],
                    ],
                    'team' => [
                        'id' => $this->teamParticipantTwo->team->id,
                        'name' => $this->teamParticipantTwo->team->name,
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($result);
    }
    public function test_showAll_containCancelledReport_excludeFromResult()
    {
        $this->evaluationReportOne->cancelled = true;
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        
        $reportResponse = [
            'id' => $this->evaluationReportTwo->formRecord->id,
        ];
        $this->seeJsonContains($reportResponse);
    }
    public function test_showAll_inactiveMember_excludeFromResult()
    {
        $this->teamMemberOne->active = false;
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        
        $reportResponse = [
            'id' => $this->evaluationReportOne->formRecord->id,
        ];
        $this->seeJsonContains($reportResponse);
    }
    
    protected function show()
    {
        $this->evaluationReportTwo->evaluationPlan->program->insert($this->connection);
        
        $this->evaluationReportTwo->evaluationPlan->feedbackForm->insert($this->connection);
        
        $this->evaluationReportTwo->evaluationPlan->insert($this->connection);
        
        $this->evaluationReportTwo->dedicatedMentor->consultant->personnel->insert($this->connection);
        
        $this->evaluationReportTwo->dedicatedMentor->consultant->insert($this->connection);
        
        $this->teamParticipantTwo->team->insert($this->connection);
        $this->teamMemberOne->insert($this->connection);
        $this->teamParticipantTwo->insert($this->connection);
        
        $this->evaluationReportTwo->dedicatedMentor->insert($this->connection);
        
        $this->evaluationReportTwo->insert($this->connection);
        
        $uri = $this->evaluationReportUri . "/{$this->evaluationReportTwo->formRecord->id}";
        $this->get($uri, $this->client->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        $result = [
            'id' => $this->evaluationReportTwo->formRecord->id,
            'modifiedTime' => $this->evaluationReportTwo->modifiedTime,
            'evaluationPlan'=> [
                'id' => $this->evaluationReportTwo->evaluationPlan->id,
                'name' => $this->evaluationReportTwo->evaluationPlan->name,
            ],
            'mentor' => [
                'id' => $this->evaluationReportTwo->dedicatedMentor->consultant->id,
                'personnel' => [
                    'id' => $this->evaluationReportTwo->dedicatedMentor->consultant->personnel->id,
                    'name' => $this->evaluationReportTwo->dedicatedMentor->consultant->personnel->getFullName(),
                ],
            ],
            'team' => [
                'id' => $this->teamParticipantTwo->team->id,
                'name' => $this->teamParticipantTwo->team->name,
            ],
            'stringFieldRecords' => [],
            'integerFieldRecords' => [],
            'textAreaFieldRecords' => [],
            'attachmentFieldRecords' => [],
            'singleSelectFieldRecords' => [],
            'multiSelectFieldRecords' => [],
        ];
        $this->seeJsonContains($result);
    }
    public function test_show_cancelledReport_404()
    {
        $this->evaluationReportTwo->cancelled = true;
        $this->show();
        $this->seeStatusCode(404);
    }
    public function test_show_inactiveMember_400()
    {
        $this->teamMemberOne->active = false;
        $this->show();
        $this->seeStatusCode(404);
    }

}
