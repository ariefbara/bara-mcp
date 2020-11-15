<?php

namespace ActivityCreator\Domain\Model\Activity\Invitee;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Personnel\Consultant,
    Model\Activity\Invitee,
    Model\CanReceiveInvitation
};

class ConsultantInvitee
{

    /**
     *
     * @var Invitee
     */
    protected $invitee;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Consultant
     */
    protected $consultant;

    function __construct(Invitee $invitee, string $id, Consultant $consultant)
    {
        $this->invitee = $invitee;
        $this->id = $id;
        $this->consultant = $consultant;
    }
    
    public function consultantEquals(CanReceiveInvitation $consultant): bool
    {
        return $this->consultant === $consultant;
    }

}
