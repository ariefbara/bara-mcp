<?php

namespace Firm\Domain\Model\Firm\Program\Participant;

use Firm\Domain\Model\Firm\Program\Participant;
use Firm\Domain\Model\Firm\Program\ProgramsProfileForm;
use SharedContext\Domain\Model\SharedEntity\FormRecord;

class ParticipantProfile
{

    /**
     * 
     * @var Participant
     */
    protected $participant;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var ProgramsProfileForm
     */
    protected $programsProfileForm;

    /**
     * 
     * @var FormRecord
     */
    protected $formRecord;

    /**
     * 
     * @var bool
     */
    protected $removed;

    function __construct(
            Participant $participant, string $id, ProgramsProfileForm $programsProfileForm, FormRecord $formRecord)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->programsProfileForm = $programsProfileForm;
        $this->formRecord = $formRecord;
        $this->removed = false;
    }

}
