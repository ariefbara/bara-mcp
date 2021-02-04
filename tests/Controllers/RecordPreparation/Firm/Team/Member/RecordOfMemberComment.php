<?php

namespace Tests\Controllers\RecordPreparation\Firm\Team\Member;

use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet\RecordOfComment;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfMemberComment implements Record
{
    /**
     * 
     * @var RecordOfMember|null
     */
    public $member;
    /**
     * 
     * @var RecordOfComment
     */
    public $comment;
    
    public function __construct(?RecordOfMember $member, RecordOfComment $comment)
    {
        $this->member = $member;
        $this->comment = $comment;
    }
    
    public function toArrayForDbEntry()
    {
        return [
            'Member_id' => isset($this->member)? $this->member->id : null,
            'id' => $this->comment->id,
        ];
    }

}
