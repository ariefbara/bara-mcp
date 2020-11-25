<?php

namespace Query\Domain\Model\Firm\Program\Participant\Worksheet\Comment;

use Query\Domain\{
    Model\Firm\Program\Participant\Worksheet\Comment,
    SharedModel\Mail
};

class CommentMail
{

    /**
     *
     * @var Comment
     */
    protected $comment;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Mail
     */
    protected $mail;

    function __construct()
    {
        
    }

}
