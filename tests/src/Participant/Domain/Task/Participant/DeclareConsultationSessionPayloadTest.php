<?php

namespace Participant\Domain\Task\Participant;

use DateTimeImmutable;
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\ValueObject\ConsultationChannel;
use Tests\TestBase;

class DeclareConsultationSessionPayloadTest extends TestBase
{

    protected $consultationSetupId = 'consultationSetupId';
    protected $mentorId = 'mentorId';
    protected $startTime;
    protected $endTime;
    protected $media = 'new media';
    protected $address = 'new location';
    protected $payload;

    protected function setUp(): void
    {
        parent::setUp();
        $this->startTime = new DateTimeImmutable('+24 hours');
        $this->endTime = new DateTimeImmutable('+25 hours');
        $this->payload = new TestableDeclareConsultationSessionPayload(
                $this->consultationSetupId, $this->mentorId, $this->startTime, $this->endTime, $this->media,
                $this->address);
    }

    protected function getStartEndTime()
    {
        return $this->payload->getStartEndTime();
    }

    public function test_getStartEndTime_returnStartEndTimeIntervale()
    {
        $startEndTime = new DateTimeInterval($this->startTime, $this->endTime);
        $this->assertEquals($startEndTime, $this->getStartEndTime());
    }
    
    protected function getConsultationChannel()
    {
        return $this->payload->getConsultationChannel();
    }
    public function test_getConsultationChannel_returnChannel()
    {
        $channel = new ConsultationChannel($this->media, $this->address);
        $this->assertEquals($channel, $this->getConsultationChannel());
    }

}

class TestableDeclareConsultationSessionPayload extends DeclareConsultationSessionPayload
{

    public $consultationSetupId;
    public $mentorId;
    public $startTime;
    public $endTime;
    public $media;
    public $address;

}
