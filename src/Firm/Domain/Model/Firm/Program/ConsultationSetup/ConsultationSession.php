<?php

namespace Firm\Domain\Model\Firm\Program\ConsultationSetup;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\AssetInProgram;
use Firm\Domain\Model\Firm\Program\Consultant;
use Firm\Domain\Model\Firm\Program\ConsultationSetup;
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\ValueObject\ConsultationChannel;

class ConsultationSession implements AssetInProgram
{

    /**
     *
     * @var ConsultationSetup
     */
    protected $consultationSetup;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Consultant
     */
    protected $consultant;

    /**
     *
     * @var DateTimeInterval
     */
    protected $startEndTime;
    
    /**
     * 
     * @var ConsultationChannel
     */
    protected $channel;

    /**
     *
     * @var bool
     */
    protected $cancelled;

    /**
     *
     * @var string|null
     */
    protected $note;

    protected function __construct()
    {
        
    }

    public function disableUpcomingSession(): void
    {
        if (!$this->cancelled && $this->startEndTime->isUpcoming()) {
            $this->cancelled = true;
            $this->note = "inactive consultant";
        }
    }
    
    public function changeChannel(?string $media, ?string $address): void
    {
        $this->channel = new ConsultationChannel($media, $address);
    }

    public function belongsToProgram(Program $program): bool
    {
        return $this->consultationSetup->belongsToProgram($program);
    }

}
