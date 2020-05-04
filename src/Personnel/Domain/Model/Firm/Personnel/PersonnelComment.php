<?php

namespace Personnel\Domain\Model\Firm\Personnel;

use Personnel\Domain\Model\Firm\ {
    Personnel,
    Program\Participant\Worksheet\Comment
};

class PersonnelComment
{

    /**
     *
     * @var Personnel
     */
    protected $personnel;

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

    function __construct(Personnel $personnel, string $id, Comment $comment)
    {
        $this->personnel = $personnel;
        $this->id = $id;
        $this->comment = $comment;
    }

    public function remove(): void
    {
        $this->comment->remove();
    }

}
