<?php

namespace Personnel\Domain\Model\Firm\Personnel;

use DateTimeImmutable;
use Personnel\Domain\Model\Firm\{
    Personnel,
    Personnel\ProgramConsultant\ConsultationRequest,
    Personnel\ProgramConsultant\ConsultationSession
};
use Resources\DateTimeImmutableBuilder;

class PersonnelNotification
{

    /**
     *
     * @var Personnel
     */
    protected $personnel;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $message;

    /**
     *
     * @var bool
     */
    protected $read = false;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $notifiedTime;

    /**
     *
     * @var ConsultationRequest||null
     */
    protected $consultationRequest = null;

    /**
     *
     * @var ConsultationSession||null
     */
    protected $consultationSession = null;

    protected function __construct(Personnel $personnel, string $id, string $message)
    {
        $this->personnel = $personnel;
        $this->id = $id;
        $this->message = $message;
        $this->read = false;
        $this->notifiedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
    }

    public static function notificationForConsultationRequest(
            Personnel $personnel, string $id, string $message, ConsultationRequest $consultationRequest): self
    {
        $notification = new static($personnel, $id, $message);
        $notification->consultationRequest = $consultationRequest;
        return $notification;
    }

    public static function notificationForConsultationSession(
            Personnel $personnel, string $id, string $message, ConsultationSession $consultationSession)
    {
        $notification = new static($personnel, $id, $message);
        $notification->consultationSession = $consultationSession;
        return $notification;
    }

    public function read(): void
    {
        $this->read = true;
    }

}
