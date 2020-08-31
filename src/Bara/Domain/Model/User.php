<?php

namespace Bara\Domain\Model;

use Config\BaseConfig;
use Resources\{
    Application\Service\Mailer,
    Domain\Model\Mail,
    Domain\Model\Mail\Recipient,
    Domain\ValueObject\PersonName,
    Exception\RegularException
};

class User
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
     * @var string
     */
    protected $activationCode = null;

    /**
     *
     * @var string
     */
    protected $resetPasswordCode = null;

    /**
     *
     * @var bool
     */
    protected $activated;

    protected function __construct()
    {
        ;
    }

    public function sendActivationCodeMail(Mailer $mailer): void
    {
        if ($this->activated) {
            $errorDetail = 'forbidden: unable to send activation code mail for active accout';
            throw RegularException::forbidden($errorDetail);
        }
        if (empty($this->activationCode)) {
            $errorDetail = 'forbidden: unable to send empty activation code mail';
            throw RegularException::forbidden($errorDetail);
        }

        $subject = "konsulta account activation";
        $body = <<<_MAIL
Hi {$this->personName->getFirstName()},

Selamat, kamu telah berhasil signup di komunitas konsulta.
Masih ada satu langkah lagi sebelum kamu dapat mengakses fasilitas konsulta, dengan terlebih dahulu aktivasi akun kamu di link ini:
https://konsulta.id/user-activation/{$this->email}/{$this->activationCode}

Salam,

Team Konsulta.
_MAIL;
        $alternativeBody = null;
        $recipient = new Recipient($this->email, $this->personName->getFullName());

        $mail = new Mail($subject, $body, $alternativeBody, $recipient);
        $mailer->send($mail, BaseConfig::MAIL_SENDER_NAME, BaseConfig::MAIL_SENDER_ADDRESS);
    }

    public function sendResetPasswordCodeMail(Mailer $mailer): void
    {
        if (!$this->activated) {
            $errorDetail = 'forbidden: unable to send reset password code mail on inactive Account';
            throw RegularException::forbidden($errorDetail);
        }
        if (empty($this->resetPasswordCode)) {
            $errorDetail = 'forbidden: unable to send empty reset password code mail';
            throw RegularException::forbidden($errorDetail);
        }

        $subject = "konsulta reset password";
        $body = <<<_MAIL
Hi {$this->personName->getFirstName()},

Permintaan reset password kamu telah kami terima.
Silahkan lanjut proses reset password dengan mengunjungi halaman berikut:
https://konsulta.id/user-activation/{$this->email}/{$this->activationCode}
link di atas akan expired dalam waktu 24 jam, jadi pastikan kamu melanjutkan proses reset password dalam waktu yg tersedia.

Jangan bagikan link di atas kepada siapa pun, termasuk kepada staff konsulta.
Jika kamu tidak merasa melakukan permintaan reset password di konsulta, Abaikan email ini.

Salam,

Team Konsulta.
_MAIL;
        $alternativeBody = null;
        $recipient = new Recipient($this->email, $this->personName->getFullName());

        $mail = new Mail($subject, $body, $alternativeBody, $recipient);
        $mailer->send($mail, BaseConfig::MAIL_SENDER_NAME, BaseConfig::MAIL_SENDER_ADDRESS);
    }

}
