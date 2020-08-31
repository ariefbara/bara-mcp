<?php

namespace Participant\Domain\Model\DependencyEntity\Firm\Program\Consultant;

use Participant\Domain\Model\ {
    DependencyEntity\Firm\Program\Consultant,
    Participant\Worksheet\Comment
};

class ConsultantComment
{

    /**
     *
     * @var Consultant
     */
    protected $consultant;

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

    protected function __construct()
    {
        ;
    }

    public function createReply(string $commentId, string $message): Comment
    {
        return $this->comment->createReply($commentId, $message);
    }

}
