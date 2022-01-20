<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\ {
    Firm\RecordOfFeedbackForm,
    Shared\RecordOfForm
};

class FeedbackFormTestCase extends ManagerTestCase
{
    protected $feedbackFormUri;
    /**
     *
     * @var RecordOfFeedbackForm
     */
    protected $feedbackForm;
    protected $feedbackFormInput = [
        "name" => 'new consultation feedback form name',
        "description" => 'new consultation feedback form description',
        "stringFields" => [],
        "integerFields" => [],
        "textAreaFields" => [],
        "singleSelectFields" => [],
        "multiSelectFields" => [],
        "attachmentFields" => [],
        "sections" => [],
    ];
    protected $feedbackFormResponse = [
        "stringFields" => [],
        "integerFields" => [],
        "textAreaFields" => [],
        "singleSelectFields" => [],
        "multiSelectFields" => [],
        "attachmentFields" => [],
        "sections" => [],
    ];


    protected function setUp(): void
    {
        parent::setUp();
        $this->feedbackFormUri = $this->managerUri . "/feedback-forms";
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("Form")->truncate();
        
        $this->connection->table('StringField')->truncate();
        $this->connection->table('IntegerField')->truncate();
        $this->connection->table('TextAreaField')->truncate();
        $this->connection->table('AttachmentField')->truncate();
        $this->connection->table('SingleSelectField')->truncate();
        $this->connection->table('MultiSelectField')->truncate();
        $this->connection->table('Section')->truncate();
        
        $this->connection->table('SelectField')->truncate();
        $this->connection->table('T_Option')->truncate();
        
        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $this->feedbackForm = new RecordOfFeedbackForm($this->firm, $form);
        $this->connection->table("FeedbackForm")->insert($this->feedbackForm->toArrayForDbEntry());
        
        $this->feedbackFormResponse["name"] = $this->feedbackFormInput["name"];
        $this->feedbackFormResponse["description"] = $this->feedbackFormInput["description"];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("Form")->truncate();
        
        $this->connection->table('StringField')->truncate();
        $this->connection->table('IntegerField')->truncate();
        $this->connection->table('TextAreaField')->truncate();
        $this->connection->table('AttachmentField')->truncate();
        $this->connection->table('SingleSelectField')->truncate();
        $this->connection->table('MultiSelectField')->truncate();
        $this->connection->table('Section')->truncate();
        
        $this->connection->table('SelectField')->truncate();
        $this->connection->table('T_Option')->truncate();
    }
}
