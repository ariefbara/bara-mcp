<?php

namespace User\Domain\Model;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Resources\ {
    DateTimeImmutableBuilder,
    Domain\Model\ModelContainEvents,
    Domain\ValueObject\Password,
    Domain\ValueObject\PersonName,
    Exception\RegularException,
    ValidationRule,
    ValidationService
};
use SharedContext\Domain\Model\SharedEntity\FileInfoData;
use User\Domain\ {
    Event\UserActivationCodeGenerated,
    Event\UserPasswordResetCodeGenerated,
    Model\User\ProgramParticipation,
    Model\User\ProgramRegistration,
    Model\User\UserFileInfo
};

class User extends ModelContainEvents
{

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
     * @var string
     */
    protected $activationCode = null;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $activationCodeExpiredTime = null;

    /**
     *
     * @var string
     */
    protected $resetPasswordCode = null;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $resetPasswordCodeExpiredTime = null;

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

    private function setEmail($email)
    {
        $errorDetail = "bad request: invalid email format";
        ValidationService::build()
                ->addRule(ValidationRule::email())
                ->execute($email, $errorDetail);
        $this->email = $email;
    }

    function __construct(string $id, UserData $userData)
    {
        $this->id = $id;
        $this->personName = new PersonName($userData->getFirstName(), $userData->getLastName());
        $this->setEmail($userData->getEmail());
        $this->password = new Password($userData->getPassword());
        $this->signupTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->activated = false;

        $this->generateActivationCode();
    }

    public function generateResetPasswordCode(): void
    {
        $this->assertActive();

        $this->resetPasswordCode = bin2hex(random_bytes(32));
        $this->resetPasswordCodeExpiredTime = new DateTimeImmutable("+24 hours");
        $event = new UserPasswordResetCodeGenerated($this->id);
        $this->recordEvent($event);
    }

    public function generateActivationCode(): void
    {
        if ($this->activated) {
            $errorDetails = 'forbidden: account already activated';
            throw RegularException::forbidden($errorDetails);
        }
        $this->activationCode = bin2hex(random_bytes(32));
        $this->activationCodeExpiredTime = new DateTimeImmutable('+24 hours');

        $event = new UserActivationCodeGenerated($this->id);
        $this->recordEvent($event);
    }

    public function activate(string $activationCode): void
    {
        if ($this->activated) {
            $errorDetail = 'forbidden: account already activated';
            throw RegularException::forbidden($errorDetail);
        }

        if (
                empty($this->activationCode) ||
                $this->activationCode != $activationCode ||
                $this->activationCodeExpiredTime < new DateTimeImmutable()
        ) {
            $errorDetail = 'forbidden: invalid or expired token';
            throw RegularException::forbidden($errorDetail);
        }

        $this->activated = true;
        $this->activationCode = null;
        $this->activationCodeExpiredTime = null;
    }

    public function resetPassword(string $resetPasswordCode, string $password): void
    {
        $this->assertActive();

        if (
                empty($this->resetPasswordCode) ||
                $this->resetPasswordCode != $resetPasswordCode ||
                $this->resetPasswordCodeExpiredTime < new DateTimeImmutable()
        ) {
            $errorDetail = 'forbidden: invalid or expired token';
            throw RegularException::forbidden($errorDetail);
        }

        $this->password = new Password($password);
        $this->resetPasswordCode = null;
        $this->resetPasswordCodeExpiredTime = null;
    }

    public function changeProfile(string $firstName, string $lastName): void
    {
        $this->assertActive();
        $this->personName = new PersonName($firstName, $lastName);
    }

    public function changePassword(string $previousPassword, string $newPassword): void
    {
        $this->assertActive();
        if (!$this->password->match($previousPassword)) {
            $errorDetails = "forbidden: previous password not match";
            throw RegularException::forbidden($errorDetails);
        }
        $this->password = new Password($newPassword);
    }

    public function registerToProgram(string $programRegistrationId, ProgramInterface $program): ProgramRegistration
    {
        $this->assertActive();
        $this->assertNoUnconcludedRegistrationToSameProgram($program);
        $this->assertNoActiveParticipationToSameProgram($program);
        return new ProgramRegistration($this, $programRegistrationId, $program);
    }

    public function createUserFileInfo(string $userFileInfoId, FileInfoData $fileInfoData): UserFileInfo
    {
        return new UserFileInfo($this, $userFileInfoId, $fileInfoData);
    }

    protected function assertActive(): void
    {
        if (!$this->activated) {
            $errorDetail = 'forbidden: inactive account';
            throw RegularException::forbidden($errorDetail);
        }
    }

    protected function assertNoUnconcludedRegistrationToSameProgram(ProgramInterface $program): void
    {
        $p = function (ProgramRegistration $programRegistration) use ($program) {
            return $programRegistration->isUnconcludedRegistrationToProgram($program);
        };
        if (!empty($this->programRegistrations->filter($p)->count())) {
            $errorDetail = 'forbidden: you already registered to this program';
            throw RegularException::forbidden($errorDetail);
        }
    }

    protected function assertNoActiveParticipationToSameProgram(ProgramInterface $program): void
    {
        $p = function (ProgramParticipation $programParticipation) use ($program) {
            return $programParticipation->isActiveParticipantInProgram($program);
        };
        if (!empty($this->programParticipations->filter($p)->count())) {
            $errorDetail = 'forbidden: you already participate in this program';
            throw RegularException::forbidden($errorDetail);
        }
    }

}
