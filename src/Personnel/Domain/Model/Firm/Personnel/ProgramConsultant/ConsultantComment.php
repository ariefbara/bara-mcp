<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Personnel\Domain\Model\Firm\ {
    Personnel\ProgramConsultant,
    Program\Participant\Worksheet\Comment
};

class ConsultantComment
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
    }

    public function remove(): void
    {
        $this->comment->remove();
    }

}
