<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\ {
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

    protected $consultantCommentRepository, $nextIdentity = 'nextIdentity';
    protected $programConsultantRepository, $programConsultant;
    protected $dispatcher;
    protected $service;
    protected $worksheetRepository, $worksheet, $participantId = 'participantId', $worksheetId = 'worksheetId';
    
    protected $firmId = 'firmId', $personnelId = 'personnelId', $programConsultationId = 'programConsultationId'; 
    protected $message = 'message';

    protected function setUp(): void
    {
        parent::setUp();
        $this->programConsultant = $this->buildMockOfClass(ProgramConsultant::class);
        $this->programConsultantRepository = $this->buildMockOfInterface(ProgramConsultantRepository::class);
        $this->programConsultantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->personnelId, $this->programConsultationId)
                ->willReturn($this->programConsultant);
        
        $this->consultantCommentRepository = $this->buildMockOfInterface(ConsultantCommentRepository::class);
        $this->consultantCommentRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextIdentity);

        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        $this->worksheetRepository = $this->buildMockOfInterface(WorksheetRepository::class);
        $this->worksheetRepository->expects($this->any())
                ->method('aWorksheetInProgramsWhereConsultantInvolved')
                ->with($this->firmId, $this->personnelId, $this->programConsultationId, $this->participantId, $this->worksheetId)
                ->willReturn($this->worksheet);
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new ConsultantCommentSubmitNew(
                $this->consultantCommentRepository, $this->programConsultantRepository, $this->worksheetRepository,
                $this->dispatcher);
    }
    
    protected function execute()
    {
        return $this->service->execute(
                $this->firmId, $this->personnelId, $this->programConsultationId, $this->participantId, $this->worksheetId,
                $this->message);
    }

    public function test_execute_addConsultantCommentToRepository()
    {
        $this->programConsultant->expects($this->once())
                ->method('submitNewCommentOnWorksheet')
                ->with($this->nextIdentity, $this->worksheet, $this->message);

        $this->consultantCommentRepository->expects($this->once())
                ->method('add');
        $this->execute();
    }
    public function test_execute_dispatchConsultantCommentToDispatcher()
    {
        $this->programConsultant->expects($this->once())
                ->method('submitNewCommentOnWorksheet')
                ->willReturn($consultantComment = $this->buildMockOfClass(ConsultantComment::class));
        
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($consultantComment);
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextIdentity, $this->execute());
    }

}
