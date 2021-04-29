<?php

namespace Firm\Domain\Model\Firm\Program\Mission;

class MissionCommentData
{

    /**
     * 
     * @var string|null
     */
    protected $message;

    /**
     * 
     * @var array
     */
    protected $rolePaths;

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getRolePaths(): array
    {
        return $this->rolePaths;
    }

    public function __construct(?string $message)
    {
        $this->message = $message;
        $this->rolePaths = [];
    }
    
    public function addRolePath(string $entityName, string $entityId): void
    {
        $this->rolePaths[$entityName] = $entityId;
    }

}
