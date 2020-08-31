<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\Application\Service\Client\ProgramParticipationRepository;
use Tests\TestBase;

class ParticipantCommentSubmitNewTest extends TestBase
{

    protected $service;
    protected $programParticipationCompositionId;
    protected $participantCommentRepository;
    protected $programParticipationRepository;
    protected $worksheetRepository, $worksheetId = 'worksheetId';
    protected $message = 'comment';

    protected function setUp(): void
    {
        parent::setUp();
        $this->programParticipationCompositionId = new ProgramParticipationCompositionId('clientId', 'participantId');
        $this->participantCommentRepository = $this->buildMockOfInterface(ParticipantCommentRepository::class);
        $this->programParticipationRepository = $this->buildMockOfInterface(ProgramParticipationRepository::class);
        $this->worksheetRepository = $this->buildMockOfInterface(WorksheetRepository::class);

        $this->service = new ParticipantCommentSubmitNew(
                $this->participantCommentRepository, $this->programParticipationRepository, $this->worksheetRepository);
    }

    public function execute()
    {
        return $this->service->execute($this->programParticipationCompositionId, $this->worksheetId, $this->message);
    }

    public function test_addParticipantCommentToRepository()
    {
        $this->participantCommentRepository->expects($this->once())
                ->method('add');
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->participantCommentRepository->expects($this->once())
                ->method('nextIdentity')
                ->willReturn($id = 'id');
        $this->assertEquals($id, $this->execute());
    }

}
