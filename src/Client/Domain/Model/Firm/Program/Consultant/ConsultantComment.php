<?php

namespace Client\Domain\Model\Firm\Program\Consultant;

use Client\Domain\Model\{
    Client\ProgramParticipation\Worksheet\Comment,
    Firm\Program\Consultant
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
    
    public function __construct()
    {
        ;
    }

}
