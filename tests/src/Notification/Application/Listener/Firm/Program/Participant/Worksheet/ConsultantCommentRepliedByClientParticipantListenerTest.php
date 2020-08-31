<?php

namespace Notification\Application\Listener\Firm\Program\Participant\Worksheet;

use Notification\Application\Service\Firm\Program\Participant\Worksheet\SendClientParticipantRepliedConsultantCommentMail;
use Tests\TestBase;

class ConsultantCommentRepliedByClientParticipantListenerTest extends TestBase
{

    protected $listener;
    protected $sendClientParticipantRepliedConsultantCommentMail;
    
    protected $event;
    protected $firmId = 'firmId', $clientId = 'clientId', $programParticipationId = 'programParticipationId', $worksheetId = 'worksheetId',
            $commentId = 'commentId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->sendClientParticipantRepliedConsultantCommentMail = $this->buildMockOfClass(SendClientParticipantRepliedConsultantCommentMail::class);
        $this->listener = new ConsultantCommentRepliedByClientParticipantListener($this->sendClientParticipantRepliedConsultantCommentMail);

        $this->event = $this->buildMockOfInterface(ConsultantCommentRepliedByClientParticipantEventInterface::class);
        $this->event->expects($this->once())->method('getFirmId')->willReturn($this->firmId);
        $this->event->expects($this->once())->method('getClientId')->willReturn($this->clientId);
        $this->event->expects($this->once())->method('getProgramParticipationId')->willReturn($this->programParticipationId);
        $this->event->expects($this->once())->method('getWorksheetId')->willReturn($this->worksheetId);
        $this->event->expects($this->once())->method('getCommentId')->willReturn($this->commentId);
    }
    
    public function test_handle_executeService()
    {
        $this->sendClientParticipantRepliedConsultantCommentMail->expects($this->once())
                ->method('execute')
                ->with($this->firmId, $this->clientId, $this->programParticipationId, $this->worksheetId, $this->commentId);
        $this->listener->handle($this->event);
    }

}
