<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Config\EventList;
use Doctrine\Common\Collections\ArrayCollection;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession\ConsultantFeedback;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession\ConsultationSessionActivityLog;
use Personnel\Domain\Model\Firm\Program\ConsultationSetup;
use Personnel\Domain\Model\Firm\Program\Participant;
use Resources\Domain\Event\CommonEvent;
use Resources\Domain\Model\EntityContainEvents;
use Resources\Domain\ValueObject\DateTimeInterval;
use Resources\Exception\RegularException;
use Resources\Uuid;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use SharedContext\Domain\ValueObject\ConsultationChannel;
use SharedContext\Domain\ValueObject\ConsultationSessionType;

class ConsultationSession extends EntityContainEvents
{

    /**
     *
     * @var ProgramConsultant
     */
    protected $programConsultant;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Participant
     */
    protected $participant;

    /**
     *
     * @var ConsultationSetup
     */
    protected $consultationSetup;

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
     * @var ConsultationSessionType
     */
    protected $sessionType;

    /**
     *
     * @var bool
     */
    protected $cancelled;

    /**
     *
     * @var ConsultantFeedback
     */
    protected $consultantFeedback = null;

    /**
     *
     * @var ArrayCollection
     */
    protected $consultationSessionActivityLogs;

    function __construct(
            ProgramConsultant $programConsultant, string $id, Participant $participant,
            ConsultationSetup $consultationSetup, DateTimeInterval $startEndTime, ConsultationChannel $channel,
            ConsultationSessionType $sessionType)
    {
        $this->programConsultant = $programConsultant;
        $this->id = $id;
        $this->participant = $participant;
        $this->consultationSetup = $consultationSetup;
        $this->startEndTime = $startEndTime;
        $this->channel = $channel;
        $this->sessionType = $sessionType;
        $this->cancelled = false;

        $this->consultationSessionActivityLogs = new ArrayCollection();
        $this->logActivity("Jadwal Konsultasi Disepakati");

        $event = new CommonEvent(EventList::CONSULTATION_SESSION_SCHEDULED_BY_CONSULTANT, $this->id);
        $this->recordEvent($event);
    }

    public function intersectWithConsultationRequest(ConsultationRequest $consultationRequest): bool
    {
        return !$this->cancelled && $consultationRequest->scheduleIntersectWith($this->startEndTime);
    }

    public function setConsultantFeedback(FormRecordData $formRecordData, ?int $participantRating): void
    {
        if ($this->cancelled) {
            $errorDetail = "forbidden: unable to submit report on cancelled session";
            throw RegularException::forbidden($errorDetail);
        }
        if (!empty($this->consultantFeedback)) {
            $this->consultantFeedback->update($formRecordData, $participantRating);
        } else {
            $id = Uuid::generateUuid4();
            $formRecord = $this->consultationSetup->createFormRecordForConsultantFeedback($id, $formRecordData);
            $this->consultantFeedback = new ConsultantFeedback($this, $id, $formRecord, $participantRating);
        }

        $this->logActivity("consultant report submitted");
    }

    protected function logActivity(string $message): void
    {
        $id = Uuid::generateUuid4();
        $consultationSessionActivityLog = new ConsultationSessionActivityLog(
                $this, $id, $message, $this->programConsultant);
        $this->consultationSessionActivityLogs->add($consultationSessionActivityLog);
    }

    public function cancel(): void
    {
        if (!$this->sessionType->canBeCancelled()) {
            throw RegularException::forbidden('forbidden: unable to cancel handsaking session');
        }
        $this->cancelled = true;
    }

    public function deny(): void
    {
        $this->sessionType = $this->sessionType->deny();
        $this->cancelled = true;
    }

    public function approve(): void
    {
        $this->sessionType = $this->sessionType->approve();
    }
    
    public function assertManageableByMentor(ProgramConsultant $mentor): void
    {
        if ($this->programConsultant !== $mentor || $this->cancelled) {
            throw RegularException::forbidden(
                    'forbidden: consultation session is unmanageable, either already cancelled or doesn\'t belongs to mentor');
        }
    }

}
