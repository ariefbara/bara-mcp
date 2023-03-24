<?php

namespace Query\Domain\Model\Firm;

use DateTimeImmutable;
use Query\Domain\Model\Firm;
use Resources\Domain\ValueObject\Password;
use Resources\Exception\RegularException;

class Manager
{

    /**
     *
     * @var Firm
     */
    protected $firm;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var string
     */
    protected $email;

    /**
     *
     * @var Password
     */
    protected $password;

    /**
     *
     * @var string||null
     */
    protected $phone;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $joinTime;
    
    /**
     *
     * @var string|null
     */
    protected $resetPasswordCode;

    /**
     *
     * @var DateTimeImmutable|null
     */
    protected $resetPasswordCodeExpiredTime;

    /**
     *
     * @var bool
     */
    protected $removed = false;

    function getFirm(): Firm
    {
        return $this->firm;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->name;
    }

    function getEmail(): string
    {
        return $this->email;
    }

    function getPhone(): ?string
    {
        return $this->phone;
    }

    function getJoinTimeString(): string
    {
        return $this->joinTime->format("Y-m-d H:i:s");
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
        ;
    }

    public function passwordMatches(string $password): bool
    {
        return $this->password->match($password);
    }
    
    //
    protected function assertActive()
    {
        if ($this->removed) {
            throw RegularException::forbidden('only active manager can make this request');
        }
    }
    
    public function executeTaskInFirm(ITaskInFirmExecutableByManager $task): void
    {
        $this->assertActive();
        $task->executeTaskInFirm($this->firm);
    }
    
    public function executeTaskInProgram(Program $program, ITaskInProgramExecutableByManager $task): void
    {
        if (!$program->firmEquals($this->firm)) {
            throw RegularException::forbidden("forbidden: unable to manage program, probably belongs to other firm");
        }
        $task->executeInProgram($program);
    }
    
    public function executeQueryInFirm(ManagerQueryInFirm $query, $payload): void
    {
        $this->assertActive();
        $query->executeQueryInFirm($this->firm, $payload);
    }

}
