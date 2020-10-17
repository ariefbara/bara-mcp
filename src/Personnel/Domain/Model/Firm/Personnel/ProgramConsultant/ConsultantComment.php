<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Config\EventList;
use Personnel\Domain\Model\Firm\ {
    Personnel\ProgramConsultant,
    Program\Participant\Worksheet\Comment
};
use Resources\Domain\ {
    Event\CommonEvent,
    Model\EntityContainEvents
};

class ConsultantComment extends EntityContainEvents
{

    /**
     *
     * @var ProgramConsultant
     */
    protected $programConsultant;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Comment
     */
    protected $comment;

    function __construct(ProgramConsultant $programConsultant, string $id, Comment $comment)
    {
        $this->programConsultant = $programConsultant;
        $this->id = $id;
        $this->comment = $comment;
        
        $this->comment->logActivity($this->programConsultant);
        
        $event = new CommonEvent(EventList::COMMENT_SUBMITTED_BY_CONSULTANT, $this->id);
        $this->recordEvent($event);
    }

    public function remove(): void
    {
        $this->comment->remove();
    }

}
