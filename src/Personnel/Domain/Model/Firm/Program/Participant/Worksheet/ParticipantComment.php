<?php

namespace Personnel\Domain\Model\Firm\Program\Participant\Worksheet;

class ParticipantComment
{
    /**
     *
     * @var Worksheet
     */
    protected $worksheet;
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
    
    public function sendMail(\Resources\Application\Service\Mailer $mailer): void
    {
        
    }
}
