<?php

namespace Firm\Domain\Model\Firm;

use Doctrine\Common\Collections\ {
    ArrayCollection,
    Criteria
};
use Firm\Domain\ {
    Event\Firm\Program\ClientRegistrationAccepted,
    Event\Firm\Program\UserRegistrationAccepted,
    Model\Firm,
    Model\Firm\Program\ClientParticipant,
    Model\Firm\Program\ClientRegistrant,
    Model\Firm\Program\Consultant,
    Model\Firm\Program\Coordinator,
    Model\Firm\Program\UserParticipant,
    Model\Firm\Program\UserRegistrant
};
use Query\Domain\Model\ {
    Firm\ParticipantTypes,
    FirmWhitelableInfo
};
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
     * @var ParticipantTypes
     */
    protected $participantTypes;

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
    protected $clientRegistrants;

    /**
     *
     * @var ArrayCollection
     */
    protected $clientParticipants;

    /**
     *
     * @var ArrayCollection
     */
    protected $userRegistrants;

    /**
     *
     * @var ArrayCollection
     */
    protected $userParticipants;

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
    
    public function getFirmWhitelableInfo(): FirmWhitelableInfo
    {
        return $this->firm->getWhitelableInfo();
    }

    public function getFirmName(): string
    {
        return $this->firm->getName();
    }
    
    public function getFirmId(): string
    {
        return $this->firm->getId();
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
        $this->participantTypes = new ParticipantTypes($programData->getParticipantTypes());
        $this->published = false;
        $this->removed = false;
    }

    public function update(ProgramData $programData): void
    {
        $this->setName($programData->getName());
        $this->setDescription($programData->getDescription());
        $this->participantTypes = new ParticipantTypes($programData->getParticipantTypes());
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

    public function acceptClientRegistration(string $clientRegistrationId): void
    {
        $clientRegistrant = $this->findClientRegistrantOrDie($clientRegistrationId);
        $clientRegistrant->accept();

        if (!empty($clientParticipant = $this->findClientParticipantCorrespondWithRegistrant($clientRegistrant))) {
            $clientParticipant->reenroll();
        } else {
            $clientParticipantId = Uuid::generateUuid4();
            $this->clientParticipants->add($clientRegistrant->createParticipant($clientParticipantId));
        }
        
        $clientId = $clientRegistrant->getClientId();
        $event = new ClientRegistrationAccepted($this->firm->getId(), $this->id, $clientId);
        $this->recordEvent($event);
    }

    public function acceptUserRegistration(string $userRegistrationId): void
    {
        $userRegistrant = $this->findUserRegistrantOrDie($userRegistrationId);
        $userRegistrant->accept();

        if (!empty($userParticipant = $this->findUserParticipantCorrespondWithRegistrant($userRegistrant))) {
            $userParticipant->reenroll();
        } else {
            $userRegistrationId = Uuid::generateUuid4();
            $this->userParticipants->add($userRegistrant->createParticipant($userRegistrationId));
        }
        
        $userId = $userRegistrant->getUserId();
        $event = new UserRegistrationAccepted($this->firm->getId(), $this->id, $userId);
        $this->recordEvent($event);
    }

    protected function findClientRegistrantOrDie(string $clientRegistrantId): ClientRegistrant
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('id', $clientRegistrantId));
        $clientRegistrant = $this->clientRegistrants->matching($criteria)->first();
        if (empty($clientRegistrant)) {
            $errorDetail = 'not found: client registrant not found';
            throw RegularException::notFound($errorDetail);
        }
        return $clientRegistrant;
    }

    protected function findClientParticipantCorrespondWithRegistrant(ClientRegistrant $clientRegistrant): ?ClientParticipant
    {
        $p = function (ClientParticipant $clientParticipant) use ($clientRegistrant) {
            return $clientParticipant->correspondWithRegistrant($clientRegistrant);
        };
        $clientParticipant = $this->clientParticipants->filter($p)->first();
        return empty($clientParticipant) ? null : $clientParticipant;
    }

    protected function findUserRegistrantOrDie(string $userRegistrantId): UserRegistrant
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('id', $userRegistrantId));
        $userRegistrant = $this->userRegistrants->matching($criteria)->first();
        if (empty($userRegistrant)) {
            $errorDetail = 'not found: user registrant not found';
            throw RegularException::notFound($errorDetail);
        }
        return $userRegistrant;
    }

    protected function findUserParticipantCorrespondWithRegistrant(UserRegistrant $userRegistrant): ?UserParticipant
    {
        $p = function (UserParticipant $userParticipant) use ($userRegistrant) {
            return $userParticipant->correspondWithRegistrant($userRegistrant);
        };
        $userParticipant = $this->userParticipants->filter($p)->first();
        return empty($userParticipant) ? null : $userParticipant;
    }

}
