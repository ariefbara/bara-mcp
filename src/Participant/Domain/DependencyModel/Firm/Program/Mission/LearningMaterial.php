<?php

namespace Participant\Domain\DependencyModel\Firm\Program\Mission;

use Participant\Domain\DependencyModel\Firm\Program\Mission;
use Participant\Domain\Model\Participant;
use Resources\Exception\RegularException;

class LearningMaterial
{

    /**
     * 
     * @var Mission
     */
    protected $mission;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var bool
     */
    protected $removed;

    protected function __construct()
    {
        
    }

    //
    public function assertAccessibleByParticipant(Participant $participant): void
    {
        if ($this->removed || !$this->mission->isSameProgramAsParticipant($participant)) {
            throw RegularException::forbidden('inaccessible learning material');
        }
    }

}
