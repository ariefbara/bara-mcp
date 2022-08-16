<?php

namespace Tests\Controllers\Client\AsTeamMember;

use DateTime;
use SharedContext\Domain\ValueObject\ParticipantStatus;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\RecordOfAssignmentFieldValue;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfAssignmentField;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfMetricAssignmentReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMetricAssignment;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfParticipantInvoice;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMetric;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfSponsor;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\RecordOfFirm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfInvoice;

class ProgramParticipationControllerTest extends ProgramParticipationTestCase
{
    protected $teamParticipantOne;
    protected $teamParticipantTwo;
    protected $programThree;
//    protected $inactiveProgramParticipation;
    protected $metricAssignment;
    protected $assignmentField;
    protected $assignmentFieldOne;
    protected $metricAssignmentReport;
    protected $metricAssignmentReportOne_lastApproved;
    protected $metricAssignmentReportTwo_last;
    protected $assignmentFieldValue_00;
    protected $assignmentFieldValue_01;
    protected $sponsorOne;
    protected $sponsorTwo;
    protected $participantInvoiceOne;
    //
    protected $applyProgramRequest;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->connection->table("MetricAssignment")->truncate();
        $this->connection->table("Metric")->truncate();
        $this->connection->table("AssignmentField")->truncate();
        $this->connection->table("MetricAssignmentReport")->truncate();
        $this->connection->table("AssignmentFieldValue")->truncate();
        $this->connection->table("Sponsor")->truncate();
        $this->connection->table("ParticipantInvoice")->truncate();
        $this->connection->table("Invoice")->truncate();
        
        $team = $this->teamMember->team;
        $firm = $team->firm;
        
        $programOne = new RecordOfProgram($firm, '1');
        $programTwo = new RecordOfProgram($firm, '2');
        $this->programThree = new RecordOfProgram($firm, '3');
        
        $participantOne = new RecordOfParticipant($programOne, '1');
        $participantTwo = new RecordOfParticipant($programTwo, '2');
        
        $this->teamParticipantOne = new RecordOfTeamProgramParticipation($team, $participantOne);
        $this->teamParticipantTwo = new RecordOfTeamProgramParticipation($team, $participantTwo);
        
        $this->metricAssignment = new RecordOfMetricAssignment($participantOne, 0);
//        $this->connection->table("MetricAssignment")->insert($this->metricAssignment->toArrayForDbEntry());
        
        $metric = new RecordOfMetric($programOne, 0);
        $metricOne = new RecordOfMetric($programOne, 1);
//        $this->connection->table("Metric")->insert($metric->toArrayForDbEntry());
//        $this->connection->table("Metric")->insert($metricOne->toArrayForDbEntry());
        
        $this->assignmentField = new RecordOfAssignmentField($this->metricAssignment, $metric, 0);
        $this->assignmentFieldOne = new RecordOfAssignmentField($this->metricAssignment, $metricOne, 1);
//        $this->connection->table("AssignmentField")->insert($this->assignmentField->toArrayForDbEntry());
//        $this->connection->table("AssignmentField")->insert($this->assignmentFieldOne->toArrayForDbEntry());
        
        $this->metricAssignmentReport = new RecordOfMetricAssignmentReport($this->metricAssignment, 0);
        $this->metricAssignmentReport->observationTime = (new DateTime("-2 months"))->format("Y-m-d H:i:s");
        $this->metricAssignmentReport->approved = true;
        $this->metricAssignmentReportOne_lastApproved = new RecordOfMetricAssignmentReport($this->metricAssignment, 1);
        $this->metricAssignmentReportOne_lastApproved->observationTime = (new DateTime("-2 weeks"))->format("Y-m-d H:i:s");
        $this->metricAssignmentReportOne_lastApproved->approved = true;
        $this->metricAssignmentReportTwo_last = new RecordOfMetricAssignmentReport($this->metricAssignment, 2);
        $this->metricAssignmentReportTwo_last->observationTime = (new DateTime("-2 days"))->format("Y-m-d H:i:s");
//        $this->connection->table("MetricAssignmentReport")->insert($this->metricAssignmentReport->toArrayForDbEntry());
//        $this->connection->table("MetricAssignmentReport")->insert($this->metricAssignmentReportOne_lastApproved->toArrayForDbEntry());
//        $this->connection->table("MetricAssignmentReport")->insert($this->metricAssignmentReportTwo_last->toArrayForDbEntry());
        
        $this->assignmentFieldValue_00 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportOne_lastApproved, $this->assignmentField, "00");
        $this->assignmentFieldValue_01 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportOne_lastApproved, $this->assignmentFieldOne, "01");
//        $this->connection->table("AssignmentFieldValue")->insert($this->assignmentFieldValue_00->toArrayForDbEntry());
//        $this->connection->table("AssignmentFieldValue")->insert($this->assignmentFieldValue_01->toArrayForDbEntry());
        
        $this->sponsorOne = new RecordOfSponsor($programOne, "1");
        $this->sponsorTwo = new RecordOfSponsor($programOne, "2");
        
        $invoiceOne = new RecordOfInvoice(1);
        $this->participantInvoiceOne = new RecordOfParticipantInvoice($participantOne, $invoiceOne);
        
        $this->applyProgramRequest = [
            'programId' => $this->programThree->id,
        ];
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("MetricAssignment")->truncate();
        $this->connection->table("Metric")->truncate();
        $this->connection->table("AssignmentField")->truncate();
        $this->connection->table("MetricAssignmentReport")->truncate();
        $this->connection->table("AssignmentFieldValue")->truncate();
        $this->connection->table("Sponsor")->truncate();
        $this->connection->table("ParticipantInvoice")->truncate();
        $this->connection->table("Invoice")->truncate();
    }
    
    protected function applyToProgram()
    {
        $this->programThree->insert($this->connection);
        $uri = $this->programParticipationUri . "/apply-program";
        $this->post($uri, $this->applyProgramRequest, $this->client->token);
    }
    public function test_applyToProgram_200()
    {
$this->disableExceptionHandling();
        $this->applyToProgram();
        $this->seeStatusCode(201);
        
        $response = [
            "program" => [
                "id" => $this->programThree->id,
                "name" => $this->programThree->name,
                'sponsors' => [],
            ],
            "status" => 'REGISTERED',
            "programPrice" => $this->programThree->price,
        ];
        $this->seeJsonContains($response);
        
        $participantEntry = [
            'Program_id' => $this->programThree->id,
            'status' => ParticipantStatus::REGISTERED,
            'programPrice' => $this->programThree->price,
        ];
        $this->seeInDatabase('Participant', $participantEntry);
    }
    public function test_applyToProgram_autoAcceptProgram_activeParticipant()
    {
        $this->programThree->autoAccept = true;
        $this->applyToProgram();
        $this->seeStatusCode(201);
        
        $response = [
            "program" => [
                "id" => $this->programThree->id,
                "name" => $this->programThree->name,
                'sponsors' => [],
            ],
            "status" => 'ACTIVE',
            "programPrice" => $this->programThree->price,
        ];
        $this->seeJsonContains($response);
        
        $participantEntry = [
            'Program_id' => $this->programThree->id,
            'status' => ParticipantStatus::ACTIVE,
            'programPrice' => $this->programThree->price,
        ];
        $this->seeInDatabase('Participant', $participantEntry);
    }
    public function test_applyToProgram_autoAcceptPaidProgram_participantWithSettlementRequiredStatus()
    {
        $this->programThree->autoAccept = true;
        $this->programThree->price = 100000;
        $this->applyToProgram();
        $this->seeStatusCode(201);
        
        $response = [
            "program" => [
                "id" => $this->programThree->id,
                "name" => $this->programThree->name,
                'sponsors' => [],
            ],
            "status" => 'SETTLEMENT_REQUIRED',
            "programPrice" => $this->programThree->price,
        ];
        $this->seeJsonContains($response);
        
        $participantEntry = [
            'Program_id' => $this->programThree->id,
            'status' => ParticipantStatus::SETTLEMENT_REQUIRED,
            'programPrice' => $this->programThree->price,
        ];
        $this->seeInDatabase('Participant', $participantEntry);
    }
    public function test_applyToProgram_participantWithSettlementRequiredStatus_generateInvoice()
    {
        $this->programThree->autoAccept = true;
        $this->programThree->price = 100000;
        $this->applyToProgram();
        $this->seeStatusCode(201);
        
        $invoiceEntry = [
            'settled' => false,
        ];
        $this->seeInDatabase('Invoice', $invoiceEntry);
    }
    public function test_applyToProgram_participantOfSameProgramWithActiveLifecycle_registered_403()
    {
        $this->teamParticipantOne->participant->program = $this->programThree;
        $this->teamParticipantOne->participant->status = ParticipantStatus::REGISTERED;
        $this->teamParticipantOne->insert($this->connection);
        
        $this->applyToProgram();
        $this->seeStatusCode(403);
    }
    public function test_applyToProgram_participantOfSameProgramWithActiveLifecycle_settlementRequired_403()
    {
        $this->teamParticipantOne->participant->program = $this->programThree;
        $this->teamParticipantOne->participant->status = ParticipantStatus::SETTLEMENT_REQUIRED;
        $this->teamParticipantOne->insert($this->connection);
        
        $this->applyToProgram();
        $this->seeStatusCode(403);
    }
    public function test_applyToProgram_participantOfSameProgramWithActiveLifecycle_active_403()
    {
        $this->teamParticipantOne->participant->program = $this->programThree;
        $this->teamParticipantOne->participant->status = ParticipantStatus::ACTIVE;
        $this->teamParticipantOne->insert($this->connection);
        
        $this->applyToProgram();
        $this->seeStatusCode(403);
    }
    public function test_applyToProgram_participantOfSameProgramWithEndedLifecycle_201()
    {
        $this->teamParticipantOne->participant->program = $this->programThree;
        $this->teamParticipantOne->participant->status = ParticipantStatus::REJECTED;
        $this->teamParticipantOne->insert($this->connection);
        
        $this->applyToProgram();
        $this->seeStatusCode(201);
    }
    public function test_applyToProgram_programFromDifferentFirm_403()
    {
        $otherFirm = new RecordOfFirm('other');
        $otherFirm->insert($this->connection);
        $this->programThree->firm = $otherFirm;
        
        $this->applyToProgram();
        $this->seeStatusCode(403);
    }
    
    protected function show()
    {
        $this->teamParticipantOne->participant->program->insert($this->connection);
        $this->teamParticipantOne->insert($this->connection);
        
        $uri = $this->programParticipationUri . "/{$this->teamParticipantOne->id}";
        $this->get($uri, $this->client->token);
    }
    public function test_show_200()
    {
$this->disableExceptionHandling();
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->teamParticipantOne->id,
            'program' => [
                'id' => $this->teamParticipantOne->participant->program->id,
                'name' => $this->teamParticipantOne->participant->program->name,
                'sponsors' => [],
                
            ],
            'status' => 'REGISTERED',
            'programPrice' => $this->teamParticipantOne->participant->programPrice,
            'metricAssignment' => null,
            'invoice' => null,
        ];
        $this->seeJsonContains($response);
    }
    public function test_show_programHasSponsors_200()
    {
        $this->sponsorOne->insert($this->connection);
        $this->sponsorTwo->insert($this->connection);
        
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            'sponsors' => [
                [
                    "id" => $this->sponsorOne->id,
                    "name" => $this->sponsorOne->name,
                    "website" => $this->sponsorOne->website,
                    "logo" => null,
                ],
                [
                    "id" => $this->sponsorTwo->id,
                    "name" => $this->sponsorTwo->name,
                    "website" => $this->sponsorTwo->website,
                    "logo" => null,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_show_participantHasInvoice_200()
    {
        $this->participantInvoiceOne->insert($this->connection);
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            'invoice' => [
                'issuedTime' => $this->participantInvoiceOne->invoice->issuedTime,
                'expiredTime' => $this->participantInvoiceOne->invoice->expiredTime,
                'paymentLink' => $this->participantInvoiceOne->invoice->paymentLink,
                'settled' => $this->participantInvoiceOne->invoice->settled,
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_show_hasMetricAssignment_200()
    {
        $this->metricAssignment->insert($this->connection);
        
        $this->assignmentField->metric->insert($this->connection);
        $this->assignmentFieldOne->metric->insert($this->connection);
        $this->assignmentField->insert($this->connection);
        $this->assignmentFieldOne->insert($this->connection);
        
        $this->metricAssignmentReport->insert($this->connection);
        $this->metricAssignmentReportOne_lastApproved->insert($this->connection);
        $this->metricAssignmentReportTwo_last->insert($this->connection);
        
        $this->assignmentFieldValue_00->insert($this->connection);
        $this->assignmentFieldValue_01->insert($this->connection);
        
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            "metricAssignment" => [
                "id" => $this->metricAssignment->id,
                "startDate" => $this->metricAssignment->startDate,
                "endDate" => $this->metricAssignment->endDate,
                "assignmentFields" => [
                    [
                        "id" => $this->assignmentField->id,
                        "target" => $this->assignmentField->target,
                        "metric" => [
                            "id" => $this->assignmentField->metric->id,
                            "name" => $this->assignmentField->metric->name,
                            "minValue" => $this->assignmentField->metric->minValue,
                            "maxValue" => $this->assignmentField->metric->maxValue,
                            "higherIsBetter" => $this->assignmentField->metric->higherIsBetter,
                        ],
                    ],
                    [
                        "id" => $this->assignmentFieldOne->id,
                        "target" => $this->assignmentFieldOne->target,
                        "metric" => [
                            "id" => $this->assignmentFieldOne->metric->id,
                            "name" => $this->assignmentFieldOne->metric->name,
                            "minValue" => $this->assignmentFieldOne->metric->minValue,
                            "maxValue" => $this->assignmentFieldOne->metric->maxValue,
                            "higherIsBetter" => $this->assignmentFieldOne->metric->higherIsBetter,
                        ],
                    ],
                ],
                "lastMetricAssignmentReport" => [
                    "id" => $this->metricAssignmentReportOne_lastApproved->id,
                    "observationTime" => $this->metricAssignmentReportOne_lastApproved->observationTime,
                    "submitTime" => $this->metricAssignmentReportOne_lastApproved->submitTime,
                    "removed" => $this->metricAssignmentReportOne_lastApproved->removed,
                    "assignmentFieldValues" => [
                        [
                            "id" => $this->assignmentFieldValue_00->id,
                            "value" => $this->assignmentFieldValue_00->inputValue,
                            "assignmentFieldId" => $this->assignmentFieldValue_00->assignmentField->id,
                        ],
                        [
                            "id" => $this->assignmentFieldValue_01->id,
                            "value" => $this->assignmentFieldValue_01->inputValue,
                            "assignmentFieldId" => $this->assignmentFieldValue_01->assignmentField->id,
                        ],
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    
    protected function showAll()
    {
        $this->teamParticipantOne->participant->program->insert($this->connection);
        $this->teamParticipantTwo->participant->program->insert($this->connection);
        
        $this->teamParticipantOne->insert($this->connection);
        $this->teamParticipantTwo->insert($this->connection);
        
        $this->get($this->programParticipationUri, $this->client->token);
    }
    public function test_showAll_200()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            "total" => 3, 
            "list" => [
                [
                    "id" => $this->programParticipation->id,
                    "program" => [
                        "id" => $this->programParticipation->participant->program->id,
                        "name" => $this->programParticipation->participant->program->name,
                    ],
                    "status" => 'REGISTERED',
                    "programPrice" => $this->programParticipation->participant->programPrice,
                ],
                [
                    "id" => $this->teamParticipantOne->id,
                    "program" => [
                        "id" => $this->teamParticipantOne->participant->program->id,
                        "name" => $this->teamParticipantOne->participant->program->name,
                    ],
                    "status" => 'REGISTERED',
                    "programPrice" => $this->teamParticipantOne->participant->programPrice,
                ],
                [
                    "id" => $this->teamParticipantTwo->id,
                    "program" => [
                        "id" => $this->teamParticipantTwo->participant->program->id,
                        "name" => $this->teamParticipantTwo->participant->program->name,
                    ],
                    "status" => 'REGISTERED',
                    "programPrice" => $this->teamParticipantTwo->participant->programPrice,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_usingActiveStatusFilter()
    {
$this->disableExceptionHandling();
        $this->teamParticipantOne->participant->status = ParticipantStatus::REJECTED;
        
        $this->programParticipationUri .= "?activeStatus=true";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalReponse = ["total" => 2];
        $this->seeJsonContains($totalReponse);
        
        $programParticipationResponse = [
            "id" => $this->programParticipation->id,
        ];
        $this->seeJsonContains(['id' => $this->programParticipation->id]);
        $this->seeJsonDoesntContains(['id' => $this->teamParticipantOne->id]);
        $this->seeJsonContains(['id' => $this->teamParticipantTwo->id]);
    }
    
/*
    public function test_quit_200()
    {
        $uri = $this->programParticipationUri . "/{$this->programParticipation->id}/quit";
        $this->patch($uri, [], $this->teamMember->client->token)
            ->seeStatusCode(200);
        
        $participantEntry = [
            "id" => $this->programParticipation->participant->id,
            "active" => false,
            "note" => 'quit',
        ];
        $this->seeInDatabase('Participant', $participantEntry);
    }
    public function test_quit_alreadyInactive_403()
    {
        $uri = $this->programParticipationUri . "/{$this->inactiveProgramParticipation->id}/quit";
        $this->patch($uri, [], $this->teamMember->client->token)
            ->seeStatusCode(403);
    }
    public function test_quit_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        
        $uri = $this->programParticipationUri . "/{$this->programParticipation->id}/quit";
        $this->patch($uri, [], $this->teamMember->client->token)
            ->seeStatusCode(403);
    }
 * 
 */
    
/*
    public function test_show()
    {
        $this->sponsorOne->disabled = true;
        $this->sponsorOne->insert($this->connection);
        $this->sponsorTwo->insert($this->connection);
        
        $response = [
            "id" => $this->programParticipation->id,
            "program" => [
                "id" => $this->programParticipation->participant->program->id,
                "name" => $this->programParticipation->participant->program->name,
                "removed" => $this->programParticipation->participant->program->removed,
                "sponsors" => [
                    [
                        "id" => $this->sponsorTwo->id,
                        "name" => $this->sponsorTwo->name,
                        "website" => $this->sponsorTwo->website,
                        "logo" => null,
                    ],
                ],
            ],
            "enrolledTime" => $this->programParticipation->participant->enrolledTime,
            "active" => $this->programParticipation->participant->active,
            "note" => $this->programParticipation->participant->note,
            "metricAssignment" => [
                "id" => $this->metricAssignment->id,
                "startDate" => $this->metricAssignment->startDate,
                "endDate" => $this->metricAssignment->endDate,
                "assignmentFields" => [
                    [
                        "id" => $this->assignmentField->id,
                        "target" => $this->assignmentField->target,
                        "metric" => [
                            "id" => $this->assignmentField->metric->id,
                            "name" => $this->assignmentField->metric->name,
                            "minValue" => $this->assignmentField->metric->minValue,
                            "maxValue" => $this->assignmentField->metric->maxValue,
                            "higherIsBetter" => $this->assignmentField->metric->higherIsBetter,
                        ],
                    ],
                    [
                        "id" => $this->assignmentFieldOne->id,
                        "target" => $this->assignmentFieldOne->target,
                        "metric" => [
                            "id" => $this->assignmentFieldOne->metric->id,
                            "name" => $this->assignmentFieldOne->metric->name,
                            "minValue" => $this->assignmentFieldOne->metric->minValue,
                            "maxValue" => $this->assignmentFieldOne->metric->maxValue,
                            "higherIsBetter" => $this->assignmentFieldOne->metric->higherIsBetter,
                        ],
                    ],
                ],
                "lastMetricAssignmentReport" => [
                    "id" => $this->metricAssignmentReportOne_lastApproved->id,
                    "observationTime" => $this->metricAssignmentReportOne_lastApproved->observationTime,
                    "submitTime" => $this->metricAssignmentReportOne_lastApproved->submitTime,
                    "removed" => $this->metricAssignmentReportOne_lastApproved->removed,
                    "assignmentFieldValues" => [
                        [
                            "id" => $this->assignmentFieldValue_00->id,
                            "value" => $this->assignmentFieldValue_00->inputValue,
                            "assignmentFieldId" => $this->assignmentFieldValue_00->assignmentField->id,
                        ],
                        [
                            "id" => $this->assignmentFieldValue_01->id,
                            "value" => $this->assignmentFieldValue_01->inputValue,
                            "assignmentFieldId" => $this->assignmentFieldValue_01->assignmentField->id,
                        ],
                    ],
                ],
            ],
        ];
        $uri = $this->programParticipationUri . "/{$this->programParticipation->id}";
        $this->get($uri, $this->teamMember->client->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
echo $uri;
    }
    public function test_show_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        
        $uri = $this->programParticipationUri . "/{$this->programParticipation->id}";
        $this->get($uri, $this->teamMember->client->token)
            ->seeStatusCode(403);
    }
    
    public function test_showAll()
    {
        $response = [
            "total" => 2, 
            "list" => [
                [
                    "id" => $this->programParticipation->id,
                    "program" => [
                        "id" => $this->programParticipation->participant->program->id,
                        "name" => $this->programParticipation->participant->program->name,
                        "removed" => $this->programParticipation->participant->program->removed,
                    ],
                    "enrolledTime" => $this->programParticipation->participant->enrolledTime,
                    "active" => $this->programParticipation->participant->active,
                    "note" => $this->programParticipation->participant->note,
                ],
                [
                    "id" => $this->inactiveProgramParticipation->id,
                    "program" => [
                        "id" => $this->inactiveProgramParticipation->participant->program->id,
                        "name" => $this->inactiveProgramParticipation->participant->program->name,
                        "removed" => $this->inactiveProgramParticipation->participant->program->removed,
                    ],
                    "enrolledTime" => $this->inactiveProgramParticipation->participant->enrolledTime,
                    "active" => $this->inactiveProgramParticipation->participant->active,
                    "note" => $this->inactiveProgramParticipation->participant->note,
                ],
            ],
        ];
        $this->get($this->programParticipationUri, $this->teamMember->client->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_showAll_applyActiveStatusFilter_200()
    {
        $totalResponse = ["total" => 1];
        $programParticipationResponse = ["id" => $this->programParticipation->id];
        $uri = $this->programParticipationUri . "?activeStatus=true";
        $this->get($uri, $this->teamMember->client->token)
            ->seeStatusCode(200)
            ->seeJsonContains($totalResponse)
            ->seeJsonContains($programParticipationResponse);
    }
    public function test_showAll_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        
        $this->get($this->programParticipationUri, $this->teamMember->client->token)
            ->seeStatusCode(403);
    }
 * 
 */
}
