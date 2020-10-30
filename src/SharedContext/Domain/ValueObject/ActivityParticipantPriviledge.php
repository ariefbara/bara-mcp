<?php

namespace SharedContext\Domain\ValueObject;

class ActivityParticipantPriviledge
{

    /**
     *
     * @var bool
     */
    protected $canInitiate;

    /**
     *
     * @var bool
     */
    protected $canAttend;

    function canInitiate(): bool
    {
        return $this->canInitiate;
    }

    function canAttend(): bool
    {
        return $this->canAttend;
    }

    function __construct(bool $canInitiate, bool $canAttend)
    {
        $this->canInitiate = $canInitiate;
        $this->canAttend = $canAttend;
    }

}
