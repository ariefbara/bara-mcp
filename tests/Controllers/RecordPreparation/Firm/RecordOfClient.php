<?php

namespace Tests\Controllers\RecordPreparation\Firm;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\ {
    JwtHeaderTokenGenerator,
    Record,
    RecordOfFirm,
    TestablePassword
};

class RecordOfClient implements Record
{
    /**
     *
     * @var RecordOfFirm
     */
    public $firm;
    public $id, $firstName, $lastName, $email, $password, $activationCode, $activationCodeExpiredTime, 
            $resetPasswordCode, $resetPasswordCodeExpiredTime, $activated, $signupTime;
    
    public $rawPassword;
    public $token;
    
    public function __construct(RecordOfFirm $firm, $index)
    {
        
        $this->firm = $firm;
        $this->id = "client-$index-id";
        $this->firstName = "client-$index-first_name";
        $this->lastName = 'last_name';
        $this->email = "client_$index@barapraja.com";
        $this->activationCode = bin2hex(random_bytes(32));
        $this->activationCodeExpiredTime = (new DateTimeImmutable('+24 hours'))->format('Y-m-d H:i:s');
        $this->resetPasswordCode = bin2hex(random_bytes(32));
        $this->resetPasswordCodeExpiredTime = (new DateTimeImmutable('+24 hours'))->format('Y-m-d H:i:s');
        $this->activated = true;
        $this->signupTime = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        
        $this->rawPassword = "Password123$index";
        $this->password = (new TestablePassword($this->rawPassword))->getHashedPassword();
        
        $data = [
            "firmId" => $this->firm->id,
            "clientId" => $this->id,
        ];
        $this->token = JwtHeaderTokenGenerator::generate($data);
    }
    
    public function getFullName()
    {
        return $this->firstName . " " . $this->lastName;
    }
    
    public function toArrayForDbEntry()
    {
        return [
            'Firm_id' => $this->firm->id,
            "id" => $this->id,
            "firstName" => $this->firstName,
            "lastName" => $this->lastName,
            "email" => $this->email,
            "password" => $this->password,
            "activated" => $this->activated,
            "signupTime" => $this->signupTime,
            "activationCode" => $this->activationCode,
            "activationCodeExpiredTime" => $this->activationCodeExpiredTime,
            "resetPasswordCode" => $this->resetPasswordCode,
            "resetPasswordCodeExpiredTime" => $this->resetPasswordCodeExpiredTime,
        ];
    }

}
