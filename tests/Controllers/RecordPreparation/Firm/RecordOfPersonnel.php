<?php

namespace Tests\Controllers\RecordPreparation\Firm;

use DateTime;
use DateTimeImmutable;
use Illuminate\Database\Connection;
use Tests\Controllers\RecordPreparation\JwtHeaderTokenGenerator;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\RecordOfFirm;
use Tests\Controllers\RecordPreparation\TestablePassword;

class RecordOfPersonnel implements Record
{
    /**
     *
     * @var RecordOfFirm
     */
    public $firm;
    public $id, $firstName, $lastName, $email, $password, $phone = "", $joinTime, $active;
    public $bio;
    public $resetPasswordCode;
    public $resetPasswordCodeExpiredTime;
    public $rawPassword;
    public $token;
    
    public function __construct(?RecordOfFirm $firm, $index)
    {
        $this->rawPassword = "Password12345";
        
        $this->firm = isset($firm)? $firm: new RecordOfFirm($index);
        $this->id = "personnel-$index-id";
        $this->firstName = "personnel $index firstname";
        $this->lastName = "personnel $index lastname";
        $this->email = "purnama.adi+personnel$index@gmail.com";
        $this->password = (new TestablePassword($this->rawPassword))->getHashedPassword();
        $this->phone = "";
        $this->bio = "personnel $index bio";
        $this->joinTime = (new DateTime())->format('Y-m-d H:i:s');
        $this->resetPasswordCode = "string-represent-reset-token";
        $this->resetPasswordCodeExpiredTime = (new DateTimeImmutable("+12 hours"))->format("Y-m-d H:i:s");
        $this->active = true;
        
        $data = [
            "firmId" => $this->firm->id,
            "personnelId" => $this->id,
        ];
        $this->token = JwtHeaderTokenGenerator::generate($data);
    }
    
    public function toArrayForDbEntry()
    {
        return [
            "Firm_id" => $this->firm->id,
            "id" => $this->id,
            "firstName" => $this->firstName,
            "lastName" => $this->lastName,
            "email" => $this->email,
            "password" => $this->password,
            "phone" => $this->phone,
            "bio" => $this->bio,
            "joinTime" => $this->joinTime,
            "resetPasswordCode" => $this->resetPasswordCode,
            "resetPasswordCodeExpiredTime" => $this->resetPasswordCodeExpiredTime,
            "active" => $this->active,
        ];
    }
    
    public function getFullName()
    {
        return $this->firstName . " " . $this->lastName;
    }
    
    public function persistSelf(Connection $connection): void
    {
        $connection->table("Personnel")->insert($this->toArrayForDbEntry());
    }
    
    public static function truncateTable(Connection $connection): void
    {
        $connection->table("Personnel")->truncate();
    }

}
