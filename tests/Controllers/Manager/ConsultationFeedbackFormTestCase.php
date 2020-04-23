<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\ {
    Firm\RecordOfConsultationFeedbackForm,
    Shared\RecordOfForm
};

class ConsultationFeedbackFormTestCase extends ManagerTestCase
{
    protected $consultationFeedbackFormUri;
    /**
     *
     * @var RecordOfConsultationFeedbackForm
     */
    protected $consultationFeedbackForm;
    protected $consultationFeedbackFormInput = [
        "name" => 'new consultation feedback form name',
        "description" => 'new consultation feedback form description',
        "stringFields" => [],
        "integerFields" => [],
        "textAreaFields" => [],
        "singleSelectFields" => [],
        "multiSelectFields" => [],
        "attachmentFields" => [],
    ];
    protected $consultationFeedbackFormResponse = [
        "stringFields" => [],
        "integerFields" => [],
        "textAreaFields" => [],
        "singleSelectFields" => [],
        "multiSelectFields" => [],
        "attachmentFields" => [],
    ];


    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationFeedbackFormUri = $this->managerUri . "/consultation-feedback-forms";
        $this->connection->table("ConsultationFeedbackForm")->truncate();
        $this->connection->table("Form")->truncate();
        
        $this->connection->table('StringField')->truncate();
        $this->connection->table('IntegerField')->truncate();
        $this->connection->table('TextAreaField')->truncate();
        $this->connection->table('AttachmentField')->truncate();
        $this->connection->table('SingleSelectField')->truncate();
        $this->connection->table('MultiSelectField')->truncate();
        
        $this->connection->table('SelectField')->truncate();
        $this->connection->table('T_Option')->truncate();
        
        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $this->consultationFeedbackForm = new RecordOfConsultationFeedbackForm($this->firm, $form);
        $this->connection->table("ConsultationFeedbackForm")->insert($this->consultationFeedbackForm->toArrayForDbEntry());
        
        $this->consultationFeedbackFormResponse["name"] = $this->consultationFeedbackFormInput["name"];
        $this->consultationFeedbackFormResponse["description"] = $this->consultationFeedbackFormInput["description"];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("ConsultationFeedbackForm")->truncate();
        $this->connection->table("Form")->truncate();
        
        $this->connection->table('StringField')->truncate();
        $this->connection->table('IntegerField')->truncate();
        $this->connection->table('TextAreaField')->truncate();
        $this->connection->table('AttachmentField')->truncate();
        $this->connection->table('SingleSelectField')->truncate();
        $this->connection->table('MultiSelectField')->truncate();
        
        $this->connection->table('SelectField')->truncate();
        $this->connection->table('T_Option')->truncate();
    }
}
