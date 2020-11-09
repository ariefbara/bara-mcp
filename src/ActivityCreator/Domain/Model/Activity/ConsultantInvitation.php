<?php

namespace ActivityCreator\Domain\Model\Activity;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Personnel\Consultant,
    service\ActivityDataProvider
};

class ConsultantInvitation
{

    /**
     *
     * @var Invitation
     */
    protected $invitation;

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

    function __construct(Invitation $invitation, string $id, Consultant $consultant)
    {
        $this->invitation = $invitation;
        $this->id = $id;
        $this->consultant = $consultant;
    }
    
    public function removeIfNotApprearInList(ActivityDataProvider $activityDataProvider): void
    {
        if (!$activityDataProvider->containConsultant($this->consultant)) {
            $this->invitation->remove();
        }
    }
    
    public function consultantEquals(Consultant $consultant): bool
    {
        return $this->consultant === $consultant;
    }

}
