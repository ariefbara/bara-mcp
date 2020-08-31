<?php

namespace Firm\Domain\Model\Firm;

use DateTimeImmutable;
use Firm\Domain\Model\Firm;
use Resources\{
    Application\Service\Mailer,
    DateTimeImmutableBuilder,
    Domain\Model\Mail,
    Domain\Model\Mail\Recipient,
    Domain\ValueObject\Password,
    Domain\ValueObject\PersonName,
    ValidationRule,
    ValidationService
};

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

    public function getId(): string
    {
        return $this->id;
    }

    public function getPersonName(): PersonName
    {
        return $this->personName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

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
        $this->firm = $firm;
        $this->id = $id;
        $this->personName = new PersonName($clientData->getFirstName(), $clientData->getLastName());
        $this->setEmail($clientData->getEmail());
        $this->password = new Password($clientData->getPassword());
        $this->signupTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->activationCode = bin2hex(random_bytes(32));
        $this->activationCodeExpiredTime = DateTimeImmutableBuilder::buildYmdHisAccuracy('+24 hours');
        $this->resetPasswordCode = null;
        $this->resetPasswordCodeExpiredTime = null;
        $this->activated = false;
    }

    public function sendActivationCodeMail(Mailer $mailer): void
    {
        $subject = "Konsulta - {$this->firm->getName()}: account activation";
        $body = <<<_MAILBODY
Hi {$this->personName->getFirstName()},

Selamat, kamu telah berhasil melakukan signup di komunitas konsulta {$this->firm->getName()}.
Masih ada satu langkah lagi sebelum kamu dapat mengakses fasilitas konsulta, dengan terlebih dahulu aktivasi akun kamu di link ini:
{$this->firm->getWhitelableUrl()}/activate-client-account/{$this->firm->getIdentifier()}/{$this->email}/{$this->activationCode}
Jangan bagikan link tersebut kepada siapapun, termasuk kepada staff konsulta.

Salam,

Team Konsulta {$this->firm->getName()}
_MAILBODY;
        $alternativeBody = null;
        $recipient = new Recipient($this->email, $this->personName);

        $mail = new Mail($subject, $body, $alternativeBody, $recipient);

        $senderName = $this->firm->getWhitelableMailSenderName();
        $senderAddress = $this->firm->getWhitelableMailSenderAddress();
        $mailer->send($mail, $senderName, $senderAddress);
    }

    public function sendResetPasswordCodeMail(Mailer $mailer): void
    {
        $subject = "Konsulta - {$this->firm->getName()}: reset password";
        $body = <<<_MAILBODY
Hi {$this->personName->getFirstName()},

Kami telah menerima permintaan reset password untuk akun kamu.
Untuk melanjutkan proses reset password, silahkan kunjungi link berikut:
{$this->firm->getWhitelableUrl()}/reset-client-password/{$this->firm->getIdentifier()}/{$this->email}/{$this->resetPasswordCode}
Jangan bagikan link tersebut kepada siapapun, termasuk kepada staff konsulta.

Jika kamu tidak merasa melakukan permintaan reset password, abaikan email ini.

Salam,

Team Konsulta {$this->firm->getName()}
_MAILBODY;
        $alternativeBody = null;
        $recipient = new Recipient($this->email, $this->personName);

        $mail = new Mail($subject, $body, $alternativeBody, $recipient);

        $senderName = $this->firm->getWhitelableMailSenderName();
        $senderAddress = $this->firm->getWhitelableMailSenderAddress();
        $mailer->send($mail, $senderName, $senderAddress);
    }
    
    public function getMailRecipient(): Recipient
    {
        return new Recipient($this->email, $this->personName);
    }
    
    public function getName(): string
    {
        return $this->personName->getFullName();
    }

}
