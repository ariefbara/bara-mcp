<?php

namespace Firm\Domain\Model\Firm;

use DateTimeImmutable;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;
use Resources\DateTimeImmutableBuilder;
use Resources\Domain\ValueObject\Password;
use Resources\Domain\ValueObject\PersonName;
use Resources\Exception\RegularException;
use Resources\ValidationRule;
use Resources\ValidationService;

class Client
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

}
