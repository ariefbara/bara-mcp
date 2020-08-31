<?php

namespace Participant\Domain\Model\DependencyEntity\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\Model\Participant\ {
    ConsultationRequest,
    ConsultationSession
};

class Consultant
{

    /**
     *
     * @var string
     */
    protected $programId;

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

    public function programIdEquals(string $programId): bool
    {
        return $this->programId === $programId;
    }

    public function hasConsultationSessionConflictedWith(ConsultationRequest $consultationRequest): bool
    {
        $p = function (ConsultationSession $consultationSession) use ($consultationRequest) {
            return $consultationSession->conflictedWithConsultationRequest($consultationRequest);
        };
        return $this->consultationSessions->filter($p)->count();
    }

}
