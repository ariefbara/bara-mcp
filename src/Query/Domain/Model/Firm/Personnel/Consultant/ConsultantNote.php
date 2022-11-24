<?php

namespace Query\Domain\Model\Firm\Personnel\Consultant;

use DateTimeImmutable;
use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\SharedModel\Note;

class ConsultantNote
{

    /**
     * 
     * @var Consultant
     */
    protected $consultant;

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
     * @var Note
     */
    protected $note;

    /**
     * 
     * @var bool
     */
    protected $viewableByParticipant;

    protected function __construct()
    {
        
    }

    public function getConsultant(): Consultant
    {
        return $this->consultant;
    }

    public function getParticipant(): Participant
    {
        return $this->participant;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getNote(): Note
    {
        return $this->note;
    }

    public function isViewableByParticipant(): bool
    {
        return $this->viewableByParticipant;
    }

}
