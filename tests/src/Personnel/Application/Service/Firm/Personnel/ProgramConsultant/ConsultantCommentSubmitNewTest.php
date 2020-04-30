<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\{
    Application\Service\Firm\Personnel\PersonnelCompositionId,
    Application\Service\Firm\Personnel\ProgramConsultantRepository,
    Application\Service\Firm\Program\Participant\WorksheetRepository,
    Domain\Model\Firm\Personnel\ProgramConsultant,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultantComment,
    Domain\Model\Firm\Program\Participant\Worksheet
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class ConsultantCommentSubmitNewTest extends TestBase
{

    protected $personnelCompositionId;
    protected $consultantCommentRepository, $nextIdentity = 'nextIdentity';
    protected $programConsultantRepository, $programConsultant, $programConsultantId = 'programConsultantId';
    protected $dispatcher;
    protected $service;
    protected $worksheetRepository, $worksheet, $participantId = 'participantId', $worksheetId = 'worksheetId';
    protected $message = 'message';

    protected function setUp(): void
    {
        parent::setUp();
        $this->personnelCompositionId = $this->buildMockOfClass(PersonnelCompositionId::class);
        $this->consultantCommentRepository = $this->buildMockOfInterface(ConsultantCommentRepository::class);
        $this->programConsultantRepository = $this->buildMockOfInterface(ProgramConsultantRepository::class);
        $this->programConsultant = $this->buildMockOfClass(ProgramConsultant::class);

        $this->worksheetRepository = $this->buildMockOfInterface(WorksheetRepository::class);
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new ConsultantCommentSubmitNew(
                $this->consultantCommentRepository, $this->programConsultantRepository, $this->worksheetRepository,
                $this->dispatcher);
    }
    
    protected function execute()
    {
        $this->consultantCommentRepository->expects($this->once())
                ->method('nextIdentity')
                ->willReturn($this->nextIdentity);
        $this->programConsultantRepository->expects($this->once())
                ->method('ofId')
                ->with($this->personnelCompositionId, $this->programConsultantId)
                ->willReturn($this->programConsultant);
        $this->worksheetRepository->expects($this->once())
                ->method('aWorksheetInProgramsWhereConsultantInvolved')
                ->with($this->personnelCompositionId, $this->programConsultantId, $this->participantId,
                        $this->worksheetId)
                ->willReturn($this->worksheet);

        
        $this->service->execute(
                $this->personnelCompositionId, $this->programConsultantId, $this->participantId, $this->worksheetId,
                $this->message);
    }

    public function test_execute_addConsultantCommentToRepository()
    {
        $comment = Worksheet\Comment::createNew($this->worksheet, $this->nextIdentity, $this->message);
        $consultantComment = new ConsultantComment($this->programConsultant, $this->nextIdentity, $comment);

        $this->consultantCommentRepository->expects($this->once())
                ->method('add')
                ->with($consultantComment);
        $this->execute();
    }
    public function test_execute_dispatchConsultantCommentToDispatcher()
    {
        $comment = Worksheet\Comment::createNew($this->worksheet, $this->nextIdentity, $this->message);
        $consultantComment = new ConsultantComment($this->programConsultant, $this->nextIdentity, $comment);
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($consultantComment);
        $this->execute();
    }

}
