<?php

namespace Firm\Domain\Model\Firm;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Program\Participant;
use Firm\Domain\Model\Firm\Program\Registrant;
use Firm\Domain\Model\Firm\Program\TeamParticipant;
use Firm\Domain\Model\Firm\Team\Member;
use Firm\Domain\Model\Firm\Team\MemberData;
use Firm\Domain\Model\Firm\Team\TeamRegistrant;
use Query\Domain\Model\Firm\ParticipantTypes;
use Resources\DateTimeImmutableBuilder;
use Resources\Exception\RegularException;
use Resources\Uuid;
use Resources\ValidationRule;
use Resources\ValidationService;
use SharedContext\Domain\ValueObject\CustomerInfo;

class Team implements IProgramApplicant
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
     * @var DateTimeImmutable
     */
    protected $createdTime;

    /**
     * 
     * @var ArrayCollection
     */
    protected $members;

    /**
     * 
     * @var ArrayCollection
     */
    protected $teamParticipants;
    
    /**
     * 
     * @var ArrayCollection
     */
    protected $teamRegistrants;
    
    function getId(): string
    {
        return $this->id;
    }

    protected function setName(string $name): void
    {
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, 'bad request: team name is mandatory');
        $this->name = $name;
    }

    public function __construct(Firm $firm, string $id, TeamData $teamData)
    {
        $this->firm = $firm;
        $this->id = $id;
        $this->setName($teamData->getName());
        $this->createdTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();

        $this->members = new ArrayCollection();
        foreach ($teamData->getMemberDataList() as $memberData) {
            $member = new Member($this, Uuid::generateUuid4(), $memberData);
            $this->members->add($member);
        }
        if (empty($this->members->count())) {
            throw RegularException::forbidden('forbidden: team must have at least one member');
        }
    }

    public function assertManageableInFirm(Firm $firm): void
    {
        if ($this->firm !== $firm) {
            throw RegularException::forbidden('forbidden: can only manage team in same firm');
        }
    }

    public function idEquals(string $id): bool
    {
        return $this->id === $id;
    }

    public function addMember(MemberData $memberData): string
    {
        $p = function (Member $member) use ($memberData) {
            return $member->correspondWithClient($memberData->getClient());
        };
        $member = $this->members->filter($p)->first();
        if (!empty($member)) {
            $member->enable($memberData->getPosition());
        } else {
            $member = new Member($this, Uuid::generateUuid4(), $memberData);
            $this->members->add($member);
        }
        return $member->getId();
    }

    public function disableMember(string $memberId): void
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('id', $memberId));
        $member = $this->members->matching($criteria)->first();
        if (empty($member)) {
            throw RegularException::notFound('not found: team member not found');
        }
        $member->disable();
    }

    public function addToProgram(Program $program): string
    {
        $program->assertCanAcceptParticipantOfType('team');
        $p = function (TeamParticipant $teamParticipant) use ($program) {
            return $teamParticipant->correspondWithProgram($program);
        };
        $teamParticipant = $this->teamParticipants->filter($p)->first();
        if (!empty($teamParticipant)) {
            $teamParticipant->enable();
        } else {
            $id = Uuid::generateUuid4();
            $participant = new Participant($program, $id);
            $teamParticipant = new TeamParticipant($participant, $id, $this);
            $this->teamParticipants->add($teamParticipant);
        }
        return $teamParticipant->getId();
    }

    public function assertUsableInFirm(Firm $firm): void
    {
        if ($this->firm !== $firm) {
            throw RegularException::forbidden('forbidden: unable to use team from different firm');
        }
    }

    public function addProgramParticipation(string $participantId, Participant $participant): void
    {
        $teamParticipant = new TeamParticipant($participant, $participantId, $this);
        $this->teamParticipants->add($teamParticipant);
    }

    public function addProgramRegistration(string $registrantId, Registrant $registrant): void
    {
        $teamRegistrant = new TeamRegistrant($this, $registrantId, $registrant);
        $this->teamRegistrants->add($teamRegistrant);
    }

    public function assertBelongsInFirm(Firm $firm): void
    {
        if ($this->firm !== $firm) {
            throw RegularException::forbidden('team not belongs in same firm');
        }
    }

    public function getUserType(): string
    {
        return ParticipantTypes::TEAM_TYPE;
    }

    public function assertNoActiveParticipationOrOngoingRegistrationInProgram(Program $program): void
    {
        $p = function (TeamRegistrant $teamRegistrant) use ($program) {
            return $teamRegistrant->isUnconcludedRegistrationInProgram($program);
        };
        if (!$this->teamRegistrants->filter($p)->isEmpty()) {
            throw RegularException::forbidden('program application refused, team has unconcluded registration in same program');
        }

        $participantFilter = function (TeamParticipant $teamParticipant) use ($program) {
            return $teamParticipant->isActiveParticipantInProgram($program);
        };
        if (!$this->teamParticipants->filter($participantFilter)->isEmpty()) {
            throw RegularException::forbidden('program application refused, team is active participant in same program');
        }
    }

    public function assertTypeIncludedIn(ParticipantTypes $participantTypes): void
    {
        if (!$participantTypes->hasType(ParticipantTypes::TEAM_TYPE)) {
            throw RegularException::forbidden('program application refused, team type is not accomodate in program');
        }
    }
    
    public function getCustomerInfo(): CustomerInfo
    {
        return new CustomerInfo($this->name, 'donotsend@innov.id');
    }

}
