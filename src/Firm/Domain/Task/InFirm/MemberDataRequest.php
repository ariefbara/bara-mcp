<?php

namespace Firm\Domain\Task\InFirm;

class MemberDataRequest
{

    /**
     * 
     * @var string|null
     */
    protected $clientId;

    /**
     * 
     * @var string|null
     */
    protected $position;

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function __construct(?string $clientId, ?string $position)
    {
        $this->clientId = $clientId;
        $this->position = $position;
    }

}
