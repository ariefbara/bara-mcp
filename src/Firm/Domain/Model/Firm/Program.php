<?php

namespace Firm\Domain\Model\Firm;

use Doctrine\Common\Collections\ {
    ArrayCollection,
    Criteria
};
use Firm\Domain\ {
    Event\ProgramManageParticipantEvent,
    Model\Firm,
    Model\Firm\Program\Consultant,
    Model\Firm\Program\Coordinator,
    Model\Firm\Program\Participant,
    Model\Firm\Program\Registrant
};
use Query\Domain\Model\Client;
use Resources\ {
    Domain\Model\ModelContainEvents,
    Exception\RegularException,
    Uuid,
    ValidationRule,
    ValidationService
};

class Program extends ModelContainEvents
{

    /**
     *
     * @var Firm
     */
    protected $firm;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var string
     */
    protected $description = null;

    /**
     *
     * @var bool
     */
    protected $published = false;

    /**
     *
     * @var bool
     */
    protected $removed = false;

    /**
     *
     * @var ArrayCollection
     */
    protected $coordinators;

    /**
     *
     * @var ArrayCollection
     */
    protected $consultants;

    /**
     *
     * @var ArrayCollection
     */
    protected $registrants;

    /**
     *
     * @var ArrayCollection
     */
    protected $participants;

    protected function setName(string $name)
    {
        $errorDetail = 'bad request: program name is required';
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, $errorDetail);
        $this->name = $name;
    }

    protected function setDescription(?string $description)
    {
        $this->description = $description;
    }

    function __construct(Firm $firm, $id, ProgramData $programData)
    {
        $this->firm = $firm;
        $this->id = $id;
        $this->setName($programData->getName());
        $this->setDescription($programData->getDescription());
        $this->published = false;
        $this->removed = false;
    }

    public function update(ProgramData $programData): void
    {
        $this->setName($programData->getName());
        $this->setDescription($programData->getDescription());
    }

    public function publish(): void
    {
        $this->published = true;
    }

    public function remove(): void
    {
        $this->removed = true;
    }

    public function assignPersonnelAsConsultant(Personnel $personnel): string
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('personnel', $personnel));
        $consultant = $this->consultants->matching($criteria)->first();

        if (!empty($consultant)) {
            if (!$consultant->isRemoved()) {
                $errorDetail = 'forbidden: personnel already assigned as consultant';
                throw RegularException::forbidden($errorDetail);
            } else {
                $consultant->reassign();
            }
        } else {
            $id = Uuid::generateUuid4();
            $consultant = new Consultant($this, $id, $personnel);
            $this->consultants->add($consultant);
        }
        return $consultant->getId();
    }

    public function assignPersonnelAsCoordinator(Personnel $personnel): string
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('personnel', $personnel));
        $coordinator = $this->coordinators->matching($criteria)->first();

        if (!empty($coordinator)) {
            if (!$coordinator->isRemoved()) {
                $errorDetail = "forbidden: personnel already assigned as coordinator";
                throw RegularException::forbidden($errorDetail);
            } else {
                $coordinator->reassign();
            }
        } else {
            $id = Uuid::generateUuid4();
            $coordinator = new Coordinator($this, $id, $personnel);
            $this->coordinators->add($coordinator);
        }
        return $coordinator->getId();
    }

    public function acceptRegistrant(string $registrantId): void
    {
        $registrant = $this->findRegistrantOrDie($registrantId);
        $registrant->accept();

        $client = $registrant->getClient();

        if (!empty($participant = $this->getParticipantOfClient($client))) {
            $participant->reActivate();
        } else {
            $id = Uuid::generateUuid4();
            $participant = new Participant($this, $id, $client);
            $this->participants->add($participant);
        }

        $messageForClient = "You have been accepted as participant of program $this->name";
        $event = new ProgramManageParticipantEvent($this->firm->getId(), $this->id, $participant->getId(), $messageForClient);
        $this->recordEvent($event);
    }

    protected function getParticipantOfClient(Client $client): ?Participant
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('client', $client));
        $participant = $this->participants->matching($criteria)->first();
        return empty($participant) ? null : $participant;
    }

    public function findRegistrantOrDie(string $registrantId): Registrant
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('id', $registrantId));
        $registrant = $this->registrants->matching($criteria)->first();
        if (empty($registrant)) {
            $errorDetail = 'not found: registrant not found';
            throw RegularException::notFound($errorDetail);
        }
        return $registrant;
    }

}
