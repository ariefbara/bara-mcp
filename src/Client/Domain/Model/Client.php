<?php

namespace Client\Domain\Model;

use Client\Domain\ {
    Event\ClientActivationCodeGenerated,
    Event\ClientResetPasswordCodeGenerated,
    Model\Client\ClientFileInfo,
    Model\Client\ProgramParticipation,
    Model\Client\ProgramRegistration
};
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Query\Domain\Model\Firm;
use Resources\ {
    DateTimeImmutableBuilder,
    Domain\Model\EntityContainEvents,
    Domain\ValueObject\Password,
    Domain\ValueObject\PersonName,
    Exception\RegularException,
    ValidationRule,
    ValidationService
};
use SharedContext\Domain\Model\SharedEntity\FileInfoData;

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

    /**
     *
     * @var ArrayCollection
     */
    protected $programRegistrations;

    /**
     *
     * @var ArrayCollection
     */
    protected $programParticipations;
    
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

        $event = new ClientActivationCodeGenerated($this->firmId, $this->id);
        $this->recordEvent($event);
    }

    public function generateResetPasswordCode(): void
    {
        $this->assertAccountActive();

        $this->resetPasswordCode = bin2hex(random_bytes(32));
        $this->resetPasswordCodeExpiredTime = DateTimeImmutableBuilder::buildYmdHisAccuracy('+24 hours');

        $event = new ClientResetPasswordCodeGenerated($this->firmId, $this->id);
        $this->recordEvent($event);
    }
    
    public function createClientFileInfo(string $clientFileInfoId, FileInfoData $fileInfoData): ClientFileInfo
    {
        return new ClientFileInfo($this, $clientFileInfoId, $fileInfoData);
    }

    public function registerToProgram(string $programRegistrationId, ProgramInterface $program): ProgramRegistration
    {
        $this->assertAccountActive();
        if (!$program->firmIdEquals($this->firmId)) {
            $errorDetail = 'forbidden: cannot register to program from different firm';
            throw RegularException::forbidden($errorDetail);
        }
        $this->assertNoUnconcludedRegistrationToSameProgram($program);
        $this->assertNoActiveParticipationInSameProgram($program);
        return new ProgramRegistration($this, $programRegistrationId, $program);
    }

    protected function assertAccountActive(): void
    {
        if (!$this->activated) {
            $errorDetail = 'forbidden: only active client can  make this request';
            throw RegularException::forbidden($errorDetail);
        }
    }
    protected function assertNoUnconcludedRegistrationToSameProgram(ProgramInterface $program): void
    {
        $p = function (ProgramRegistration $programRegistration) use ($program) {
            return $programRegistration->isUnconcludedRegistrationToProgram($program);
        };
        if (!empty($this->programRegistrations->filter($p)->count())) {
            $errorDetail = 'forbidden: client already registered to this program';
            throw RegularException::forbidden($errorDetail);
        }
    }
    protected function assertNoActiveParticipationInSameProgram(ProgramInterface $program): void
    {
        $p = function (ProgramParticipation $programParticipation) use ($program) {
            return $programParticipation->isActiveParticipantOfProgram($program);
        };
        if (!empty($this->programParticipations->filter($p)->count())) {
            $errorDetail = 'forbidden: client already active participant of this program';
            throw RegularException::forbidden($errorDetail);
        }
    }
}
