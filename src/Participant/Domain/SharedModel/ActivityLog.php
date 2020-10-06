<?php

namespace Participant\Domain\SharedModel;

use DateTimeImmutable;
use Participant\Domain\ {
    DependencyModel\Firm\Client\TeamMembership,
    SharedModel\ActivityLog\TeamMemberActivityLog
};
use Resources\DateTimeImmutableBuilder;

class ActivityLog
{

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $message;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $occuredTime;
    
    /**
     *
     * @var TeamMemberActivityLog|null
     */
    protected $teamMemberActivityLog;

    public function __construct(string $id, string $message)
    {
        $this->id = $id;
        $this->message = $message;
        $this->occuredTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
    }
    
    public function setOperator(TeamMembership $teamMember): void
    {
        $this->teamMemberActivityLog = new TeamMemberActivityLog($this, $this->id, $teamMember);
    }


}
