<?php

namespace Notification\Domain\Model\Firm\Program\Consultant;

use Notification\Domain\Model\Firm\Program\ {
    Consultant,
    Participant\Worksheet\Comment
};
use Resources\Domain\Model\Mail\Recipient;

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

    public function getConsultantMailRecipient(): Recipient
    {
        return $this->consultant->getPersonnelMailRecipient();
    }

}
