<?php

namespace Client\Application\Listener;

use Tests\TestBase;

class ConsultantCommentOnWorksheetListenerTest extends TestBase
{

    protected $clientNotificationRepository;
    protected $commentRepository;
    protected $listener;
    protected $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientNotificationRepository = $this->buildMockOfInterface(ClientNotificationRepository::class);
        $this->commentRepository = $this->buildMockOfInterface(CommentRepository::class);
        $this->listener = new ConsultantCommentOnWorksheetListener(
                $this->commentRepository, $this->clientNotificationRepository);
        
        $this->event = $this->buildMockOfInterface(ConsultantCommentOnWorsheetEventInterface::class);
    }
    public function test_handle_addClientNotificationCreatedInCommentToRepository()
    {
        $this->commentRepository->expects($this->once())
                ->method('aCommentFromConsultant');
        $this->clientNotificationRepository->expects($this->once())
                ->method('add');
        $this->listener->handle($this->event);
    }

}
