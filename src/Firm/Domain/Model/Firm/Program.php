<?php

namespace Firm\Domain\Model\Firm;

use Doctrine\Common\Collections\{
    ArrayCollection,
    Criteria
};
use Firm\Domain\Model\{
    Client,
    Firm,
    Firm\Program\Coordinator,
    Firm\Program\Mentor,
    Firm\Program\Participant,
    Firm\Program\Registrant
};
use Resources\{
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
    protected $mentors;

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

    function getFirm(): Firm
    {
        return $this->firm;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->name;
    }

    function getDescription(): ?string
    {
        return $this->description;
    }

    function isPublished(): bool
    {
        return $this->published;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

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

    public function assignPersonnelAsMentor(Personnel $personnel): Mentor
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('personnel', $personnel));
        $mentor = $this->mentors->matching($criteria)->first();

        if (!empty($mentor)) {
            if (!$mentor->isRemoved()) {
                $errorDetail = 'forbidden: personnel already assigned as mentor';
                throw RegularException::forbidden($errorDetail);
            } else {
                $mentor->reassign();
            }
        } else {
            $id = Uuid::generateUuid4();
            $mentor = new Mentor($this, $id, $personnel);
            $this->mentors->add($mentor);
        }
        return $mentor;
    }

    public function assignPersonnelAsCoordinator(Personnel $personnel): Coordinator
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
        return $coordinator;
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

        $clientId = $client->getId();
        $participantId = $participant->getId();
        $message = "Your have been accepted as participant of program $this->name";

        $event = new \Firm\Domain\Event\ParticipantAcceptedEvent($clientId, $participantId, $message);
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
