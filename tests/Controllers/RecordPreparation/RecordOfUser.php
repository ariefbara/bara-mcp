<?php

namespace Tests\Controllers\RecordPreparation;

use DateTimeImmutable;
use Illuminate\Database\ConnectionInterface;

class RecordOfUser implements Record
{
    public $id, $firstName, $lastName, $email, $password, $signupTime, $activated = false;
    public $activationCode, $activationCodeExpiredTime, $resetPasswordCode, $resetPasswordCodeExpiredTime;
    public $rawPassword;
    public $token;
    
    public function __construct($index)
    {
        $this->rawPassword = "pwd123user$index";
        
        $this->id = "user-$index-id";
        $this->firstName = "user-$index-fistname";
        $this->lastName = "user-$index-lastname";
        $this->email = "purnama.adi+user$index@gmail.com";
        $this->password = (new TestablePassword($this->rawPassword))->getHashedPassword();
        $this->signupTime = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        $this->activated = true;
        
        $this->activationCode = bin2hex(random_bytes(32));
        $this->activationCodeExpiredTime = (new DateTimeImmutable('+24 hours'))->format('Y-m-d H:i:s');
        $this->resetPasswordCode = bin2hex(random_bytes(32));
        $this->resetPasswordCodeExpiredTime = (new DateTimeImmutable('+24 hours'))->format('Y-m-d H:i:s');
        
        $data = [
            "userId" => $this->id,
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
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'password' => $this->password,
            'signupTime' => $this->signupTime,
            'activated' => $this->activated,
            'activationCode' => $this->activationCode,
            'activationCodeExpiredTime' => $this->activationCodeExpiredTime,
            'resetPasswordCode' => $this->resetPasswordCode,
            'resetPasswordCodeExpiredTime' => $this->resetPasswordCodeExpiredTime,
            
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('User')->insert($this->toArrayForDbEntry());
    }

}
