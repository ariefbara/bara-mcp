<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\DedicatedMentor\RecordOfMentorEvaluationReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfDedicatedMentor;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfEvaluationPlan;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFeedbackForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfIntegerField;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfStringField;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfTextAreaField;
use Tests\Controllers\RecordPreparation\Shared\FormRecord\RecordOfIntegerFieldRecord;
use Tests\Controllers\RecordPreparation\Shared\FormRecord\RecordOfStringFieldRecord;
use Tests\Controllers\RecordPreparation\Shared\FormRecord\RecordOfTextAreaFieldRecord;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class EvaluationReportSummaryTest extends ManagerTestCase
{
    protected $evaluationReportSummaryUri;
    protected $evaluationReportTranscriptUri;
    
    protected $evaluationPlan_11_prog1;
    protected $evaluationPlan_21_prog2;
    
    protected $stringField_11, $integerField_11;
    protected $textAreaField_21, $integerField_21;
    
    protected $clientOne;
    protected $clientTwo;
    protected $teamOne;
    protected $teamTwo;

    protected $clientParticipant_11_prog1_c1;
    protected $clientParticipant_12_prog1_c2;
    
    protected $teamParticipant_21_prog2_t1;
    protected $teamParticipant_22_prog2_t2;
    
    protected $member_11_t1_c1;
    protected $member_12_t1_c2;
    protected $member_21_t2_c1;
    
    protected $personnelOne;
    protected $personnelTwo;

    protected $evaluationReport_1111_dm111_ep11_c1_pers1, $stringRecord_111_fr11_sf11, $integerRecord_111_fr11_if11;
    protected $evaluationReport_1221_dm122_ep11_c2_pers2, $textAreaRecord_211_fr21_taf21, $integerRecord_121_fr12_if11;
    protected $evaluationReport_2111_dm211_ep21_c1c2_pers1, $textAreaRecord_221_fr22_taf_21, $integerRecord_211_fr21_if21;
    protected $evaluationReport_2211_dm221_ep21_c1_pers1, $stringRecord_121_fr12_sf11, $integerRecord_221_fr22_if21;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Form')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('IntegerField')->truncate();
        $this->connection->table('TextAreaField')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('EvaluationPlan')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('T_Member')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();
        $this->connection->table('IntegerFieldRecord')->truncate();
        $this->connection->table('TextAreaFieldRecord')->truncate();
        $this->connection->table('MentorEvaluationReport')->truncate();
        
        $this->evaluationReportSummaryUri = $this->managerUri . "/evaluation-report-summary";
        $this->evaluationReportTranscriptUri = $this->managerUri . "/evaluation-report-transcript";
        
        $firm = $this->manager->firm;
        
        $programOne = new RecordOfProgram($firm, '1');
        $programTwo = new RecordOfProgram($firm, '2');
        
        $formOne = new RecordOfForm(1);
        $formTwo = new RecordOfForm(2);
        
        $this->stringField_11 = new RecordOfStringField($formOne, '11');
        $this->stringField_11->position = "2";
        
        $this->integerField_11 = new RecordOfIntegerField($formOne, '11');
        $this->integerField_11->position = '1';
        $this->integerField_21 = new RecordOfIntegerField($formTwo, '21');
        $this->integerField_21->position = '1';
        
        $this->textAreaField_21 = new RecordOfTextAreaField($formTwo, '21');
        $this->textAreaField_21->position = "2";
        
        $feedbackFormOne = new RecordOfFeedbackForm($firm, $formOne);
        $feedbackFormTwo = new RecordOfFeedbackForm($firm, $formTwo);
        
        $this->evaluationPlan_11_prog1 = new RecordOfEvaluationPlan($programOne, $feedbackFormOne, '11');
        $this->evaluationPlan_21_prog2 = new RecordOfEvaluationPlan($programTwo, $feedbackFormTwo, '21');
        
        
        $participant_11_prog1_c1 = new RecordOfParticipant($programOne, '11');
        $participant_12_prog1_c2 = new RecordOfParticipant($programOne, '12');
        $participant_21_prog2_t1 = new RecordOfParticipant($programTwo, '21');
        $participant_22_prog2_t2 = new RecordOfParticipant($programTwo, '22');
        
        $this->clientOne = new RecordOfClient($firm, '1');
        $this->clientTwo = new RecordOfClient($firm, '2');
        
        $this->clientParticipant_11_prog1_c1 = new RecordOfClientParticipant($this->clientOne, $participant_11_prog1_c1);
        $this->clientParticipant_12_prog1_c2 = new RecordOfClientParticipant($this->clientTwo, $participant_12_prog1_c2);
        
        $this->teamOne = new RecordOfTeam($firm, $this->clientOne, '1');
        $this->teamTwo = new RecordOfTeam($firm, $this->clientOne, '2');
        
        $this->member_11_t1_c1 = new RecordOfMember($this->teamOne, $this->clientOne, '11');
        $this->member_12_t1_c2 = new RecordOfMember($this->teamOne, $this->clientTwo, '12');
        $this->member_21_t2_c1 = new RecordOfMember($this->teamTwo, $this->clientOne, '21');
        
        $this->teamParticipant_21_prog2_t1 = new RecordOfTeamProgramParticipation($this->teamOne, $participant_21_prog2_t1);
        $this->teamParticipant_22_prog2_t2 = new RecordOfTeamProgramParticipation($this->teamTwo, $participant_22_prog2_t2);

        $this->personnelOne = new RecordOfPersonnel($firm, '1');
        $this->personnelTwo = new RecordOfPersonnel($firm, '2');
        
        $mentor_11_prog1_pers1 = new RecordOfConsultant($programOne, $this->personnelOne, '11');
        $mentor_12_prog1_pers2 = new RecordOfConsultant($programOne, $this->personnelTwo, '12');
        $mentor_21_prog2_pers1 = new RecordOfConsultant($programTwo, $this->personnelOne, '21');
        
        $dedicatedMentor_111_par11_m11 = new RecordOfDedicatedMentor($participant_11_prog1_c1, $mentor_11_prog1_pers1, '111');
        $dedicatedMentor_122_par12_m12 = new RecordOfDedicatedMentor($participant_12_prog1_c2, $mentor_12_prog1_pers2, '122');
        $dedicatedMentor_211_par21_m21 = new RecordOfDedicatedMentor($participant_21_prog2_t1, $mentor_21_prog2_pers1, '211');
        $dedicatedMentor_221_par22_m21 = new RecordOfDedicatedMentor($participant_22_prog2_t2, $mentor_21_prog2_pers1, '221');
        
        $formRecord_11_form1 = new RecordOfFormRecord($formOne, '11');
        $formRecord_12_form1 = new RecordOfFormRecord($formOne, '12');
        $formRecord_21_form2 = new RecordOfFormRecord($formTwo, '21');
        $formRecord_22_form2 = new RecordOfFormRecord($formTwo, '22');
        
        $this->stringRecord_111_fr11_sf11 = new RecordOfStringFieldRecord($formRecord_11_form1, $this->stringField_11, '111');
        $this->stringRecord_121_fr12_sf11 = new RecordOfStringFieldRecord($formRecord_12_form1, $this->stringField_11, '121');
        
        $this->integerRecord_111_fr11_if11 = new RecordOfIntegerFieldRecord($formRecord_11_form1, $this->integerField_11, '111');
        $this->integerRecord_121_fr12_if11 = new RecordOfIntegerFieldRecord($formRecord_12_form1, $this->integerField_11, '121');
        $this->integerRecord_211_fr21_if21 = new RecordOfIntegerFieldRecord($formRecord_21_form2, $this->integerField_21, '211');
        $this->integerRecord_221_fr22_if21 = new RecordOfIntegerFieldRecord($formRecord_22_form2, $this->integerField_21, '221');
        
        $this->textAreaRecord_211_fr21_taf21 = new RecordOfTextAreaFieldRecord($formRecord_21_form2, $this->textAreaField_21, '211');
        $this->textAreaRecord_221_fr22_taf_21 = new RecordOfTextAreaFieldRecord($formRecord_22_form2, $this->textAreaField_21, '221');
        
        $this->evaluationReport_1111_dm111_ep11_c1_pers1 = new RecordOfMentorEvaluationReport($dedicatedMentor_111_par11_m11, $this->evaluationPlan_11_prog1, $formRecord_11_form1);
        $this->evaluationReport_1221_dm122_ep11_c2_pers2 = new RecordOfMentorEvaluationReport($dedicatedMentor_122_par12_m12, $this->evaluationPlan_11_prog1, $formRecord_12_form1);
        $this->evaluationReport_2111_dm211_ep21_c1c2_pers1 = new RecordOfMentorEvaluationReport($dedicatedMentor_211_par21_m21, $this->evaluationPlan_21_prog2, $formRecord_21_form2);
        $this->evaluationReport_2211_dm221_ep21_c1_pers1 = new RecordOfMentorEvaluationReport($dedicatedMentor_221_par22_m21, $this->evaluationPlan_21_prog2, $formRecord_22_form2);
    }
    protected function tearDown(): void
    {
//        parent::tearDown();
//        $this->connection->table('Form')->truncate();
//        $this->connection->table('StringField')->truncate();
//        $this->connection->table('IntegerField')->truncate();
//        $this->connection->table('TextAreaField')->truncate();
//        $this->connection->table('FeedbackForm')->truncate();
//        $this->connection->table('Program')->truncate();
//        $this->connection->table('EvaluationPlan')->truncate();
//        $this->connection->table('Participant')->truncate();
//        $this->connection->table('Client')->truncate();
//        $this->connection->table('ClientParticipant')->truncate();
//        $this->connection->table('Team')->truncate();
//        $this->connection->table('TeamParticipant')->truncate();
//        $this->connection->table('T_Member')->truncate();
//        $this->connection->table('Personnel')->truncate();
//        $this->connection->table('Consultant')->truncate();
//        $this->connection->table('DedicatedMentor')->truncate();
//        $this->connection->table('FormRecord')->truncate();
//        $this->connection->table('StringFieldRecord')->truncate();
//        $this->connection->table('IntegerFieldRecord')->truncate();
//        $this->connection->table('TextAreaFieldRecord')->truncate();
//        $this->connection->table('MentorEvaluationReport')->truncate();
    }
    
    protected function summary()
    {
        $this->evaluationReport_1111_dm111_ep11_c1_pers1->evaluationPlan->program->insert($this->connection);
        $this->evaluationReport_2111_dm211_ep21_c1c2_pers1->evaluationPlan->program->insert($this->connection);
        
        $this->integerField_11->insert($this->connection);
        $this->integerField_21->insert($this->connection);
        $this->stringField_11->insert($this->connection);
        $this->textAreaField_21->insert($this->connection);
        
        $this->evaluationReport_1111_dm111_ep11_c1_pers1->evaluationPlan->feedbackForm->insert($this->connection);
        $this->evaluationReport_2111_dm211_ep21_c1c2_pers1->evaluationPlan->feedbackForm->insert($this->connection);
        
        $this->evaluationReport_1111_dm111_ep11_c1_pers1->evaluationPlan->insert($this->connection);
        $this->evaluationReport_2111_dm211_ep21_c1c2_pers1->evaluationPlan->insert($this->connection);
        
        $this->evaluationReport_1111_dm111_ep11_c1_pers1->dedicatedMentor->consultant->personnel->insert($this->connection);
        $this->evaluationReport_1221_dm122_ep11_c2_pers2->dedicatedMentor->consultant->personnel->insert($this->connection);
        
        $this->evaluationReport_1111_dm111_ep11_c1_pers1->dedicatedMentor->consultant->insert($this->connection);
        $this->evaluationReport_1221_dm122_ep11_c2_pers2->dedicatedMentor->consultant->insert($this->connection);
        $this->evaluationReport_2111_dm211_ep21_c1c2_pers1->dedicatedMentor->consultant->insert($this->connection);
        
        $this->clientParticipant_11_prog1_c1->client->insert($this->connection);
        $this->clientParticipant_12_prog1_c2->client->insert($this->connection);
        $this->teamParticipant_21_prog2_t1->team->insert($this->connection);
        $this->teamParticipant_22_prog2_t2->team->insert($this->connection);
        
        $this->member_11_t1_c1->insert($this->connection);
        $this->member_12_t1_c2->insert($this->connection);
        $this->member_21_t2_c1->insert($this->connection);
        
        $this->clientParticipant_11_prog1_c1->insert($this->connection);
        $this->clientParticipant_12_prog1_c2->insert($this->connection);
        $this->teamParticipant_21_prog2_t1->insert($this->connection);
        $this->teamParticipant_22_prog2_t2->insert($this->connection);
        
        $this->evaluationReport_1111_dm111_ep11_c1_pers1->dedicatedMentor->insert($this->connection);
        $this->evaluationReport_1221_dm122_ep11_c2_pers2->dedicatedMentor->insert($this->connection);
        $this->evaluationReport_2111_dm211_ep21_c1c2_pers1->dedicatedMentor->insert($this->connection);
        $this->evaluationReport_2211_dm221_ep21_c1_pers1->dedicatedMentor->insert($this->connection);
        
        $this->evaluationReport_1111_dm111_ep11_c1_pers1->insert($this->connection);
        $this->evaluationReport_1221_dm122_ep11_c2_pers2->insert($this->connection);
        $this->evaluationReport_2111_dm211_ep21_c1c2_pers1->insert($this->connection);
        $this->evaluationReport_2211_dm221_ep21_c1_pers1->insert($this->connection);
        
        $this->integerRecord_111_fr11_if11->insert($this->connection);
        $this->integerRecord_121_fr12_if11->insert($this->connection);
        $this->integerRecord_211_fr21_if21->insert($this->connection);
        $this->integerRecord_221_fr22_if21->insert($this->connection);
        $this->stringRecord_111_fr11_sf11->insert($this->connection);
        $this->stringRecord_121_fr12_sf11->insert($this->connection);
        $this->textAreaRecord_211_fr21_taf21->insert($this->connection);
        $this->textAreaRecord_221_fr22_taf_21->insert($this->connection);
        
        $this->get($this->evaluationReportSummaryUri, $this->manager->token);
    }
    public function test_summary_200()
    {
        $this->summary();
        $this->seeStatusCode(200);
        
        $evaluationPlanOneTable = [
            'id' => $this->evaluationPlan_11_prog1->id,
            'name' => $this->evaluationPlan_11_prog1->name,
            'summaryTable' => [
                'header' => [
                    1 => ['colNumber' => 1, 'label' => 'client'],
                    2 => ['colNumber' => 2, 'label' => 'mentor'],
                    3 => ['colNumber' => 3, 'label' => $this->integerField_11->name],
                    4 => ['colNumber' => 4, 'label' => $this->stringField_11->name],
                ],
                'entries' => [
                    [
                        1 => ['colNumber' => 1, 'value' => $this->clientOne->getFullName()],
                        2 => ['colNumber' => 2, 'value' => $this->evaluationReport_1111_dm111_ep11_c1_pers1->dedicatedMentor->consultant->personnel->getFullName()],
                        3 => ['colNumber' => 3, 'value' => $this->integerRecord_111_fr11_if11->value],
                        4 => ['colNumber' => 4, 'value' => $this->stringRecord_111_fr11_sf11->value],
                    ],
                    [
                        1 => ['colNumber' => 1, 'value' => $this->clientTwo->getFullName()],
                        2 => ['colNumber' => 2, 'value' => $this->evaluationReport_1221_dm122_ep11_c2_pers2->dedicatedMentor->consultant->personnel->getFullName()],
                        3 => ['colNumber' => 3, 'value' => $this->integerRecord_121_fr12_if11->value],
                        4 => ['colNumber' => 4, 'value' => $this->stringRecord_121_fr12_sf11->value],
                    ],
                ],
            ],
        ];
        
        $evaluationPlanTwoTable = [
            'id' => $this->evaluationPlan_21_prog2->id,
            'name' => $this->evaluationPlan_21_prog2->name,
            'summaryTable' => [
                'header' => [
                    1 => ['colNumber' => 1, 'label' => 'client'],
                    2 => ['colNumber' => 2, 'label' => 'mentor'],
                    3 => ['colNumber' => 3, 'label' => $this->integerField_21->name],
                    4 => ['colNumber' => 4, 'label' => $this->textAreaField_21->name],
                ],
                'entries' => [
                    [
                        1 => ['colNumber' => 1, 'value' => "{$this->clientOne->getFullName()} (of team: {$this->teamOne->name})"],
                        2 => ['colNumber' => 2, 'value' => $this->evaluationReport_2111_dm211_ep21_c1c2_pers1->dedicatedMentor->consultant->personnel->getFullName()],
                        3 => ['colNumber' => 3, 'value' => $this->integerRecord_211_fr21_if21->value],
                        4 => ['colNumber' => 4, 'value' => $this->textAreaRecord_211_fr21_taf21->value],
                    ],
                    [
                        1 => ['colNumber' => 1, 'value' => "{$this->clientTwo->getFullName()} (of team: {$this->teamOne->name})"],
                        2 => ['colNumber' => 2, 'value' => $this->evaluationReport_2111_dm211_ep21_c1c2_pers1->dedicatedMentor->consultant->personnel->getFullName()],
                        3 => ['colNumber' => 3, 'value' => $this->integerRecord_211_fr21_if21->value],
                        4 => ['colNumber' => 4, 'value' => $this->textAreaRecord_211_fr21_taf21->value],
                    ],
                    [
                        1 => ['colNumber' => 1, 'value' => "{$this->clientOne->getFullName()} (of team: {$this->teamTwo->name})"],
                        2 => ['colNumber' => 2, 'value' => $this->evaluationReport_2211_dm221_ep21_c1_pers1->dedicatedMentor->consultant->personnel->getFullName()],
                        3 => ['colNumber' => 3, 'value' => $this->integerRecord_221_fr22_if21->value],
                        4 => ['colNumber' => 4, 'value' => $this->textAreaRecord_221_fr22_taf_21->value],
                    ],
                ],
            ],
        ];
        
        $response = [
            "data" => [
                $evaluationPlanOneTable,
                $evaluationPlanTwoTable,
            ],
            "meta" => [
                "code" => 200,
                "type" => "OK",
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_summary_clientIdListFilterApplied_200()
    {
        $this->evaluationReportSummaryUri .= "?clientIdList[]={$this->clientOne->id}";
        $this->summary();
        $this->seeStatusCode(200);
        
        $evaluationPlanOneTable = [
            'id' => $this->evaluationPlan_11_prog1->id,
            'name' => $this->evaluationPlan_11_prog1->name,
            'summaryTable' => [
                'header' => [
                    1 => ['colNumber' => 1, 'label' => 'client'],
                    2 => ['colNumber' => 2, 'label' => 'mentor'],
                    3 => ['colNumber' => 3, 'label' => $this->integerField_11->name],
                    4 => ['colNumber' => 4, 'label' => $this->stringField_11->name],
                ],
                'entries' => [
                    [
                        1 => ['colNumber' => 1, 'value' => $this->clientOne->getFullName()],
                        2 => ['colNumber' => 2, 'value' => $this->evaluationReport_1111_dm111_ep11_c1_pers1->dedicatedMentor->consultant->personnel->getFullName()],
                        3 => ['colNumber' => 3, 'value' => $this->integerRecord_111_fr11_if11->value],
                        4 => ['colNumber' => 4, 'value' => $this->stringRecord_111_fr11_sf11->value],
                    ],
                ],
            ],
        ];
        
        $evaluationPlanTwoTable = [
            'id' => $this->evaluationPlan_21_prog2->id,
            'name' => $this->evaluationPlan_21_prog2->name,
            'summaryTable' => [
                'header' => [
                    1 => ['colNumber' => 1, 'label' => 'client'],
                    2 => ['colNumber' => 2, 'label' => 'mentor'],
                    3 => ['colNumber' => 3, 'label' => $this->integerField_21->name],
                    4 => ['colNumber' => 4, 'label' => $this->textAreaField_21->name],
                ],
                'entries' => [
                    [
                        1 => ['colNumber' => 1, 'value' => "{$this->clientOne->getFullName()} (of team: {$this->teamOne->name})"],
                        2 => ['colNumber' => 2, 'value' => $this->evaluationReport_2111_dm211_ep21_c1c2_pers1->dedicatedMentor->consultant->personnel->getFullName()],
                        3 => ['colNumber' => 3, 'value' => $this->integerRecord_211_fr21_if21->value],
                        4 => ['colNumber' => 4, 'value' => $this->textAreaRecord_211_fr21_taf21->value],
                    ],
// BUG, this record should not exist
                    [
                        1 => ['colNumber' => 1, 'value' => "{$this->clientTwo->getFullName()} (of team: {$this->teamOne->name})"],
                        2 => ['colNumber' => 2, 'value' => $this->evaluationReport_2111_dm211_ep21_c1c2_pers1->dedicatedMentor->consultant->personnel->getFullName()],
                        3 => ['colNumber' => 3, 'value' => $this->integerRecord_211_fr21_if21->value],
                        4 => ['colNumber' => 4, 'value' => $this->textAreaRecord_211_fr21_taf21->value],
                    ],
                    [
                        1 => ['colNumber' => 1, 'value' => "{$this->clientOne->getFullName()} (of team: {$this->teamTwo->name})"],
                        2 => ['colNumber' => 2, 'value' => $this->evaluationReport_2211_dm221_ep21_c1_pers1->dedicatedMentor->consultant->personnel->getFullName()],
                        3 => ['colNumber' => 3, 'value' => $this->integerRecord_221_fr22_if21->value],
                        4 => ['colNumber' => 4, 'value' => $this->textAreaRecord_221_fr22_taf_21->value],
                    ],
                ],
            ],
        ];
        
        $response = [
            "data" => [
                $evaluationPlanOneTable,
                $evaluationPlanTwoTable,
            ],
            "meta" => [
                "code" => 200,
                "type" => "OK",
            ],
        ];
        $this->seeJsonContains($response);
        $this->markTestSkipped("bug, other member of team still shown");
    }
    public function test_summary_evaluationPlanIdListFilterApplied_200()
    {
        $this->evaluationReportSummaryUri .= "?evaluationPlanIdList[]={$this->evaluationPlan_11_prog1->id}";
        $this->summary();
        $this->seeStatusCode(200);
        
        $evaluationPlanOneTable = [
            'id' => $this->evaluationPlan_11_prog1->id,
            'name' => $this->evaluationPlan_11_prog1->name,
            'summaryTable' => [
                'header' => [
                    1 => ['colNumber' => 1, 'label' => 'client'],
                    2 => ['colNumber' => 2, 'label' => 'mentor'],
                    3 => ['colNumber' => 3, 'label' => $this->integerField_11->name],
                    4 => ['colNumber' => 4, 'label' => $this->stringField_11->name],
                ],
                'entries' => [
                    [
                        1 => ['colNumber' => 1, 'value' => $this->clientOne->getFullName()],
                        2 => ['colNumber' => 2, 'value' => $this->evaluationReport_1111_dm111_ep11_c1_pers1->dedicatedMentor->consultant->personnel->getFullName()],
                        3 => ['colNumber' => 3, 'value' => $this->integerRecord_111_fr11_if11->value],
                        4 => ['colNumber' => 4, 'value' => $this->stringRecord_111_fr11_sf11->value],
                    ],
                    [
                        1 => ['colNumber' => 1, 'value' => $this->clientTwo->getFullName()],
                        2 => ['colNumber' => 2, 'value' => $this->evaluationReport_1221_dm122_ep11_c2_pers2->dedicatedMentor->consultant->personnel->getFullName()],
                        3 => ['colNumber' => 3, 'value' => $this->integerRecord_121_fr12_if11->value],
                        4 => ['colNumber' => 4, 'value' => $this->stringRecord_121_fr12_sf11->value],
                    ],
                ],
            ],
        ];
        
        $response = [
            "data" => [
                $evaluationPlanOneTable,
            ],
            "meta" => [
                "code" => 200,
                "type" => "OK",
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_summary_personnelIdListApplied_200()
    {
        $this->evaluationReportSummaryUri .= "?personnelIdList[]={$this->personnelOne->id}";
        $this->summary();
        $this->seeStatusCode(200);
        
        $evaluationPlanOneTable = [
            'id' => $this->evaluationPlan_11_prog1->id,
            'name' => $this->evaluationPlan_11_prog1->name,
            'summaryTable' => [
                'header' => [
                    1 => ['colNumber' => 1, 'label' => 'client'],
                    2 => ['colNumber' => 2, 'label' => 'mentor'],
                    3 => ['colNumber' => 3, 'label' => $this->integerField_11->name],
                    4 => ['colNumber' => 4, 'label' => $this->stringField_11->name],
                ],
                'entries' => [
                    [
                        1 => ['colNumber' => 1, 'value' => $this->clientOne->getFullName()],
                        2 => ['colNumber' => 2, 'value' => $this->evaluationReport_1111_dm111_ep11_c1_pers1->dedicatedMentor->consultant->personnel->getFullName()],
                        3 => ['colNumber' => 3, 'value' => $this->integerRecord_111_fr11_if11->value],
                        4 => ['colNumber' => 4, 'value' => $this->stringRecord_111_fr11_sf11->value],
                    ],
                ],
            ],
        ];
        
        $evaluationPlanTwoTable = [
            'id' => $this->evaluationPlan_21_prog2->id,
            'name' => $this->evaluationPlan_21_prog2->name,
            'summaryTable' => [
                'header' => [
                    1 => ['colNumber' => 1, 'label' => 'client'],
                    2 => ['colNumber' => 2, 'label' => 'mentor'],
                    3 => ['colNumber' => 3, 'label' => $this->integerField_21->name],
                    4 => ['colNumber' => 4, 'label' => $this->textAreaField_21->name],
                ],
                'entries' => [
                    [
                        1 => ['colNumber' => 1, 'value' => "{$this->clientOne->getFullName()} (of team: {$this->teamOne->name})"],
                        2 => ['colNumber' => 2, 'value' => $this->evaluationReport_2111_dm211_ep21_c1c2_pers1->dedicatedMentor->consultant->personnel->getFullName()],
                        3 => ['colNumber' => 3, 'value' => $this->integerRecord_211_fr21_if21->value],
                        4 => ['colNumber' => 4, 'value' => $this->textAreaRecord_211_fr21_taf21->value],
                    ],
                    [
                        1 => ['colNumber' => 1, 'value' => "{$this->clientTwo->getFullName()} (of team: {$this->teamOne->name})"],
                        2 => ['colNumber' => 2, 'value' => $this->evaluationReport_2111_dm211_ep21_c1c2_pers1->dedicatedMentor->consultant->personnel->getFullName()],
                        3 => ['colNumber' => 3, 'value' => $this->integerRecord_211_fr21_if21->value],
                        4 => ['colNumber' => 4, 'value' => $this->textAreaRecord_211_fr21_taf21->value],
                    ],
                    [
                        1 => ['colNumber' => 1, 'value' => "{$this->clientOne->getFullName()} (of team: {$this->teamTwo->name})"],
                        2 => ['colNumber' => 2, 'value' => $this->evaluationReport_2211_dm221_ep21_c1_pers1->dedicatedMentor->consultant->personnel->getFullName()],
                        3 => ['colNumber' => 3, 'value' => $this->integerRecord_221_fr22_if21->value],
                        4 => ['colNumber' => 4, 'value' => $this->textAreaRecord_221_fr22_taf_21->value],
                    ],
                ],
            ],
        ];
        
        $response = [
            "data" => [
                $evaluationPlanOneTable,
                $evaluationPlanTwoTable,
            ],
            "meta" => [
                "code" => 200,
                "type" => "OK",
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_summary_containCancelledReport_excludeFromResult()
    {
        $this->evaluationReport_1111_dm111_ep11_c1_pers1->cancelled = true;
        $this->summary();
        $this->seeStatusCode(200);
        
        $evaluationPlanOneTable = [
            'id' => $this->evaluationPlan_11_prog1->id,
            'name' => $this->evaluationPlan_11_prog1->name,
            'summaryTable' => [
                'header' => [
                    1 => ['colNumber' => 1, 'label' => 'client'],
                    2 => ['colNumber' => 2, 'label' => 'mentor'],
                    3 => ['colNumber' => 3, 'label' => $this->integerField_11->name],
                    4 => ['colNumber' => 4, 'label' => $this->stringField_11->name],
                ],
                'entries' => [
                    [
                        1 => ['colNumber' => 1, 'value' => $this->clientTwo->getFullName()],
                        2 => ['colNumber' => 2, 'value' => $this->evaluationReport_1221_dm122_ep11_c2_pers2->dedicatedMentor->consultant->personnel->getFullName()],
                        3 => ['colNumber' => 3, 'value' => $this->integerRecord_121_fr12_if11->value],
                        4 => ['colNumber' => 4, 'value' => $this->stringRecord_121_fr12_sf11->value],
                    ],
                ],
            ],
        ];
        
        $evaluationPlanTwoTable = [
            'id' => $this->evaluationPlan_21_prog2->id,
            'name' => $this->evaluationPlan_21_prog2->name,
            'summaryTable' => [
                'header' => [
                    1 => ['colNumber' => 1, 'label' => 'client'],
                    2 => ['colNumber' => 2, 'label' => 'mentor'],
                    3 => ['colNumber' => 3, 'label' => $this->integerField_21->name],
                    4 => ['colNumber' => 4, 'label' => $this->textAreaField_21->name],
                ],
                'entries' => [
                    [
                        1 => ['colNumber' => 1, 'value' => "{$this->clientOne->getFullName()} (of team: {$this->teamOne->name})"],
                        2 => ['colNumber' => 2, 'value' => $this->evaluationReport_2111_dm211_ep21_c1c2_pers1->dedicatedMentor->consultant->personnel->getFullName()],
                        3 => ['colNumber' => 3, 'value' => $this->integerRecord_211_fr21_if21->value],
                        4 => ['colNumber' => 4, 'value' => $this->textAreaRecord_211_fr21_taf21->value],
                    ],
                    [
                        1 => ['colNumber' => 1, 'value' => "{$this->clientTwo->getFullName()} (of team: {$this->teamOne->name})"],
                        2 => ['colNumber' => 2, 'value' => $this->evaluationReport_2111_dm211_ep21_c1c2_pers1->dedicatedMentor->consultant->personnel->getFullName()],
                        3 => ['colNumber' => 3, 'value' => $this->integerRecord_211_fr21_if21->value],
                        4 => ['colNumber' => 4, 'value' => $this->textAreaRecord_211_fr21_taf21->value],
                    ],
                    [
                        1 => ['colNumber' => 1, 'value' => "{$this->clientOne->getFullName()} (of team: {$this->teamTwo->name})"],
                        2 => ['colNumber' => 2, 'value' => $this->evaluationReport_2211_dm221_ep21_c1_pers1->dedicatedMentor->consultant->personnel->getFullName()],
                        3 => ['colNumber' => 3, 'value' => $this->integerRecord_221_fr22_if21->value],
                        4 => ['colNumber' => 4, 'value' => $this->textAreaRecord_221_fr22_taf_21->value],
                    ],
                ],
            ],
        ];
        
        $response = [
            "data" => [
                $evaluationPlanOneTable,
                $evaluationPlanTwoTable,
            ],
            "meta" => [
                "code" => 200,
                "type" => "OK",
            ],
        ];
        $this->seeJsonContains($response);
    }
    
    protected function transcript()
    {
        $this->evaluationReport_1111_dm111_ep11_c1_pers1->evaluationPlan->program->insert($this->connection);
        $this->evaluationReport_2111_dm211_ep21_c1c2_pers1->evaluationPlan->program->insert($this->connection);
        
        $this->integerField_11->insert($this->connection);
        $this->integerField_21->insert($this->connection);
        $this->stringField_11->insert($this->connection);
        $this->textAreaField_21->insert($this->connection);
        
        $this->evaluationReport_1111_dm111_ep11_c1_pers1->evaluationPlan->feedbackForm->insert($this->connection);
        $this->evaluationReport_2111_dm211_ep21_c1c2_pers1->evaluationPlan->feedbackForm->insert($this->connection);
        
        $this->evaluationReport_1111_dm111_ep11_c1_pers1->evaluationPlan->insert($this->connection);
        $this->evaluationReport_2111_dm211_ep21_c1c2_pers1->evaluationPlan->insert($this->connection);
        
        $this->evaluationReport_1111_dm111_ep11_c1_pers1->dedicatedMentor->consultant->personnel->insert($this->connection);
        $this->evaluationReport_1221_dm122_ep11_c2_pers2->dedicatedMentor->consultant->personnel->insert($this->connection);
        
        $this->evaluationReport_1111_dm111_ep11_c1_pers1->dedicatedMentor->consultant->insert($this->connection);
        $this->evaluationReport_1221_dm122_ep11_c2_pers2->dedicatedMentor->consultant->insert($this->connection);
        $this->evaluationReport_2111_dm211_ep21_c1c2_pers1->dedicatedMentor->consultant->insert($this->connection);
        
        $this->clientParticipant_11_prog1_c1->client->insert($this->connection);
        $this->clientParticipant_12_prog1_c2->client->insert($this->connection);
        $this->teamParticipant_21_prog2_t1->team->insert($this->connection);
        $this->teamParticipant_22_prog2_t2->team->insert($this->connection);
        
        $this->member_11_t1_c1->insert($this->connection);
        $this->member_12_t1_c2->insert($this->connection);
        $this->member_21_t2_c1->insert($this->connection);
        
        $this->clientParticipant_11_prog1_c1->insert($this->connection);
        $this->clientParticipant_12_prog1_c2->insert($this->connection);
        $this->teamParticipant_21_prog2_t1->insert($this->connection);
        $this->teamParticipant_22_prog2_t2->insert($this->connection);
        
        $this->evaluationReport_1111_dm111_ep11_c1_pers1->dedicatedMentor->insert($this->connection);
        $this->evaluationReport_1221_dm122_ep11_c2_pers2->dedicatedMentor->insert($this->connection);
        $this->evaluationReport_2111_dm211_ep21_c1c2_pers1->dedicatedMentor->insert($this->connection);
        $this->evaluationReport_2211_dm221_ep21_c1_pers1->dedicatedMentor->insert($this->connection);
        
        $this->evaluationReport_1111_dm111_ep11_c1_pers1->insert($this->connection);
        $this->evaluationReport_1221_dm122_ep11_c2_pers2->insert($this->connection);
        $this->evaluationReport_2111_dm211_ep21_c1c2_pers1->insert($this->connection);
        $this->evaluationReport_2211_dm221_ep21_c1_pers1->insert($this->connection);
        
        $this->integerRecord_111_fr11_if11->insert($this->connection);
        $this->integerRecord_121_fr12_if11->insert($this->connection);
        $this->integerRecord_211_fr21_if21->insert($this->connection);
        $this->integerRecord_221_fr22_if21->insert($this->connection);
        $this->stringRecord_111_fr11_sf11->insert($this->connection);
        $this->stringRecord_121_fr12_sf11->insert($this->connection);
        $this->textAreaRecord_211_fr21_taf21->insert($this->connection);
        $this->textAreaRecord_221_fr22_taf_21->insert($this->connection);
        
        $this->get($this->evaluationReportTranscriptUri, $this->manager->token);
var_dump($this->manager->token);
var_dump($this->evaluationReportTranscriptUri);
    }
    public function test_transcript_200()
    {
        $this->transcript();
        $this->seeStatusCode(200);
        
        $clientOneTable = [
            'id' => $this->clientOne->id,
            'name' => $this->clientOne->getFullName(),
            'evaluationPlans' => [
                [
                    'id' => $this->evaluationPlan_11_prog1->id,
                    'name' => $this->evaluationPlan_11_prog1->name,
                    'summaryTable' => [
                        'header' => [
                            1 => ['colNumber' => 1, 'label' => 'mentor'],
                            2 => ['colNumber' => 2, 'label' => $this->integerField_11->name],
                            3 => ['colNumber' => 3, 'label' => $this->stringField_11->name],
                        ],
                        'entries' => [
                            [
                                1 => ['colNumber' => 1, 'value' => $this->evaluationReport_1111_dm111_ep11_c1_pers1->dedicatedMentor->consultant->personnel->getFullName()],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_111_fr11_if11->value],
                                3 => ['colNumber' => 3, 'value' => $this->stringRecord_111_fr11_sf11->value],
                            ],
                        ],
                    ],
                ],
                [
                    'id' => $this->evaluationPlan_21_prog2->id,
                    'name' => $this->evaluationPlan_21_prog2->name,
                    'summaryTable' => [
                        'header' => [
                            1 => ['colNumber' => 1, 'label' => 'mentor'],
                            2 => ['colNumber' => 2, 'label' => $this->integerField_21->name],
                            3 => ['colNumber' => 3, 'label' => $this->textAreaField_21->name],
                        ],
                        'entries' => [
                            [
                                1 => ['colNumber' => 1, 'value' => "{$this->evaluationReport_2111_dm211_ep21_c1c2_pers1->dedicatedMentor->consultant->personnel->getFullName()} (of team: {$this->teamOne->name})"],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_211_fr21_if21->value],
                                3 => ['colNumber' => 3, 'value' => $this->textAreaRecord_211_fr21_taf21->value],
                            ],
                            [
                                1 => ['colNumber' => 1, 'value' => "{$this->evaluationReport_2211_dm221_ep21_c1_pers1->dedicatedMentor->consultant->personnel->getFullName()} (of team: {$this->teamTwo->name})"],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_221_fr22_if21->value],
                                3 => ['colNumber' => 3, 'value' => $this->textAreaRecord_221_fr22_taf_21->value],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        
        $clientTwoTable = [
            'id' => $this->clientTwo->id,
            'name' => $this->clientTwo->getFullName(),
            'evaluationPlans' => [
                [
                    'id' => $this->evaluationPlan_11_prog1->id,
                    'name' => $this->evaluationPlan_11_prog1->name,
                    'summaryTable' => [
                        'header' => [
                            1 => ['colNumber' => 1, 'label' => 'mentor'],
                            2 => ['colNumber' => 2, 'label' => $this->integerField_11->name],
                            3 => ['colNumber' => 3, 'label' => $this->stringField_11->name],
                        ],
                        'entries' => [
                            [
                                1 => ['colNumber' => 1, 'value' => $this->evaluationReport_1221_dm122_ep11_c2_pers2->dedicatedMentor->consultant->personnel->getFullName()],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_121_fr12_if11->value],
                                3 => ['colNumber' => 3, 'value' => $this->stringRecord_121_fr12_sf11->value],
                            ],
                        ],
                    ],
                ],
                [
                    'id' => $this->evaluationPlan_21_prog2->id,
                    'name' => $this->evaluationPlan_21_prog2->name,
                    'summaryTable' => [
                        'header' => [
                            1 => ['colNumber' => 1, 'label' => 'mentor'],
                            2 => ['colNumber' => 2, 'label' => $this->integerField_21->name],
                            3 => ['colNumber' => 3, 'label' => $this->textAreaField_21->name],
                        ],
                        'entries' => [
                            [
                                1 => ['colNumber' => 1, 'value' => "{$this->evaluationReport_2111_dm211_ep21_c1c2_pers1->dedicatedMentor->consultant->personnel->getFullName()} (of team: {$this->teamOne->name})"],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_211_fr21_if21->value],
                                3 => ['colNumber' => 3, 'value' => $this->textAreaRecord_211_fr21_taf21->value],
                            ],
                        ],
                    ],
                ],
            ],
        ];
                                
        $response = [
            "data" => [
                $clientOneTable,
                $clientTwoTable,
            ],
            "meta" => [
                "code" => 200,
                "type" => "OK",
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_transcript_clientIdListFilterApplied_200()
    {
        $this->evaluationReportTranscriptUri .= "?clientIdList[]={$this->clientOne->id}";
        $this->transcript();
        $this->seeStatusCode(200);
        
        $clientOneTable = [
            'id' => $this->clientOne->id,
            'name' => $this->clientOne->getFullName(),
            'evaluationPlans' => [
                [
                    'id' => $this->evaluationPlan_11_prog1->id,
                    'name' => $this->evaluationPlan_11_prog1->name,
                    'summaryTable' => [
                        'header' => [
                            1 => ['colNumber' => 1, 'label' => 'mentor'],
                            2 => ['colNumber' => 2, 'label' => $this->integerField_11->name],
                            3 => ['colNumber' => 3, 'label' => $this->stringField_11->name],
                        ],
                        'entries' => [
                            [
                                1 => ['colNumber' => 1, 'value' => $this->evaluationReport_1111_dm111_ep11_c1_pers1->dedicatedMentor->consultant->personnel->getFullName()],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_111_fr11_if11->value],
                                3 => ['colNumber' => 3, 'value' => $this->stringRecord_111_fr11_sf11->value],
                            ],
                        ],
                    ],
                ],
                [
                    'id' => $this->evaluationPlan_21_prog2->id,
                    'name' => $this->evaluationPlan_21_prog2->name,
                    'summaryTable' => [
                        'header' => [
                            1 => ['colNumber' => 1, 'label' => 'mentor'],
                            2 => ['colNumber' => 2, 'label' => $this->integerField_21->name],
                            3 => ['colNumber' => 3, 'label' => $this->textAreaField_21->name],
                        ],
                        'entries' => [
                            [
                                1 => ['colNumber' => 1, 'value' => "{$this->evaluationReport_2111_dm211_ep21_c1c2_pers1->dedicatedMentor->consultant->personnel->getFullName()} (of team: {$this->teamOne->name})"],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_211_fr21_if21->value],
                                3 => ['colNumber' => 3, 'value' => $this->textAreaRecord_211_fr21_taf21->value],
                            ],
                            [
                                1 => ['colNumber' => 1, 'value' => "{$this->evaluationReport_2211_dm221_ep21_c1_pers1->dedicatedMentor->consultant->personnel->getFullName()} (of team: {$this->teamTwo->name})"],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_221_fr22_if21->value],
                                3 => ['colNumber' => 3, 'value' => $this->textAreaRecord_221_fr22_taf_21->value],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        
        $response = [
            "data" => [
                $clientOneTable,
            ],
            "meta" => [
                "code" => 200,
                "type" => "OK",
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_transcript_evaluationPlanIdListFilterApplied_200()
    {
        $this->evaluationReportTranscriptUri .= "?evaluationPlanIdList[]={$this->evaluationPlan_11_prog1->id}";
        $this->transcript();
        $this->seeStatusCode(200);
        
        $clientOneTable = [
            'id' => $this->clientOne->id,
            'name' => $this->clientOne->getFullName(),
            'evaluationPlans' => [
                [
                    'id' => $this->evaluationPlan_11_prog1->id,
                    'name' => $this->evaluationPlan_11_prog1->name,
                    'summaryTable' => [
                        'header' => [
                            1 => ['colNumber' => 1, 'label' => 'mentor'],
                            2 => ['colNumber' => 2, 'label' => $this->integerField_11->name],
                            3 => ['colNumber' => 3, 'label' => $this->stringField_11->name],
                        ],
                        'entries' => [
                            [
                                1 => ['colNumber' => 1, 'value' => $this->evaluationReport_1111_dm111_ep11_c1_pers1->dedicatedMentor->consultant->personnel->getFullName()],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_111_fr11_if11->value],
                                3 => ['colNumber' => 3, 'value' => $this->stringRecord_111_fr11_sf11->value],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        
        $clientTwoTable = [
            'id' => $this->clientTwo->id,
            'name' => $this->clientTwo->getFullName(),
            'evaluationPlans' => [
                [
                    'id' => $this->evaluationPlan_11_prog1->id,
                    'name' => $this->evaluationPlan_11_prog1->name,
                    'summaryTable' => [
                        'header' => [
                            1 => ['colNumber' => 1, 'label' => 'mentor'],
                            2 => ['colNumber' => 2, 'label' => $this->integerField_11->name],
                            3 => ['colNumber' => 3, 'label' => $this->stringField_11->name],
                        ],
                        'entries' => [
                            [
                                1 => ['colNumber' => 1, 'value' => $this->evaluationReport_1221_dm122_ep11_c2_pers2->dedicatedMentor->consultant->personnel->getFullName()],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_121_fr12_if11->value],
                                3 => ['colNumber' => 3, 'value' => $this->stringRecord_121_fr12_sf11->value],
                            ],
                        ],
                    ],
                ],
            ],
        ];
                                
        $response = [
            "data" => [
                $clientOneTable,
                $clientTwoTable,
            ],
            "meta" => [
                "code" => 200,
                "type" => "OK",
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_transcript_personnelIdListFilterApplied_200()
    {
        $this->evaluationReportTranscriptUri .= "?personnelIdList[]={$this->personnelOne->id}";
        $this->transcript();
        $this->seeStatusCode(200);
        
        $clientOneTable = [
            'id' => $this->clientOne->id,
            'name' => $this->clientOne->getFullName(),
            'evaluationPlans' => [
                [
                    'id' => $this->evaluationPlan_11_prog1->id,
                    'name' => $this->evaluationPlan_11_prog1->name,
                    'summaryTable' => [
                        'header' => [
                            1 => ['colNumber' => 1, 'label' => 'mentor'],
                            2 => ['colNumber' => 2, 'label' => $this->integerField_11->name],
                            3 => ['colNumber' => 3, 'label' => $this->stringField_11->name],
                        ],
                        'entries' => [
                            [
                                1 => ['colNumber' => 1, 'value' => $this->evaluationReport_1111_dm111_ep11_c1_pers1->dedicatedMentor->consultant->personnel->getFullName()],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_111_fr11_if11->value],
                                3 => ['colNumber' => 3, 'value' => $this->stringRecord_111_fr11_sf11->value],
                            ],
                        ],
                    ],
                ],
                [
                    'id' => $this->evaluationPlan_21_prog2->id,
                    'name' => $this->evaluationPlan_21_prog2->name,
                    'summaryTable' => [
                        'header' => [
                            1 => ['colNumber' => 1, 'label' => 'mentor'],
                            2 => ['colNumber' => 2, 'label' => $this->integerField_21->name],
                            3 => ['colNumber' => 3, 'label' => $this->textAreaField_21->name],
                        ],
                        'entries' => [
                            [
                                1 => ['colNumber' => 1, 'value' => "{$this->evaluationReport_2111_dm211_ep21_c1c2_pers1->dedicatedMentor->consultant->personnel->getFullName()} (of team: {$this->teamOne->name})"],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_211_fr21_if21->value],
                                3 => ['colNumber' => 3, 'value' => $this->textAreaRecord_211_fr21_taf21->value],
                            ],
                            [
                                1 => ['colNumber' => 1, 'value' => "{$this->evaluationReport_2211_dm221_ep21_c1_pers1->dedicatedMentor->consultant->personnel->getFullName()} (of team: {$this->teamTwo->name})"],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_221_fr22_if21->value],
                                3 => ['colNumber' => 3, 'value' => $this->textAreaRecord_221_fr22_taf_21->value],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        
        $clientTwoTable = [
            'id' => $this->clientTwo->id,
            'name' => $this->clientTwo->getFullName(),
            'evaluationPlans' => [
                [
                    'id' => $this->evaluationPlan_21_prog2->id,
                    'name' => $this->evaluationPlan_21_prog2->name,
                    'summaryTable' => [
                        'header' => [
                            1 => ['colNumber' => 1, 'label' => 'mentor'],
                            2 => ['colNumber' => 2, 'label' => $this->integerField_21->name],
                            3 => ['colNumber' => 3, 'label' => $this->textAreaField_21->name],
                        ],
                        'entries' => [
                            [
                                1 => ['colNumber' => 1, 'value' => "{$this->evaluationReport_2111_dm211_ep21_c1c2_pers1->dedicatedMentor->consultant->personnel->getFullName()} (of team: {$this->teamOne->name})"],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_211_fr21_if21->value],
                                3 => ['colNumber' => 3, 'value' => $this->textAreaRecord_211_fr21_taf21->value],
                            ],
                        ],
                    ],
                ],
            ],
        ];
                                
        $response = [
            "data" => [
                $clientOneTable,
                $clientTwoTable,
            ],
            "meta" => [
                "code" => 200,
                "type" => "OK",
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_transcript_containCancelledReport_excludeFromResult()
    {
        $this->evaluationReport_1111_dm111_ep11_c1_pers1->cancelled = true;
        $this->transcript();
        $this->seeStatusCode(200);
        
        $clientOneTable = [
            'id' => $this->clientOne->id,
            'name' => $this->clientOne->getFullName(),
            'evaluationPlans' => [
                [
                    'id' => $this->evaluationPlan_21_prog2->id,
                    'name' => $this->evaluationPlan_21_prog2->name,
                    'summaryTable' => [
                        'header' => [
                            1 => ['colNumber' => 1, 'label' => 'mentor'],
                            2 => ['colNumber' => 2, 'label' => $this->integerField_21->name],
                            3 => ['colNumber' => 3, 'label' => $this->textAreaField_21->name],
                        ],
                        'entries' => [
                            [
                                1 => ['colNumber' => 1, 'value' => "{$this->evaluationReport_2111_dm211_ep21_c1c2_pers1->dedicatedMentor->consultant->personnel->getFullName()} (of team: {$this->teamOne->name})"],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_211_fr21_if21->value],
                                3 => ['colNumber' => 3, 'value' => $this->textAreaRecord_211_fr21_taf21->value],
                            ],
                            [
                                1 => ['colNumber' => 1, 'value' => "{$this->evaluationReport_2211_dm221_ep21_c1_pers1->dedicatedMentor->consultant->personnel->getFullName()} (of team: {$this->teamTwo->name})"],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_221_fr22_if21->value],
                                3 => ['colNumber' => 3, 'value' => $this->textAreaRecord_221_fr22_taf_21->value],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        
        $clientTwoTable = [
            'id' => $this->clientTwo->id,
            'name' => $this->clientTwo->getFullName(),
            'evaluationPlans' => [
                [
                    'id' => $this->evaluationPlan_11_prog1->id,
                    'name' => $this->evaluationPlan_11_prog1->name,
                    'summaryTable' => [
                        'header' => [
                            1 => ['colNumber' => 1, 'label' => 'mentor'],
                            2 => ['colNumber' => 2, 'label' => $this->integerField_11->name],
                            3 => ['colNumber' => 3, 'label' => $this->stringField_11->name],
                        ],
                        'entries' => [
                            [
                                1 => ['colNumber' => 1, 'value' => $this->evaluationReport_1221_dm122_ep11_c2_pers2->dedicatedMentor->consultant->personnel->getFullName()],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_121_fr12_if11->value],
                                3 => ['colNumber' => 3, 'value' => $this->stringRecord_121_fr12_sf11->value],
                            ],
                        ],
                    ],
                ],
                [
                    'id' => $this->evaluationPlan_21_prog2->id,
                    'name' => $this->evaluationPlan_21_prog2->name,
                    'summaryTable' => [
                        'header' => [
                            1 => ['colNumber' => 1, 'label' => 'mentor'],
                            2 => ['colNumber' => 2, 'label' => $this->integerField_21->name],
                            3 => ['colNumber' => 3, 'label' => $this->textAreaField_21->name],
                        ],
                        'entries' => [
                            [
                                1 => ['colNumber' => 1, 'value' => "{$this->evaluationReport_2111_dm211_ep21_c1c2_pers1->dedicatedMentor->consultant->personnel->getFullName()} (of team: {$this->teamOne->name})"],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_211_fr21_if21->value],
                                3 => ['colNumber' => 3, 'value' => $this->textAreaRecord_211_fr21_taf21->value],
                            ],
                        ],
                    ],
                ],
            ],
        ];
                                
        $response = [
            "data" => [
                $clientOneTable,
                $clientTwoTable,
            ],
            "meta" => [
                "code" => 200,
                "type" => "OK",
            ],
        ];
        $this->seeJsonContains($response);
    }
}
