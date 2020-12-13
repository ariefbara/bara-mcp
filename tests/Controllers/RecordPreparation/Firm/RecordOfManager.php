<?php

namespace Tests\Controllers\RecordPreparation\Firm;

use DateTime;
use DateTimeImmutable;
use Illuminate\Database\Connection;
use Tests\Controllers\RecordPreparation\JwtHeaderTokenGenerator;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\RecordOfFirm;
use Tests\Controllers\RecordPreparation\TestablePassword;

class RecordOfManager implements Record
{
    /**
     *
     * @var RecordOfFirm
     */
    public $firm;
    public $id, $name, $email, $password, $phone = "", $joinTime, $removed = false;
    public $resetPasswordCode;
    public $resetPasswordCodeExpiredTime;
    public $rawPassword;
    public $token;
    
    public function __construct(?RecordOfFirm $firm, $index)
    {
        $this->rawPassword = "Password123";
        
        $this->firm = isset($firm)? $firm: new RecordOfFirm($index);
        $this->id = "manager-$index-id";
        $this->name = "manager $index name";
        $this->email = "manager@email.org";
        $this->password = (new TestablePassword($this->rawPassword))->getHashedPassword();
        $this->phone = "";
        $this->joinTime = (new DateTime())->format('Y-m-d H:i:s');
        $this->resetPasswordCode = "string-represent-reset-token";
        $this->resetPasswordCodeExpiredTime = (new DateTimeImmutable("+8 hours"))->format("Y-m-d H:i:s");
        $this->removed = false;
        
        $data = [
            "firmId" => $this->firm->id,
            "managerId" => $this->id,
        ];
        $this->token = JwtHeaderTokenGenerator::generate($data);
    }
    
    public function toArrayForDbEntry()
    {
        return [
            "Firm_id" => $this->firm->id,
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "password" => $this->password,
            "phone" => $this->phone,
            "joinTime" => $this->joinTime,
            "resetPasswordCode" => $this->resetPasswordCode,
            "resetPasswordCodeExpiredTime" => $this->resetPasswordCodeExpiredTime,
            "removed" => $this->removed,
        ];
    }
    
    public function persistSelf(Connection $connection): void
    {
        $connection->table("Manager")->insert($this->toArrayForDbEntry());
    }
    
    public static function truncateTable(Connection $connection): void
    {
        $connection->table("Manager")->truncate();
    }

}
