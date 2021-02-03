-- SET FOREIGN_KEY_CHECKS = 0;

-- REPLACE INTO miktiid_mcp_test.Admin (id, name, email, password, removed)
-- SELECT id, name, email, password, removed FROM miktiid_innovid2020.SysAdmin;

REPLACE INTO miktiid_mcp_test.FileInfo (id, folders, name, size)
SELECT id, folders, name, size FROM miktiid_innovid2020.FileInfo;

REPLACE INTO miktiid_mcp_test.Form (id, name, description)
SELECT id, name, description FROM miktiid_innovid2020.Form;

REPLACE INTO miktiid_mcp_test.FormRecord (Form_id, id)
SELECT Form_id, id FROM miktiid_innovid2020.FormRecord;

REPLACE INTO miktiid_mcp_test.Notification (id, message)
SELECT id, message FROM miktiid_innovid2020.Notification;

REPLACE INTO miktiid_mcp_test.AttachmentField (Form_id, id, name, description, position, mandatory, minimumValue, maximumValue, removed)
SELECT Form_id, id, name, description, position, required, fileCount_minimumValue, fileCount_maximumValue, removed FROM miktiid_innovid2020.AttachmentField;

REPLACE INTO miktiid_mcp_test.IntegerField (Form_id, id, name, description, position, mandatory, minimumValue, maximumValue, removed, defaultValue, placeholder)
SELECT Form_id, id, name, description, position, required, minimumValue, maximumValue, removed, defaultValue, placeholder FROM miktiid_innovid2020.IntegerField;

REPLACE INTO miktiid_mcp_test.SelectField (id, name, description, position, mandatory)
SELECT id, name, description, position, required FROM miktiid_innovid2020.SelectField;

REPLACE INTO miktiid_mcp_test.MultiSelectField (id, removed, minimumValue, maximumValue, SelectField_id, Form_id)
SELECT id, removed, minimumValue, maximumValue, SelectField_id, Form_id FROM miktiid_innovid2020.MultiSelectField;

REPLACE INTO miktiid_mcp_test.SingleSelectField (id, defaultValue, removed, SelectField_id, Form_id)
SELECT id, defaultValue, removed, SelectField_id, Form_id FROM miktiid_innovid2020.SingleSelectField;

REPLACE INTO miktiid_mcp_test.StringField (Form_id, id, name, description, position, mandatory, minimumValue, maximumValue, removed, defaultValue, placeholder)
SELECT Form_id, id, name, description, position, required, minimumValue, maximumValue, removed, defaultValue, placeholder FROM miktiid_innovid2020.StringField;

REPLACE INTO miktiid_mcp_test.TextAreaField (Form_id, id, name, description, position, mandatory, minimumValue, maximumValue, removed, defaultValue, placeholder)
SELECT Form_id, id, name, description, position, required, minimumValue, maximumValue, removed, defaultValue, placeholder FROM miktiid_innovid2020.TextAreaField;

REPLACE INTO miktiid_mcp_test.T_Option (id, name, description, position, removed, SelectField_id)
SELECT id, name, description, position, removed, SelectField_id FROM miktiid_innovid2020.TOption;

REPLACE INTO miktiid_mcp_test.AttachmentFieldRecord (id, removed, FormRecord_id, AttachmentField_id)
SELECT id, removed, FormRecord_id, AttachmentField_id FROM miktiid_innovid2020.AttachmentFieldRecord;

REPLACE INTO miktiid_mcp_test.IntegerFieldRecord (id, removed, FormRecord_id, IntegerField_id, value)
SELECT id, removed, FormRecord_id, IntegerField_id, value FROM miktiid_innovid2020.IntegerFieldRecord;

REPLACE INTO miktiid_mcp_test.MultiSelectFieldRecord (id, removed, FormRecord_id, MultiSelectField_id)
SELECT id, removed, FormRecord_id, MultiSelectField_id FROM miktiid_innovid2020.MultiSelectFieldRecord;

REPLACE INTO miktiid_mcp_test.SingleSelectFieldRecord (id, removed, FormRecord_id, SingleSelectField_id, Option_id)
SELECT id, removed, FormRecord_id, SingleSelectField_id, Option_id FROM miktiid_innovid2020.SingleSelectFieldRecord;

REPLACE INTO miktiid_mcp_test.StringFieldRecord (id, removed, FormRecord_id, StringField_id, value)
SELECT id, removed, FormRecord_id, StringField_id, value FROM miktiid_innovid2020.StringFieldRecord;

REPLACE INTO miktiid_mcp_test.TextAreaFieldRecord (id, removed, FormRecord_id, TextAreaField_id, value)
SELECT id, removed, FormRecord_id, TextAreaField_id, value FROM miktiid_innovid2020.TextAreaFieldRecord;

REPLACE INTO miktiid_mcp_test.AttachedFile (AttachmentFieldRecord_id, id, FileInfo_id, removed)
SELECT AttachmentFieldRecord_id, id, FileInfo_id, removed FROM miktiid_innovid2020.AttachedFile;

REPLACE INTO miktiid_mcp_test.SelectedOption (MultiSelectFieldRecord_id, id, Option_id, removed)
SELECT MultiSelectFieldRecord_id, id, Option_id, removed FROM miktiid_innovid2020.SelectedOption;

REPLACE INTO miktiid_mcp_test.Firm (id, name, identifier, suspended)
SELECT id, name, identifier, suspended FROM miktiid_innovid2020.Incubator;

REPLACE INTO miktiid_mcp_test.Personnel (Firm_id, id, firstName, email, password, phone, joinTime, active, bio)
SELECT Incubator_id, id, name, email, password, phone, joinTime, NOT `removed`, NULL FROM miktiid_innovid2020.Personnel;

REPLACE INTO miktiid_mcp_test.Manager (Firm_id, id, removed, name, email, password, phone, joinTime)
SELECT A.Incubator_id, A.id, A.removed, P.name, P.email, P.password, P.phone, P.joinTime
FROM miktiid_innovid2020.Admin A LEFT JOIN miktiid_innovid2020.Personnel P ON A.Personnel_id = P.id;

REPLACE INTO miktiid_mcp_test.Client (Firm_id, id, firstName, email, password, signupTime, activated, activationCode, activationCodeExpiredTime, resetPasswordCode, resetPasswordCodeExpiredTime)
SELECT Incubator_id, id, name, email, password, signupTime, activated, activationCode, activationCodeExpiredTime, resetPasswordCode, resetPasswordCodeExpiredTime FROM miktiid_innovid2020.Founder;

REPLACE INTO miktiid_mcp_test.FeedbackForm (Firm_id, id, Form_id, removed)
SELECT Incubator_id, id, Form_id, removed FROM miktiid_innovid2020.MentoringFeedbackForm;

REPLACE INTO miktiid_mcp_test.BioForm (Firm_id, id, disabled)
SELECT Incubator_id, id, false FROM miktiid_innovid2020.ProfileForm;

REPLACE INTO miktiid_mcp_test.ClientBio (Client_id, id, BioForm_id, removed)
SELECT Founder_id, id, ProfileForm_id, removed FROM miktiid_innovid2020.Profile;


REPLACE INTO miktiid_mcp_test.Program (Firm_id, id, name, description, published, removed, participantTypes)
SELECT Incubator_id, id, name, description, published, removed, 'team' as participantTypes FROM miktiid_innovid2020.Program;

REPLACE INTO miktiid_mcp_test.Team (Firm_id, id, name, createdTime, Client_idOfCreator)
SELECT Incubator_id, id, name, createdTime, Founder_idOfCreator FROM miktiid_innovid2020.Team;

REPLACE INTO miktiid_mcp_test.WorksheetForm (Firm_id, id, Form_id, removed)
SELECT Incubator_id, id, Form_id, removed FROM miktiid_innovid2020.WorksheetForm;

REPLACE INTO miktiid_mcp_test.ClientFileInfo (Client_id, id, FileInfo_id, removed)
SELECT Founder_id, id, FileInfo_id, removed FROM miktiid_innovid2020.FounderFileInfo;

REPLACE INTO miktiid_mcp_test.ClientNotificationRecipient (Client_id, id, Notification_id, readStatus, notifiedTime)
SELECT FN.Founder_id, FN.id, FN.Notification_id, N.isRead, N.notifiedTime
FROM miktiid_innovid2020.FounderNotification FN LEFT JOIN miktiid_innovid2020.Notification N ON FN.Notification_id = N.id;

REPLACE INTO miktiid_mcp_test.PersonnelFileInfo (Personnel_id, id, FileInfo_id, removed)
SELECT Personnel_id, id, FileInfo_id, removed FROM miktiid_innovid2020.PersonnelFileInfo;

REPLACE INTO miktiid_mcp_test.PersonnelNotificationRecipient (Personnel_id, id, Notification_id, readStatus, notifiedTime)
SELECT FN.Personnel_id, FN.id, FN.Notification_id, N.isRead, N.notifiedTime
FROM miktiid_innovid2020.PersonnelNotification FN LEFT JOIN miktiid_innovid2020.Notification N ON FN.Notification_id = N.id;

REPLACE INTO miktiid_mcp_test.Coordinator (Program_id, id, Personnel_id, active)
SELECT Program_id, id, Personnel_id, NOT `removed` FROM miktiid_innovid2020.Coordinator;

REPLACE INTO miktiid_mcp_test.Consultant (Program_id, id, Personnel_id, active)
SELECT Program_id, id, Personnel_id, NOT `removed` FROM miktiid_innovid2020.Mentor;

REPLACE INTO miktiid_mcp_test.ConsultationSetup (Program_id, id, name, sessionDuration, FeedbackForm_idForParticipant, FeedbackForm_idForConsultant, removed)
SELECT Program_id, id, name, sessionDuration, MentoringFeedbackForm_idForParticipant, MentoringFeedbackForm_idForMentor, removed FROM miktiid_innovid2020.Mentoring;

-- SET FOREIGN_KEY_CHECKS=0;
REPLACE INTO miktiid_mcp_test.Mission (Program_id, id, parent_id, name, description, position, published, WorksheetForm_id)
SELECT Program_id, id, previousMission_id, name, description, position, published, WorksheetForm_id FROM miktiid_innovid2020.Mission;
-- SET FOREIGN_KEY_CHECKS=1;

REPLACE INTO miktiid_mcp_test.Participant (Program_id, id, enrolledTime, active, note)
SELECT Program_id, id, acceptedTime, active, note FROM miktiid_innovid2020.Participant;

REPLACE INTO miktiid_mcp_test.TeamParticipant (Team_id, id, Participant_id)
SELECT Team_id, id, id FROM miktiid_innovid2020.Participant;

REPLACE INTO miktiid_mcp_test.Registrant (Program_id, id, registeredTime, concluded, note)
SELECT Program_id, id, appliedTime, concluded, note FROM miktiid_innovid2020.Registrant;

REPLACE INTO miktiid_mcp_test.TeamRegistrant (Team_id, id, Registrant_id)
SELECT Team_id, id, id FROM miktiid_innovid2020.Registrant;

REPLACE INTO miktiid_mcp_test.RegistrationPhase (Program_id, id, name, removed, startDate, endDate)
SELECT Program_id, id, name, removed, startDate, endDate FROM miktiid_innovid2020.RegistrationPhase;

REPLACE INTO miktiid_mcp_test.ConsultantComment (Consultant_id, id, Comment_id)
SELECT Mentor_id, id, Comment_id FROM miktiid_innovid2020.MentorComment;

REPLACE INTO miktiid_mcp_test.ConsultationRequest (ConsultationSetup_id, id, concluded, startDateTime, endDateTime, status, Participant_id, Consultant_id)
SELECT Mentoring_id, id, concluded, startDateTime, endDateTime, 
CASE
    WHEN status = 'pro' THEN 'proposed'
    WHEN status = 'rej' THEN 'rejected'
    WHEN status = 'can' THEN 'cancelled'
    WHEN status = 'off' THEN 'offered'
    WHEN status = 'sch' THEN 'scheduled'
    ELSE NULL
END, 
Participant_id, Mentor_id 
FROM miktiid_innovid2020.NegotiateSchedule;

REPLACE INTO miktiid_mcp_test.ConsultationSession (ConsultationSetup_id, id, startDateTime, endDateTime, Participant_id, Consultant_id, cancelled)
SELECT Mentoring_id, id, startDateTime, endDateTime, Participant_id, Mentor_id, FALSE FROM miktiid_innovid2020.Schedule;

REPLACE INTO miktiid_mcp_test.ConsultationRequestNotification (ConsultationRequest_id, id, Notification_id)
SELECT NegotiateSchedule_id, id, FounderNotification_id FROM miktiid_innovid2020.FounderNegotiateScheduleNotification;

REPLACE INTO miktiid_mcp_test.ConsultationRequestNotification (ConsultationRequest_id, id, Notification_id)
SELECT NegotiateSchedule_id, id, PersonnelNotification_id FROM miktiid_innovid2020.PersonnelNegotiateScheduleNotification;

REPLACE INTO miktiid_mcp_test.ConsultationSessionNotification (ConsultationSession_id, id, Notification_id)
SELECT Schedule_id, id, FounderNotification_id FROM miktiid_innovid2020.FounderScheduleNotification;

REPLACE INTO miktiid_mcp_test.ConsultationSessionNotification (ConsultationSession_id, id, Notification_id)
SELECT Schedule_id, id, PersonnelNotification_id FROM miktiid_innovid2020.PersonnelScheduleNotification;

REPLACE INTO miktiid_mcp_test.ConsultantFeedback (ConsultationSession_id, id, FormRecord_id)
SELECT Schedule_id, id, FormRecord_id FROM miktiid_innovid2020.MentorMentoringReport;

REPLACE INTO miktiid_mcp_test.ParticipantFeedback (ConsultationSession_id, id, FormRecord_id)
SELECT Schedule_id, id, FormRecord_id FROM miktiid_innovid2020.ParticipantMentoringReport;

REPLACE INTO miktiid_mcp_test.Worksheet (Participant_id, id, parent_id, name, removed, FormRecord_id, Mission_id)
SELECT _a.Participant_id, _b.id, _c.parentId, _b.name, _a.removed, _b.FormRecord_id, _a.Mission_id
FROM miktiid_innovid2020.Journal _a
LEFT JOIN miktiid_innovid2020.Worksheet _b ON _a.Worksheet_id = _b.id
LEFT JOIN (
    SELECT __a.id journalId, __b.id parentId
    FROM miktiid_innovid2020.Journal __a
    LEFT JOIN miktiid_innovid2020.Worksheet __b ON __a.Worksheet_id = __b.id
)_c ON _c.journalId = _a.parentJournal_id;

REPLACE INTO miktiid_mcp_test.LearningMaterial (Mission_id, id, name, content, removed)
SELECT Mission_id, id, name, content, removed FROM miktiid_innovid2020.LearningMaterial;

REPLACE INTO miktiid_mcp_test.Comment (Worksheet_id, id, message, submitTime, removed, parent_id)
SELECT J.Worksheet_id, C.id, C.message, C.submitTime, C.removed, C.parentComment_id 
FROM miktiid_innovid2020.Comment C
LEFT JOIN miktiid_innovid2020.Journal J ON J.id = C.Journal_id;

REPLACE INTO miktiid_mcp_test.CommentNotification (Comment_id, id, Notification_id)
SELECT Comment_id, id, FounderNotification_id FROM miktiid_innovid2020.CommentFounderNotification;

REPLACE INTO miktiid_mcp_test.T_Member (Team_id, id, position, joinTime, active, Client_id, anAdmin)
SELECT Team_id, id, position, joinTime, NOT `removed`, Founder_id, true FROM miktiid_innovid2020.Member;

REPLACE INTO miktiid_mcp_test.TeamFileInfo (Team_id, id, removed, FileInfo_id)
SELECT Team_id, id, removed, FileInfo_id FROM miktiid_innovid2020.TeamFileInfo;

-- SET FOREIGN_KEY_CHECKS = 1;

-- Skipped Data:
-- SysAdmin

-- Truncated Table:
-- LastTeamMembership
-- MemberCandidate
-- MemberCandidateNotification
-- MemberComment
-- ParticipantNotification
-- TeamProfileForm
-- TeamProfile

-- Untransfered Data
-- Mentor.introduction
-- Journal.id, Journal.Worksheet_id, Journal.createdTime, Journal.lastChangedTime, Journal.reviewed
-- Worksheet.removed, Worksheet.Team_id, Worksheet.WorksheetForm_id, Worksheet.createdTime, Worksheet.lastUpdatedTime

