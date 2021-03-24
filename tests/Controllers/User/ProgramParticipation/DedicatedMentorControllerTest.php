<?php

namespace Tests\Controllers\User\ProgramParticipation;

use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfDedicatedMentor;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\User\ProgramParticipationTestCase;

class DedicatedMentorControllerTest extends ProgramParticipationTestCase
{

    protected $dedicatedMentorUri;
    protected $dedicatedMentorOne;
    protected $dedicatedMentorTwo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();

        $this->dedicatedMentorUri = $this->programParticipationUri . "/{$this->programParticipation->id}/dedicated-mentors";

        $participant = $this->programParticipation->participant;
        $program = $participant->program;
        $firm = $program->firm;

        $personnelOne = new RecordOfPersonnel($firm, '1');
        $personnelTwo = new RecordOfPersonnel($firm, '2');

        $consultantOne = new RecordOfConsultant($program, $personnelOne, '1');
        $consultantTwo = new RecordOfConsultant($program, $personnelTwo, '2');

        $this->dedicatedMentorOne = new RecordOfDedicatedMentor($participant, $consultantOne, '1');
        $this->dedicatedMentorTwo = new RecordOfDedicatedMentor($participant, $consultantTwo, '2');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();
    }

    protected function executeShow()
    {
        $this->dedicatedMentorOne->consultant->personnel->insert($this->connection);
        $this->dedicatedMentorOne->consultant->insert($this->connection);
        $this->dedicatedMentorOne->insert($this->connection);

        $uri = $this->dedicatedMentorUri . "/{$this->dedicatedMentorOne->id}";
        $this->get($uri, $this->programParticipation->user->token);
    }

    public function test_show_200()
    {
        $this->executeShow();
        $this->seeStatusCode(200);

        $response = [
            'id' => $this->dedicatedMentorOne->id,
            'modifiedTime' => $this->dedicatedMentorOne->modifiedTime,
            'cancelled' => $this->dedicatedMentorOne->cancelled,
            'consultant' => [
                'id' => $this->dedicatedMentorOne->consultant->id,
                'personnel' => [
                    'id' => $this->dedicatedMentorOne->consultant->personnel->id,
                    'name' => $this->dedicatedMentorOne->consultant->personnel->getFullName(),
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }

    protected function executeShowAll(?string $uri = null)
    {
        $this->dedicatedMentorOne->consultant->personnel->insert($this->connection);
        $this->dedicatedMentorOne->consultant->insert($this->connection);
        $this->dedicatedMentorOne->insert($this->connection);

        $this->dedicatedMentorTwo->consultant->personnel->insert($this->connection);
        $this->dedicatedMentorTwo->consultant->insert($this->connection);
        $this->dedicatedMentorTwo->insert($this->connection);

        $uri = $uri ?? $this->dedicatedMentorUri;
        $this->get($uri, $this->programParticipation->user->token);
    }

    public function test_showAll_200()
    {
        $this->executeShowAll();
        $this->seeStatusCode(200);

        $totalResponse = ['total' => 2];
        $this->seeJsonContains($totalResponse);

        $dedicatedMentorOneReponse = [
            'id' => $this->dedicatedMentorOne->id,
            'modifiedTime' => $this->dedicatedMentorOne->modifiedTime,
            'cancelled' => $this->dedicatedMentorOne->cancelled,
            'consultant' => [
                'id' => $this->dedicatedMentorOne->consultant->id,
                'personnel' => [
                    'id' => $this->dedicatedMentorOne->consultant->personnel->id,
                    'name' => $this->dedicatedMentorOne->consultant->personnel->getFullName(),
                ],
            ],
        ];
        $this->seeJsonContains($dedicatedMentorOneReponse);

        $dedicatedMentorTwoReponse = [
            'id' => $this->dedicatedMentorTwo->id,
            'modifiedTime' => $this->dedicatedMentorTwo->modifiedTime,
            'cancelled' => $this->dedicatedMentorTwo->cancelled,
            'consultant' => [
                'id' => $this->dedicatedMentorTwo->consultant->id,
                'personnel' => [
                    'id' => $this->dedicatedMentorTwo->consultant->personnel->id,
                    'name' => $this->dedicatedMentorTwo->consultant->personnel->getFullName(),
                ],
            ],
        ];
        $this->seeJsonContains($dedicatedMentorTwoReponse);
    }

    public function test_showAll_useCancelledStatusFilter()
    {
        $this->dedicatedMentorTwo->cancelled = true;
        $uri = $this->dedicatedMentorUri . "?cancelledStatus=false";

        $this->executeShowAll($uri);
        $this->seeStatusCode(200);

        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);

        $dedicatedMentorOneReponse = [
            'id' => $this->dedicatedMentorOne->id,
        ];
        $this->seeJsonContains($dedicatedMentorOneReponse);
    }

}
