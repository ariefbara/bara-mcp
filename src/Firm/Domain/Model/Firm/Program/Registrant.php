<?php

namespace Firm\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Resources\ {
    DateTimeImmutableBuilder,
    Exception\RegularException
};

class Registrant
{

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $registeredTime;

    /**
     *
     * @var bool
     */
    protected $concluded;

    /**
     *
     * @var string||null
     */
    protected $note;

    public function isConcluded(): bool
    {
        return $this->concluded;
    }

    public function __construct(string $id)
    {
        $this->id = $id;
        $this->registeredTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->concluded = false;
        $this->note = null;
    }

    public function accept(): void
    {
        $this->assertUnconcluded();
        $this->concluded = true;
        $this->note = 'accepted';
    }

    public function reject(): void
    {
        $this->assertUnconcluded();
        $this->concluded = true;
        $this->note = 'rejected';
    }
    
    protected function assertUnconcluded(): void
    {
        if ($this->concluded) {
            $errorDetail = "forbidden: application already concluded";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
