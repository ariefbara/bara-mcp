<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Personnel\Domain\{
    Event\ConsultantCommentOnWorksheetEvent,
    Model\Firm\Personnel\ProgramConsultant,
    Model\Firm\Program\Participant\Worksheet,
    Model\Firm\Program\Participant\Worksheet\Comment
};
use Resources\Domain\Model\ModelContainEvents;

class ConsultantComment extends ModelContainEvents
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

    function getProgramConsultant(): ProgramConsultant
    {
        return $this->programConsultant;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getWorksheet(): Worksheet
    {
        return $this->comment->getWorksheet();
    }

    function getParent(): ?Comment
    {
        return $this->comment->getParent();
    }

    function getMessage(): ?string
    {
        return $this->comment->getMessage();
    }

    function getSubmitTimeString(): string
    {
        return $this->comment->getSubmitTimeString();
    }

    function isRemoved(): bool
    {
        return $this->comment->isRemoved();
    }

    function __construct(ProgramConsultant $programConsultant, string $id, Comment $comment)
    {
        $this->programConsultant = $programConsultant;
        $this->id = $id;
        $this->comment = $comment;

        $firmId = $this->programConsultant->getPersonnel()->getFirm()->getId();
        $personnelId = $this->programConsultant->getPersonnel()->getId();
        $consultantId = $this->programConsultant->getId();
        $messageForParticipant = "consultant {$this->programConsultant->getPersonnel()->getName()} has commented on your worksheet";
        
        $event = new ConsultantCommentOnWorksheetEvent(
                $firmId, $personnelId, $consultantId, $this->id, $messageForParticipant);
        $this->recordEvent($event);
    }

    public function remove(): void
    {
        $this->comment->remove();
    }

}
