<?php

namespace Participant\Domain\Task\Participant;

use Participant\Domain\Model\Participant\MentoringRequestData;

class RequestMentoringPayload
{

    /**
     * 
     * @var string|null
     */
    protected $mentorId;

    /**
     * 
     * @var string|null
     */
    protected $consultationSetupId;

    /**
     * 
     * @var MentoringRequestData|null
     */
    protected $mentoringRequestData;

    public function getMentorId(): ?string
    {
        return $this->mentorId;
    }

    public function getConsultationSetupId(): ?string
    {
        return $this->consultationSetupId;
    }

    public function getMentoringRequestData(): ?MentoringRequestData
    {
        return $this->mentoringRequestData;
    }

    public function __construct(
            ?string $mentorId, ?string $consultationSetupId, ?MentoringRequestData $mentoringRequestData)
    {
        $this->mentorId = $mentorId;
        $this->consultationSetupId = $consultationSetupId;
        $this->mentoringRequestData = $mentoringRequestData;
    }

}
