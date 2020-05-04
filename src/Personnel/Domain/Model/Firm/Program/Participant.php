<?php

namespace Personnel\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ {
    ConsultationRequest,
    ConsultationSession
};
use Query\Domain\Model\ {
    Client,
    Firm\Program
};

class Participant
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
    protected $acceptedTime;

    /**
     *
     * @var bool
     */
    protected $active = true;

    /**
     *
     * @var string
     */
    protected $note;

    /**
     *
     * @var ArrayCollection
     */
    protected $consultationSessions;

    protected function __construct()
    {
        ;
    }

    public function hasConsultationSessionInConflictWithConsultationRequest(ConsultationRequest $consultationRequest): bool
    {
        $p = function (ConsultationSession $consultationSession) use ($consultationRequest) {
            return $consultationSession->intersectWithConsultationRequest($consultationRequest);
        };
        return !empty($this->consultationSessions->filter($p)->count());
    }

}
