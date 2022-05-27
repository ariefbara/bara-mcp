<?php

namespace Firm\Domain\Model\Firm;

use Client\Domain\Model\Client\ClientParticipant;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;
use Firm\Domain\Model\Firm\Program\Participant;
use Firm\Domain\Model\Firm\Program\Registrant;
use Resources\DateTimeImmutableBuilder;
use Resources\Domain\Model\EntityContainEvents;
use Resources\Domain\ValueObject\Password;
use Resources\Domain\ValueObject\PersonName;
use Resources\Exception\RegularException;
use Resources\ValidationRule;
use Resources\ValidationService;
use SharedContext\Domain\ValueObject\CustomerInfo;

class Client extends EntityContainEvents implements IProgramApplicant
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
     * @var PersonName
     */
    protected $personName;

    /**
     * 
     * @var string
     */
    protected $email;

    /**
     * 
     * @var Password
     */
    protected $password;

    /**
     * 
     * @var DateTimeImmutable|null
     */
    protected $signupTime;

    /**
     * 
     * @var bool
     */
    protected $activated;

    /**
     * 
     * @var string|null
     */
    protected $activationCode;

    /**
     * 
     * @var DateTimeImmutable|null
     */
    protected $activationCodeExpiredTime;
    
    /**
     * 
     * @var ArrayCollection
     */
    protected $clientParticipants;
    
    /**
     * 
     * @var ArrayCollection
     */
    protected $clientRegistrants;

    protected function setEmail(string $email): void
    {
        ValidationService::build()
                ->addRule(ValidationRule::email())
                ->execute($email, 'bad request: client email must be a valid mail address');
        $this->email = $email;
    }

    public function __construct(Firm $firm, string $id, ClientRegistrationData $clientRegistrationData)
    {
        $this->firm = $firm;
        $this->id = $id;
        $this->setEmail($clientRegistrationData->getEmail());
        $this->personName = new PersonName(
                $clientRegistrationData->getFirstName(), $clientRegistrationData->getLastName());
        $this->password = new Password($clientRegistrationData->getPassword());
        $this->signupTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->activated = true;
        $this->activationCode = null;
        $this->activationCodeExpiredTime = null;
    }

    public function assertManageableInFirm(Firm $firm): void
    {
        if ($this->firm !== $firm) {
            throw RegularException::forbidden('forbidden: can only manage client in same firm');
        }
    }
    
    public function assertUsableInFirm(Firm $firm): void
    {
        if (!$this->activated) {
            throw RegularException::forbidden('forbidden: only activated client is usable');
        }
        if ($this->firm !== $firm) {
            throw RegularException::forbidden('forbidden: can only used client from same firm');
        }
    }

    public function activate(): void
    {
        $this->activated = true;
        $this->activationCode = null;
        $this->activationCodeExpiredTime = null;
    }

    public function submitCommentInMission(
            Mission $mission, string $missionCommentId, MissionCommentData $missionCommentData): MissionComment
    {
        return $mission->receiveComment(
                        $missionCommentId, $missionCommentData, $this->id, $this->personName->getFullName());
    }

    public function replyMissionComment(
            MissionComment $missionComment, string $replyId, MissionCommentData $missionCommentData): MissionComment
    {
        return $missionComment->receiveReply(
                        $replyId, $missionCommentData, $this->id, $this->personName->getFullName());
    }
    
//    public function addIntoProgram(Program $program): string
//    {
//        $program->assertCanAcceptParticipantOfType('client');
//        $p = function(ClientParticipant $clientParticipant) use($program) {
//            return $clientParticipant->correspondWithProgram($program);
//        };
//        $clientParticipant = $this->clientParticipants->filter($p)->first();
//        if (!empty($clientParticipant)) {
//            $clientParticipant->enable();
//        } else {
//            $id = Uuid::generateUuid4();
//            $participant = new Participant($program, $id);
//            $clientParticipant = new ClientParticipant($participant, $id, $this);
//            $this->clientParticipants->add($clientParticipant);
//        }
//        return $clientParticipant->getId();
//    }

    public function assertBelongsInFirm(Firm $firm): void
    {
        if ($this->firm !== $firm) {
            throw RegularException::forbidden('client is from different firm');
        }
    }

    public function getUserType(): string
    {
        return 'client';
    }
    
    public function getClientCustomerInfo(): CustomerInfo
    {
        return new CustomerInfo($this->personName->getFullName(), $this->email);
    }

    public function addProgramParticipation(string $participantId, Participant $participant): void
    {
        $clientParticipant = new ClientParticipant($participant, $participantId, $this);
        $this->clientParticipants->add($clientParticipant);
    }

    public function addProgramRegistration(string $registrantId, Registrant $registrant): void
    {
        
    }

}
