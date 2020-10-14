<?php

namespace Participant\Domain\Model\Participant;

use Participant\Domain\{
    DependencyModel\Firm\Client\TeamMembership,
    Model\Participant,
    SharedModel\ActivityLog
};

class ViewLearningMaterialActivityLog
{

    /**
     *
     * @var Participant
     */
    protected $participant;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $learningMaterialId;

    /**
     *
     * @var ActivityLog
     */
    protected $activityLog;

    public function __construct(
            Participant $participant, string $id, string $learningMaterialId, ?TeamMembership $teamMember)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->learningMaterialId = $learningMaterialId;
        $message = "accessed learning material";
        $this->activityLog = new ActivityLog($id, $message, $teamMember);
    }

}
