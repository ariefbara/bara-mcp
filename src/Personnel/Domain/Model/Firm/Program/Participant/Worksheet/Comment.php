<?php

namespace Personnel\Domain\Model\Firm\Program\Participant\Worksheet;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Personnel\Domain\Model\Firm\ {
    Personnel\AssetBelongsToParticipantInProgram,
    Personnel\ProgramConsultant,
    Program\Participant\Worksheet,
    Program\Participant\Worksheet\Comment\CommentActivityLog
};
use Resources\ {
    DateTimeImmutableBuilder,
    Exception\RegularException,
    Uuid
};

class Comment implements AssetBelongsToParticipantInProgram
{

    /**
     *
     * @var Worksheet
     */
    protected $worksheet;

    /**
     *
     * @var Comment
     */
    protected $parent;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $message;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $submitTime;

    /**
     *
     * @var bool
     */
    protected $removed;

    /**
     *
     * @var ArrayCollection
     */
    protected $commentActivityLogs;

    public function __construct(Worksheet $worksheet, string $id, string $message)
    {
        $this->worksheet = $worksheet;
        $this->parent = null;
        $this->id = $id;
        $this->message = $message;
        $this->submitTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->removed = false;

        $this->commentActivityLogs = new ArrayCollection();
    }

    public function createReply(string $id, string $message): self
    {
        $reply = new static($this->worksheet, $id, $message);
        $reply->parent = $this;
        return $reply;
    }

    public function remove(): void
    {
        if ($this->isConsultantComment()) {
            $errorDetail = 'forbidden: unable to remove consultant comment';
            throw RegularException::forbidden($errorDetail);
        }
        $this->removed = true;
    }

    public function getWorksheetId(): string
    {
        return $this->worksheet->getId();
    }

    public function isConsultantComment(): bool
    {
        return !empty($this->consultantComment);
    }

    public function logActivity(ProgramConsultant $consultant): void
    {
        $id = Uuid::generateUuid4();
        $message = "comment submitted";
        $commentActivityLog = new CommentActivityLog($this, $id, $message, $consultant);
        $this->commentActivityLogs->add($commentActivityLog);
    }

    public function belongsToParticipantInProgram(string $programId): bool
    {
        return $this->worksheet->belongsToParticipantInProgram($programId);
    }

}
