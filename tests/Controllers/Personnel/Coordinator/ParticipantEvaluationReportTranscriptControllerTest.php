<?php

namespace Tests\Controllers\Personnel\Coordinator;

use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\DedicatedMentor\RecordOfMentorEvaluationReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfDedicatedMentor;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfEvaluationPlan;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFeedbackForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfIntegerField;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfStringField;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfTextAreaField;
use Tests\Controllers\RecordPreparation\Shared\FormRecord\RecordOfIntegerFieldRecord;
use Tests\Controllers\RecordPreparation\Shared\FormRecord\RecordOfStringFieldRecord;
use Tests\Controllers\RecordPreparation\Shared\FormRecord\RecordOfTextAreaFieldRecord;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class ParticipantEvaluationReportTranscriptControllerTest extends CoordinatorTestCase
{
    protected $transcriptUri;
    protected $downloadtranscriptUri;
    
    protected $evaluationPlanOne, $stringField_11, $integerField_11;
    protected $evaluationPlanTwo, $textAreaField_21, $integerField_21;
    protected $clientParticipantOne;
    protected $clientParticipantTwo;
    protected $dedicatedMentor_m1_p1;
    protected $dedicatedMentor_m1_p2;
    protected $dedicatedMentor_m2_p1;
    protected $evaluationReport_m1_p1_ep1, $stringRecord_111, $integerRecord_111;
    protected $evaluationReport_m1_p1_ep2, $textAreaRecord_211, $integerRecord_211;
    protected $evaluationReport_m1_p2_ep2, $textAreaRecord_212, $integerRecord_212;
    protected $evaluationReport_m2_p1_ep1, $stringRecord_112, $integerRecord_112;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Form')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('IntegerField')->truncate();
        $this->connection->table('TextAreaField')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('EvaluationPlan')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();
        $this->connection->table('IntegerFieldRecord')->truncate();
        $this->connection->table('TextAreaFieldRecord')->truncate();
        $this->connection->table('MentorEvaluationReport')->truncate();
        
        $program = $this->coordinator->program;
        $firm = $program->firm;
        
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
        
        $this->evaluationPlanOne = new RecordOfEvaluationPlan($program, $feedbackFormOne, 1);
        $this->evaluationPlanTwo = new RecordOfEvaluationPlan($program, $feedbackFormTwo, 2);
        
        $participantOne = new RecordOfParticipant($program, '1');
        $participantTwo = new RecordOfParticipant($program, '2');
        
        $clientOne = new RecordOfClient($firm, '1');
        $clientTwo = new RecordOfClient($firm, '2');
        
        $this->clientParticipantOne = new RecordOfClientParticipant($clientOne, $participantOne);
        $this->clientParticipantTwo = new RecordOfClientParticipant($clientTwo, $participantTwo);
        

        $personnelOne = new RecordOfPersonnel($firm, '1');
        $personnelTwo = new RecordOfPersonnel($firm, '2');
        
        $consultantOne = new RecordOfConsultant($program, $personnelOne, '1');
        $consultantTwo = new RecordOfConsultant($program, $personnelTwo, '2');
        
        $this->dedicatedMentor_m1_p1 = new RecordOfDedicatedMentor($participantOne, $consultantOne, '11');
        $this->dedicatedMentor_m1_p2 = new RecordOfDedicatedMentor($participantTwo, $consultantOne, '12');
        $this->dedicatedMentor_m2_p1 = new RecordOfDedicatedMentor($participantOne, $consultantTwo, '21');
        
        $formRecordOne = new RecordOfFormRecord($formOne, '1');
        $formRecordTwo = new RecordOfFormRecord($formTwo, '2');
        $formRecordThree = new RecordOfFormRecord($formTwo, '3');
        $formRecordFour = new RecordOfFormRecord($formOne, '4');
        
        $this->stringRecord_111 = new RecordOfStringFieldRecord($formRecordOne, $this->stringField_11, '111');
        $this->stringRecord_112 = new RecordOfStringFieldRecord($formRecordFour, $this->stringField_11, '112');
        
        $this->integerRecord_111 = new RecordOfIntegerFieldRecord($formRecordOne, $this->integerField_11, '111');
        $this->integerRecord_111->value = 111;
        $this->integerRecord_211 = new RecordOfIntegerFieldRecord($formRecordTwo, $this->integerField_21, '211');
        $this->integerRecord_211->value = 211;
        $this->integerRecord_212 = new RecordOfIntegerFieldRecord($formRecordThree, $this->integerField_21, '212');
        $this->integerRecord_212->value = 212;
        $this->integerRecord_112 = new RecordOfIntegerFieldRecord($formRecordFour, $this->integerField_11, '112');
        $this->integerRecord_112->value = 112;
        
        $this->textAreaRecord_211 = new RecordOfTextAreaFieldRecord($formRecordTwo, $this->textAreaField_21, '211');
        $this->textAreaRecord_212 = new RecordOfTextAreaFieldRecord($formRecordThree, $this->textAreaField_21, '212');
        
        $this->evaluationReport_m1_p1_ep1 = new RecordOfMentorEvaluationReport($this->dedicatedMentor_m1_p1, $this->evaluationPlanOne, $formRecordOne);
        $this->evaluationReport_m1_p1_ep2 = new RecordOfMentorEvaluationReport($this->dedicatedMentor_m1_p1, $this->evaluationPlanTwo, $formRecordTwo);
        $this->evaluationReport_m1_p2_ep2 = new RecordOfMentorEvaluationReport($this->dedicatedMentor_m1_p2, $this->evaluationPlanTwo, $formRecordThree);
        $this->evaluationReport_m2_p1_ep1 = new RecordOfMentorEvaluationReport($this->dedicatedMentor_m2_p1, $this->evaluationPlanOne, $formRecordFour);
        
        $this->transcriptUri = $this->coordinatorUri . "/{$this->coordinator->id}/evaluation-report-transcript";
        $this->downloadtranscriptUri = $this->coordinatorUri . "/{$this->coordinator->id}/download-evaluation-report-transcript-xls";
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Form')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('IntegerField')->truncate();
        $this->connection->table('TextAreaField')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('EvaluationPlan')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();
        $this->connection->table('IntegerFieldRecord')->truncate();
        $this->connection->table('TextAreaFieldRecord')->truncate();
        $this->connection->table('MentorEvaluationReport')->truncate();
    }
    
    protected function transcript()
    {
        $this->evaluationReport_m1_p1_ep1->evaluationPlan->feedbackForm->insert($this->connection);
        $this->evaluationReport_m1_p1_ep2->evaluationPlan->feedbackForm->insert($this->connection);
        
        $this->evaluationReport_m1_p1_ep1->evaluationPlan->insert($this->connection);
        $this->evaluationReport_m1_p1_ep2->evaluationPlan->insert($this->connection);
        
        $this->evaluationReport_m1_p1_ep1->dedicatedMentor->consultant->personnel->insert($this->connection);
        $this->evaluationReport_m2_p1_ep1->dedicatedMentor->consultant->personnel->insert($this->connection);
        
        $this->evaluationReport_m1_p1_ep1->dedicatedMentor->consultant->insert($this->connection);
        $this->evaluationReport_m2_p1_ep1->dedicatedMentor->consultant->insert($this->connection);
        
        $this->evaluationReport_m1_p1_ep1->dedicatedMentor->participant->insert($this->connection);
        $this->evaluationReport_m1_p2_ep2->dedicatedMentor->participant->insert($this->connection);
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantTwo->client->insert($this->connection);
        
        $this->clientParticipantOne->insert($this->connection);
        $this->clientParticipantTwo->insert($this->connection);
        
        $this->evaluationReport_m1_p1_ep1->dedicatedMentor->insert($this->connection);
        $this->evaluationReport_m1_p2_ep2->dedicatedMentor->insert($this->connection);
        $this->evaluationReport_m2_p1_ep1->dedicatedMentor->insert($this->connection);
        
        $this->evaluationReport_m1_p1_ep1->insert($this->connection);
        $this->evaluationReport_m1_p1_ep2->insert($this->connection);
        $this->evaluationReport_m1_p2_ep2->insert($this->connection);
        $this->evaluationReport_m2_p1_ep1->insert($this->connection);
        
        
        $this->stringRecord_111->stringField->insert($this->connection);
        $this->integerRecord_112->integerField->insert($this->connection);
        $this->integerRecord_211->integerField->insert($this->connection);
        $this->textAreaRecord_211->textAreaField->insert($this->connection);
        
        $this->stringRecord_111->insert($this->connection);
        $this->stringRecord_112->insert($this->connection);
        $this->integerRecord_111->insert($this->connection);
        $this->integerRecord_112->insert($this->connection);
        $this->integerRecord_211->insert($this->connection);
        $this->integerRecord_212->insert($this->connection);
        $this->textAreaRecord_211->insert($this->connection);
        $this->textAreaRecord_212->insert($this->connection);
        
        $this->get($this->transcriptUri, $this->coordinator->personnel->token);
    }
    public function test_transcript_200()
    {
        $this->transcript();
        $this->seeStatusCode(200);
        
        $participantOneTable = [
            'id' => $this->clientParticipantOne->participant->id,
            'name' => $this->clientParticipantOne->client->getFullName(),
            'evaluationPlans' => [
                [
                    "id" => $this->evaluationPlanOne->id,
                    "name" => $this->evaluationPlanOne->name,
                    "summaryTable" => [
                        'header' => [
                            1 => ['colNumber' => 1, 'label' => 'mentor'],
                            2 => ['colNumber' => 2, 'label' => $this->integerField_11->name],
                            3 => ['colNumber' => 3, 'label' => $this->stringField_11->name],
                        ],
                        'entries' => [
                            [
                                1 => ['colNumber' => 1, 'value' => $this->evaluationReport_m1_p1_ep1->dedicatedMentor->consultant->personnel->getFullName()],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_111->value],
                                3 => ['colNumber' => 3, 'value' => $this->stringRecord_111->value],
                            ],
                            [
                                1 => ['colNumber' => 1, 'value' => $this->evaluationReport_m2_p1_ep1->dedicatedMentor->consultant->personnel->getFullName()],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_112->value],
                                3 => ['colNumber' => 3, 'value' => $this->stringRecord_112->value],
                            ],
                        ],
                    ],
                ],
                [
                    "id" => $this->evaluationPlanTwo->id,
                    "name" => $this->evaluationPlanTwo->name,
                    "summaryTable" => [
                        'header' => [
                            1 => ['colNumber' => 1, 'label' => 'mentor'],
                            2 => ['colNumber' => 2, 'label' => $this->integerField_21->name],
                            3 => ['colNumber' => 3, 'label' => $this->textAreaField_21->name],
                        ],
                        'entries' => [
                            [
                                1 => ['colNumber' => 1, 'value' => $this->evaluationReport_m1_p1_ep2->dedicatedMentor->consultant->personnel->getFullName()],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_211->value],
                                3 => ['colNumber' => 3, 'value' => $this->textAreaRecord_211->value],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        
        $participantTwoTable = [
            'id' => $this->clientParticipantTwo->participant->id,
            'name' => $this->clientParticipantTwo->client->getFullName(),
            'evaluationPlans' => [
                [
                    "id" => $this->evaluationPlanTwo->id,
                    "name" => $this->evaluationPlanTwo->name,
                    "summaryTable" => [
                        'header' => [
                            1 => ['colNumber' => 1, 'label' => 'mentor'],
                            2 => ['colNumber' => 2, 'label' => $this->integerField_21->name],
                            3 => ['colNumber' => 3, 'label' => $this->textAreaField_21->name],
                        ],
                        'entries' => [
                            [
                                1 => ['colNumber' => 1, 'value' => $this->evaluationReport_m1_p2_ep2->dedicatedMentor->consultant->personnel->getFullName()],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_212->value],
                                3 => ['colNumber' => 3, 'value' => $this->textAreaRecord_212->value],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        
        $result = [
            "data" => [
                $participantOneTable,
                $participantTwoTable,
            ],
            "meta" => [
                "code" => 200,
                "type" => "OK",
            ],
        ];
        $this->seeJsonContains($result);
    }
    public function test_evaluationPlanIdListApplied_200()
    {
        $this->transcriptUri .= "?evaluationPlanIdList[]={$this->evaluationPlanOne->id}";
        $this->transcript();
        $this->seeStatusCode(200);
        
        $participantOneTable = [
            'id' => $this->clientParticipantOne->participant->id,
            'name' => $this->clientParticipantOne->client->getFullName(),
            'evaluationPlans' => [
                [
                    "id" => $this->evaluationPlanOne->id,
                    "name" => $this->evaluationPlanOne->name,
                    "summaryTable" => [
                        'header' => [
                            1 => ['colNumber' => 1, 'label' => 'mentor'],
                            2 => ['colNumber' => 2, 'label' => $this->integerField_11->name],
                            3 => ['colNumber' => 3, 'label' => $this->stringField_11->name],
                        ],
                        'entries' => [
                            [
                                1 => ['colNumber' => 1, 'value' => $this->evaluationReport_m1_p1_ep1->dedicatedMentor->consultant->personnel->getFullName()],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_111->value],
                                3 => ['colNumber' => 3, 'value' => $this->stringRecord_111->value],
                            ],
                            [
                                1 => ['colNumber' => 1, 'value' => $this->evaluationReport_m2_p1_ep1->dedicatedMentor->consultant->personnel->getFullName()],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_112->value],
                                3 => ['colNumber' => 3, 'value' => $this->stringRecord_112->value],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        
        $result = [
            "data" => [
                $participantOneTable,
            ],
            "meta" => [
                "code" => 200,
                "type" => "OK",
            ],
        ];
        $this->seeJsonContains($result);
    }
    public function test_transcript_mentorIdListApplied_200()
    {
        $this->transcriptUri .= "?mentorIdList[]={$this->dedicatedMentor_m1_p1->consultant->id}";
        $this->transcript();
        $this->seeStatusCode(200);
        
        $participantOneTable = [
            'id' => $this->clientParticipantOne->participant->id,
            'name' => $this->clientParticipantOne->client->getFullName(),
            'evaluationPlans' => [
                [
                    "id" => $this->evaluationPlanOne->id,
                    "name" => $this->evaluationPlanOne->name,
                    "summaryTable" => [
                        'header' => [
                            1 => ['colNumber' => 1, 'label' => 'mentor'],
                            2 => ['colNumber' => 2, 'label' => $this->integerField_11->name],
                            3 => ['colNumber' => 3, 'label' => $this->stringField_11->name],
                        ],
                        'entries' => [
                            [
                                1 => ['colNumber' => 1, 'value' => $this->evaluationReport_m1_p1_ep1->dedicatedMentor->consultant->personnel->getFullName()],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_111->value],
                                3 => ['colNumber' => 3, 'value' => $this->stringRecord_111->value],
                            ],
                        ],
                    ],
                ],
                [
                    "id" => $this->evaluationPlanTwo->id,
                    "name" => $this->evaluationPlanTwo->name,
                    "summaryTable" => [
                        'header' => [
                            1 => ['colNumber' => 1, 'label' => 'mentor'],
                            2 => ['colNumber' => 2, 'label' => $this->integerField_21->name],
                            3 => ['colNumber' => 3, 'label' => $this->textAreaField_21->name],
                        ],
                        'entries' => [
                            [
                                1 => ['colNumber' => 1, 'value' => $this->evaluationReport_m1_p1_ep2->dedicatedMentor->consultant->personnel->getFullName()],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_211->value],
                                3 => ['colNumber' => 3, 'value' => $this->textAreaRecord_211->value],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        
        $participantTwoTable = [
            'id' => $this->clientParticipantTwo->participant->id,
            'name' => $this->clientParticipantTwo->client->getFullName(),
            'evaluationPlans' => [
                [
                    "id" => $this->evaluationPlanTwo->id,
                    "name" => $this->evaluationPlanTwo->name,
                    "summaryTable" => [
                        'header' => [
                            1 => ['colNumber' => 1, 'label' => 'mentor'],
                            2 => ['colNumber' => 2, 'label' => $this->integerField_21->name],
                            3 => ['colNumber' => 3, 'label' => $this->textAreaField_21->name],
                        ],
                        'entries' => [
                            [
                                1 => ['colNumber' => 1, 'value' => $this->evaluationReport_m1_p2_ep2->dedicatedMentor->consultant->personnel->getFullName()],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_212->value],
                                3 => ['colNumber' => 3, 'value' => $this->textAreaRecord_212->value],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        
        $result = [
            "data" => [
                $participantOneTable,
                $participantTwoTable,
            ],
            "meta" => [
                "code" => 200,
                "type" => "OK",
            ],
        ];
        $this->seeJsonContains($result);
    }
    public function test_transcript_participantIdListFilterApplied_200()
    {
        $this->transcriptUri .= "?participantIdList[]={$this->clientParticipantOne->participant->id}";
        $this->transcript();
        $this->seeStatusCode(200);
        
        $participantOneTable = [
            'id' => $this->clientParticipantOne->participant->id,
            'name' => $this->clientParticipantOne->client->getFullName(),
            'evaluationPlans' => [
                [
                    "id" => $this->evaluationPlanOne->id,
                    "name" => $this->evaluationPlanOne->name,
                    "summaryTable" => [
                        'header' => [
                            1 => ['colNumber' => 1, 'label' => 'mentor'],
                            2 => ['colNumber' => 2, 'label' => $this->integerField_11->name],
                            3 => ['colNumber' => 3, 'label' => $this->stringField_11->name],
                        ],
                        'entries' => [
                            [
                                1 => ['colNumber' => 1, 'value' => $this->evaluationReport_m1_p1_ep1->dedicatedMentor->consultant->personnel->getFullName()],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_111->value],
                                3 => ['colNumber' => 3, 'value' => $this->stringRecord_111->value],
                            ],
                            [
                                1 => ['colNumber' => 1, 'value' => $this->evaluationReport_m2_p1_ep1->dedicatedMentor->consultant->personnel->getFullName()],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_112->value],
                                3 => ['colNumber' => 3, 'value' => $this->stringRecord_112->value],
                            ],
                        ],
                    ],
                ],
                [
                    "id" => $this->evaluationPlanTwo->id,
                    "name" => $this->evaluationPlanTwo->name,
                    "summaryTable" => [
                        'header' => [
                            1 => ['colNumber' => 1, 'label' => 'mentor'],
                            2 => ['colNumber' => 2, 'label' => $this->integerField_21->name],
                            3 => ['colNumber' => 3, 'label' => $this->textAreaField_21->name],
                        ],
                        'entries' => [
                            [
                                1 => ['colNumber' => 1, 'value' => $this->evaluationReport_m1_p1_ep2->dedicatedMentor->consultant->personnel->getFullName()],
                                2 => ['colNumber' => 2, 'value' => $this->integerRecord_211->value],
                                3 => ['colNumber' => 3, 'value' => $this->textAreaRecord_211->value],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        
        $result = [
            "data" => [
                $participantOneTable,
            ],
            "meta" => [
                "code" => 200,
                "type" => "OK",
            ],
        ];
        $this->seeJsonContains($result);
    }
}
