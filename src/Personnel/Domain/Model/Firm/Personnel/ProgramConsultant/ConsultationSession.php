<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Config\EventList;
use Doctrine\Common\Collections\ArrayCollection;
use Personnel\Domain\Model\Firm\ {
    Personnel\ProgramConsultant,
    Personnel\ProgramConsultant\ConsultationSession\ConsultantFeedback,
    Personnel\ProgramConsultant\ConsultationSession\ConsultationSessionActivityLog,
    Program\ConsultationSetup,
    Program\Participant
};
use Resources\ {
    Domain\Event\CommonEvent,
    Domain\Model\EntityContainEvents,
    Domain\ValueObject\DateTimeInterval,
    Uuid
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

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
            ConsultationSetup $consultationSetup, DateTimeInterval $startEndTime)
    {
        $this->programConsultant = $programConsultant;
        $this->id = $id;
        $this->participant = $participant;
        $this->consultationSetup = $consultationSetup;
        $this->startEndTime = $startEndTime;
        
        $this->consultationSessionActivityLogs = new ArrayCollection();
        $this->logActivity("Jadwal Konsultasi Disepakati");
        
        $event = new CommonEvent(EventList::CONSULTATION_SESSION_SCHEDULED_BY_CONSULTANT, $this->id);
        $this->recordEvent($event);
    }

    public function intersectWithConsultationRequest(ConsultationRequest $consultationRequest): bool
    {
        return $consultationRequest->scheduleIntersectWith($this->startEndTime);
    }

    public function setConsultantFeedback(FormRecordData $formRecordData): void
    {
        if (!empty($this->consultantFeedback)) {
            $this->consultantFeedback->update($formRecordData);
        } else {
            $id = Uuid::generateUuid4();
            $formRecord = $this->consultationSetup->createFormRecordForConsultantFeedback($id, $formRecordData);
            $this->consultantFeedback = new ConsultantFeedback($this, $id, $formRecord);
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

}
