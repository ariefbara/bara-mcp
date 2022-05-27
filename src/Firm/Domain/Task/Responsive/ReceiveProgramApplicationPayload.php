<?php

namespace Firm\Domain\Task\Responsive;

class ReceiveProgramApplicationPayload
{

    protected $programId;
    protected $applicantId;

    public function getProgramId()
    {
        return $this->programId;
    }

    public function getApplicantId()
    {
        return $this->applicantId;
    }

    public function __construct($programId, $applicantId)
    {
        $this->programId = $programId;
        $this->applicantId = $applicantId;
    }

}
