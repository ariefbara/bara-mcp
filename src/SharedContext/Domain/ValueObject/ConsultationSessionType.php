<?php

namespace SharedContext\Domain\ValueObject;

use ReflectionClass;
use Resources\Exception\RegularException;

class ConsultationSessionType
{

    public const HANDSHAKING_TYPE = 0;
    public const DECLARED_TYPE = 1;

    /**
     * 
     * @var int
     */
    protected $sessionType;

    /**
     * 
     * @var bool|null
     */
    protected $approvedByMentor;

    public function getSessionTypeDisplayValue(): string
    {
        return $this->sessionType === self::DECLARED_TYPE ? 'DECLARED' : 'HANDSHAKING';
    }

    public function isApprovedByMentor(): ?bool
    {
        return $this->approvedByMentor;
    }

    public function __construct(int $sessionType, ?bool $approvedByMentor = null)
    {
        $c = new ReflectionClass($this);
        if (!in_array($sessionType, $c->getConstants())) {
            $path = explode('\\', static::class);
            $className = array_pop($path);
            throw RegularException::badRequest("bad request: invalid consultation session type argument");
        }

        $this->sessionType = $sessionType;
        $this->approvedByMentor = $approvedByMentor;
    }

    public function canBeCancelled(): bool
    {
        return $this->sessionType === $this::DECLARED_TYPE;
    }

    public function deny(): self
    {
        $this->assertCanBeReponded();
        return new static($this->sessionType, false);
    }

    public function approve(): self
    {
        $this->assertCanBeReponded();
        return new static($this->sessionType, true);
    }

    protected function assertCanBeReponded(): void
    {
        if ($this->sessionType !== $this::DECLARED_TYPE || isset($this->approvedByMentor)) {
            throw RegularException::forbidden('forbidden: unable to deny session (either session is non declare type or already approved/denied)');
        }
    }

}
