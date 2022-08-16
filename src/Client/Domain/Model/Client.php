<?php

namespace Client\Domain\Model;

use Client\Domain\DependencyModel\Firm\BioForm;
use Client\Domain\Event\ClientHasAppliedToProgram;
use Client\Domain\Model\Client\ClientBio;
use Client\Domain\Model\Client\ClientFileInfo;
use Config\EventList;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Query\Domain\Model\Firm;
use Resources\DateTimeImmutableBuilder;
use Resources\Domain\Event\CommonEvent;
use Resources\Domain\Model\EntityContainEvents;
use Resources\Domain\ValueObject\Password;
use Resources\Domain\ValueObject\PersonName;
use Resources\Exception\RegularException;
use Resources\Uuid;
use Resources\ValidationRule;
use Resources\ValidationService;
use SharedContext\Domain\Model\SharedEntity\FileInfoData;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class Client extends EntityContainEvents
{

    /**
     *
     * @var string
     */
    protected $firmId;

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
     * @var DateTimeImmutable
     */
    protected $signupTime;

    /**
     *
     * @var string||null
     */
    protected $activationCode;

    /**
     *
     * @var DateTimeImmutable||null
     */
    protected $activationCodeExpiredTime;

    /**
     *
     * @var string||null
     */
    protected $resetPasswordCode;

    /**
     *
     * @var DateTimeImmutable||null
     */
    protected $resetPasswordCodeExpiredTime;

    /**
     *
     * @var bool
     */
    protected $activated = false;

//    /**
//     *
//     * @var ArrayCollection
//     */
//    protected $programRegistrations;
//
//    /**
//     *
//     * @var ArrayCollection
//     */
//    protected $programParticipations;

    /**
     * 
     * @var ArrayCollection
     */
    protected $clientBios;

    protected function setEmail(string $email): void
    {
        $errorDetail = 'bad request: invalid email format';
        ValidationService::build()
                ->addRule(ValidationRule::email())
                ->execute($email, $errorDetail);
        $this->email = $email;
    }

    function __construct(Firm $firm, string $id, ClientData $clientData)
    {
        $this->firmId = $firm->getId();
        $this->id = $id;
        $this->personName = new PersonName($clientData->getFirstName(), $clientData->getLastName());
        $this->setEmail($clientData->getEmail());
        $this->password = new Password($clientData->getPassword());
        $this->signupTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->resetPasswordCode = null;
        $this->resetPasswordCodeExpiredTime = null;
        $this->activated = false;

        $this->generateActivationCode();
    }

    public function updateProfile(string $firstName, ?string $lastName): void
    {
        $this->assertAccountActive();
        $this->personName = new PersonName($firstName, $lastName);
    }

    public function changePassword(string $previousPassword, string $newPassword): void
    {
        $this->assertAccountActive();
        if (!$this->password->match($previousPassword)) {
            $errorDetail = 'forbidden: previous password not match';
            throw RegularException::forbidden($errorDetail);
        }
        $this->password = new Password($newPassword);
    }

    public function activate(string $activationCode): void
    {
        if ($this->activated) {
            $errorDetail = 'forbidden: account already activated';
            throw RegularException::forbidden($errorDetail);
        }

        if (empty($this->activationCode) || $this->activationCode !== $activationCode || $this->activationCodeExpiredTime < new \DateTimeImmutable()
        ) {
            $errorDetail = 'forbidden: activation code not match or expired';
            throw RegularException::forbidden($errorDetail);
        }

        $this->activated = true;
        $this->activationCode = null;
        $this->activationCodeExpiredTime = null;
    }

    public function resetPassword(string $resetPasswordCode, string $password): void
    {
        $this->assertAccountActive();

        if (empty($this->resetPasswordCode) || $this->resetPasswordCode !== $resetPasswordCode || $this->resetPasswordCodeExpiredTime < new \DateTimeImmutable()
        ) {
            $errorDetail = 'forbidden: reset password code not match or expired';
            throw RegularException::forbidden($errorDetail);
        }
        $this->password = new Password($password);
        $this->resetPasswordCode = null;
        $this->resetPasswordCodeExpiredTime = null;
    }

    public function generateActivationCode(): void
    {
        if ($this->activated) {
            $errorDetail = 'forbidden: account already activated';
            throw RegularException::forbidden($errorDetail);
        }
        $this->activationCode = bin2hex(random_bytes(32));
        $this->activationCodeExpiredTime = DateTimeImmutableBuilder::buildYmdHisAccuracy('+24 hours');

        $event = new CommonEvent(EventList::CLIENT_ACTIVATION_CODE_GENERATED, $this->id);
        $this->recordEvent($event);
    }

    public function generateResetPasswordCode(): void
    {
        $this->assertAccountActive();

        $this->resetPasswordCode = bin2hex(random_bytes(32));
        $this->resetPasswordCodeExpiredTime = DateTimeImmutableBuilder::buildYmdHisAccuracy('+24 hours');

        $event = new CommonEvent(EventList::CLIENT_RESET_PASSWORD_CODE_GENERATED, $this->id);
        $this->recordEvent($event);
    }

    public function createClientFileInfo(string $clientFileInfoId, FileInfoData $fileInfoData): ClientFileInfo
    {
        return new ClientFileInfo($this, $clientFileInfoId, $fileInfoData);
    }

//    public function registerToProgram(string $programRegistrationId, ProgramInterface $program): ProgramRegistration
//    {
//        $this->assertAccountActive();
//        if (!$program->firmIdEquals($this->firmId)) {
//            $errorDetail = 'forbidden: cannot register to program from different firm';
//            throw RegularException::forbidden($errorDetail);
//        }
//        $this->assertNoUnconcludedRegistrationToSameProgram($program);
//        $this->assertNoActiveParticipationInSameProgram($program);
//        return new ProgramRegistration($this, $programRegistrationId, $program);
//    }

    public function submitBio(BioForm $bioForm, FormRecordData $formRecordData): void
    {
        $this->assertAccountActive();
        if (!$bioForm->belongsToFirm($this->firmId)) {
            $errorDetail = "forbidden: can only use asset in same firm";
            throw RegularException::forbidden($errorDetail);
        }

        $p = function (ClientBio $clientBio) use ($bioForm) {
            return $clientBio->isActiveBioCorrespondWithForm($bioForm);
        };
        if (!empty($clientBio = $this->clientBios->filter($p)->first())) {
            $clientBio->update($formRecordData);
        } else {
            $id = Uuid::generateUuid4();
            $clientBio = new ClientBio($this, $id, $bioForm, $formRecordData);
            $this->clientBios->add($clientBio);
        }
    }

    public function removeBio(ClientBio $clientBio): void
    {
        $this->assertAccountActive();
        if (!$clientBio->belongsToClient($this)) {
            $errorDetail = "forbidden: can only manage owned asset";
            throw RegularException::forbidden($errorDetail);
        }
        $clientBio->remove();
    }

    protected function assertAccountActive(): void
    {
        if (!$this->activated) {
            $errorDetail = 'forbidden: only active client can make this request';
            throw RegularException::forbidden($errorDetail);
        }
    }

//    protected function assertNoUnconcludedRegistrationToSameProgram(ProgramInterface $program): void
//    {
//        $p = function (ProgramRegistration $programRegistration) use ($program) {
//            return $programRegistration->isUnconcludedRegistrationToProgram($program);
//        };
//        if (!empty($this->programRegistrations->filter($p)->count())) {
//            $errorDetail = 'forbidden: client already registered to this program';
//            throw RegularException::forbidden($errorDetail);
//        }
//    }
//
//    protected function assertNoActiveParticipationInSameProgram(ProgramInterface $program): void
//    {
//        $p = function (ProgramParticipation $programParticipation) use ($program) {
//            return $programParticipation->isActiveParticipantOfProgram($program);
//        };
//        if (!empty($this->programParticipations->filter($p)->count())) {
//            $errorDetail = 'forbidden: client already active participant of this program';
//            throw RegularException::forbidden($errorDetail);
//        }
//    }

    public function executeTask(IClientTask $task, $payload): void
    {
        if (!$this->activated) {
            throw RegularException::forbidden('only active client can make this request');
        }
        $task->execute($this, $payload);
    }

    public function applyToProgram(string $programId): void
    {
        $event = new ClientHasAppliedToProgram($this->firmId, $this->id, $programId);
        $this->recordEvent($event);
    }

//    public function applyToProgram(Program $program): void
//    {
//        $registrationFilter = function (ClientRegistrant $clientRegistrant) use ($program) {
//            return $clientRegistrant->isActiveRegistrationCorrespondWithProgram($program);
//        };
//        if (!empty($this->clientRegistrants->filter($registrationFilter)->count())) {
//            throw RegularException::forbidden('you have active registration to this program');
//        }
//        
//        $participationFilter = function(ClientParticipant $clientParticipant) use ($program) {
//            return $clientParticipant->isActiveParticipationCorrespondWithProgram($program);
//        };
//        if (!empty($this->clientParticipants->filter($participationFilter)->count())) {
//            throw RegularException::forbidden('you are participating in this program');
//        }
//        
//        $event = new ClientHasAppliedToProgram($this->id, $program->getId());
//        $this->recordEvent($event);
//    }
//
//    public function createClientRegistrant(string $clientRegistrantId, Registrant $registrant): ClientRegistrant
//    {
//        return new ClientRegistrant($this, $clientRegistrantId, $registrant);
//    }
//
//    public function createClientParticipant(string $clientParticipantId, Program\Participant $participant): ClientParticipant
//    {
//        return new ClientParticipant($this, $clientParticipantId, $participant);
//    }
}
