<?php

namespace Personnel\Domain\Model\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\{
    ConsultationRequest,
    ConsultationSession
};

class Participant
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
     * @var bool
     */
    protected $active = true;

    /**
     *
     * @var ArrayCollection
     */
    protected $consultationSessions;

    protected function __construct()
    {
        
    }
    
    public function programEquals(string $programId): bool
    {
        return $this->programId === $programId;
    }

    public function hasConsultationSessionInConflictWithConsultationRequest(ConsultationRequest $consultationRequest): bool
    {
        $p = function (ConsultationSession $consultationSession) use ($consultationRequest) {
            return $consultationSession->intersectWithConsultationRequest($consultationRequest);
        };
        return !empty($this->consultationSessions->filter($p)->count());
    }

}
