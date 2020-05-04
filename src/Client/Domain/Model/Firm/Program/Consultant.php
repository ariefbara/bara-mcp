<?php

namespace Client\Domain\Model\Firm\Program;

use Client\Domain\Model\{
    Client\ProgramParticipation\ConsultationRequest,
    Client\ProgramParticipation\ConsultationSession,
    Firm\Program
};
use Doctrine\Common\Collections\ArrayCollection;
use Query\Domain\Model\Firm\Personnel;

class Consultant
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
     * @var Personnel
     */
    protected $personnel;

    /**
     *
     * @var bool
     */
    protected $removed;

    /**
     *
     * @var ArrayCollection
     */
    protected $consultationSessions;

    protected function __construct()
    {
        ;
    }

    public function hasConsultationSessionConflictedWith(ConsultationRequest $consultationRequest): bool
    {
        $p = function (ConsultationSession $consultationSession) use ($consultationRequest) {
            return $consultationSession->conflictedWithConsultationRequest($consultationRequest);
        };
        return $this->consultationSessions->filter($p)->count();
    }

}
