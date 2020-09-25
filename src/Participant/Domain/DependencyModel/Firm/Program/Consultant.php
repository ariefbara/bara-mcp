<?php

namespace Participant\Domain\DependencyModel\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\ {
    DependencyModel\Firm\Program,
    Model\Participant\ConsultationRequest,
    Model\Participant\ConsultationSession
};

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
     * @var string
     */
    protected $personnelId;

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

    public function programEquals(Program $program): bool
    {
        return $this->program === $program;
    }

    public function hasConsultationSessionConflictedWith(ConsultationRequest $consultationRequest): bool
    {
        $p = function (ConsultationSession $consultationSession) use ($consultationRequest) {
            return $consultationSession->conflictedWithConsultationRequest($consultationRequest);
        };
        return $this->consultationSessions->filter($p)->count();
    }

}
