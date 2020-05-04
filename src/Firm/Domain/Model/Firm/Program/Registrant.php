<?php

namespace Firm\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Firm\Domain\Model\Firm\Program;
use Query\Domain\Model\Client;
use Resources\Exception\RegularException;

class Registrant
{

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Client
     */
    protected $client;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $appliedTime;

    /**
     *
     * @var bool
     */
    protected $concluded;

    /**
     *
     * @var string
     */
    protected $note = null;

    function getId(): string
    {
        return $this->id;
    }

    function getClient(): Client
    {
        return $this->client;
    }

    protected function __construct()
    {
        ;
    }

    public function accept(): void
    {
        $this->assertNotConcluded();
        $this->concluded = true;
        $this->note = 'accepted';
    }

    public function reject(): void
    {
        $this->assertNotConcluded();
        $this->concluded = true;
        $this->note = 'rejected';
    }

    protected function assertNotConcluded(): void
    {
        if ($this->concluded) {
            $errorDetail = 'forbidden: application already concluded';
            throw RegularException::forbidden($errorDetail);
        }
    }

}
