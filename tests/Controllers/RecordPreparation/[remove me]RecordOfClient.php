<?php

namespace Tests\Controllers\RecordPreparation;

use DateTime;

class RecordOfClient implements Record
{
    public $id, $name, $email, $password, $signupTime, $activated = false;
    public $activationCode, $activationCodeExpiredTime, $resetPasswordCode, $resetPasswordCodeExpiredTime;
    public $rawPassword;
    public $token;
    
    public function __construct($index, $email, $rawPassword)
    {
        $this->id = "client-$index-id";
        $this->name = "client $index name";
        $this->email = $email;
        $this->password = (new TestablePassword($rawPassword))->getHashedPassword();
        $this->signupTime = (new DateTime())->format('Y-m-d H:i:s');
        $this->activated = false;
        
        $this->activationCode = "random-string_activation-code__";// . random_bytes(8);
        $this->activationCodeExpiredTime = (new DateTime('+24 hours'))->format('Y-m-d H:i:s');
        $this->resetPasswordCode = "random-string_reset-password-code__";// . random_bytes(8);
        $this->resetPasswordCodeExpiredTime = (new DateTime('+24 hours'))->format('Y-m-d H:i:s');
        
        $this->rawPassword = $rawPassword;
        
        $data = [
            "clientId" => $this->id,
        ];
        $this->token = JwtHeaderTokenGenerator::generate($data);
    }
    
    public function toArrayForDbEntry()
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "password" => $this->password,
            "signupTime" => $this->signupTime,
            "activated" => $this->activated,
            "activationCode" => $this->activationCode,
            "activationCodeExpiredTime" => $this->activationCodeExpiredTime,
            "resetPasswordCode" => $this->resetPasswordCode,
            "resetPasswordCodeExpiredTime" => $this->resetPasswordCodeExpiredTime,
        ];
    }

}
