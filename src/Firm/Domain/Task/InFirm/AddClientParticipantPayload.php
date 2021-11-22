<?php

namespace Firm\Domain\Task\InFirm;

class AddClientParticipantPayload
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
    protected $programId;

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function getProgramId(): ?string
    {
        return $this->programId;
    }

    public function __construct(?string $clientId, ?string $programId)
    {
        $this->clientId = $clientId;
        $this->programId = $programId;
    }

}
