
-- INSERT INTO bara_mcp.Admin (id, name, email, password, removed)
-- SELECT id, name, email, password, removed FROM bara_bekup.SysAdmin;

INSERT INTO bara_mcp.FileInfo (id, folders, name, size)
SELECT id, folders, name, size FROM bara_bekup.FileInfo;

INSERT INTO bara_mcp.Form (id, name, description)
SELECT id, name, description FROM bara_bekup.Form;

INSERT INTO bara_mcp.FormRecord (Form_id, id)
SELECT Form_id, id FROM bara_bekup.FormRecord;

INSERT INTO bara_mcp.Notification (id, message)
SELECT id, message FROM bara_bekup.Notification;

INSERT INTO bara_mcp.AttachmentField (Form_id, id, name, description, position, mandatory, minimumValue, maximumValue, removed)
SELECT Form_id, id, name, description, position, required, fileCount_minimumValue, fileCount_maximumValue, removed FROM bara_bekup.AttachmentField;

INSERT INTO bara_mcp.IntegerField (Form_id, id, name, description, position, mandatory, minimumValue, maximumValue, removed, defaultValue, placeholder)
SELECT Form_id, id, name, description, position, required, minimumValue, maximumValue, removed, defaultValue, placeholder FROM bara_bekup.IntegerField;

INSERT INTO bara_mcp.SelectField (id, name, description, position, mandatory)
SELECT id, name, description, position, required FROM bara_bekup.SelectField;

INSERT INTO bara_mcp.MultiSelectField (id, removed, minimumValue, maximumValue, SelectField_id, Form_id)
SELECT id, removed, minimumValue, maximumValue, SelectField_id, Form_id FROM bara_bekup.MultiSelectField;

INSERT INTO bara_mcp.SingleSelectField (id, defaultValue, removed, SelectField_id, Form_id)
SELECT id, defaultValue, removed, SelectField_id, Form_id FROM bara_bekup.SingleSelectField;

INSERT INTO bara_mcp.StringField (Form_id, id, name, description, position, mandatory, minimumValue, maximumValue, removed, defaultValue, placeholder)
SELECT Form_id, id, name, description, position, required, minimumValue, maximumValue, removed, defaultValue, placeholder FROM bara_bekup.StringField;

INSERT INTO bara_mcp.TextAreaField (Form_id, id, name, description, position, mandatory, minimumValue, maximumValue, removed, defaultValue, placeholder)
SELECT Form_id, id, name, description, position, required, minimumValue, maximumValue, removed, defaultValue, placeholder FROM bara_bekup.TextAreaField;

INSERT INTO bara_mcp.T_Option (id, name, description, position, removed, SelectField_id)
SELECT id, name, description, position, removed, SelectField_id FROM bara_bekup.TOption;

INSERT INTO bara_mcp.AttachmentFieldRecord (id, removed, FormRecord_id, AttachmentField_id)
SELECT id, removed, FormRecord_id, AttachmentField_id FROM bara_bekup.AttachmentFieldRecord;

INSERT INTO bara_mcp.IntegerFieldRecord (id, removed, FormRecord_id, IntegerField_id, value)
SELECT id, removed, FormRecord_id, IntegerField_id, value FROM bara_bekup.IntegerFieldRecord;

INSERT INTO bara_mcp.MultiSelectFieldRecord (id, removed, FormRecord_id, MultiSelectField_id)
SELECT id, removed, FormRecord_id, MultiSelectField_id FROM bara_bekup.MultiSelectFieldRecord;

INSERT INTO bara_mcp.SingleSelectFieldRecord (id, removed, FormRecord_id, SingleSelectField_id, Option_id)
SELECT id, removed, FormRecord_id, SingleSelectField_id, Option_id FROM bara_bekup.SingleSelectFieldRecord;

INSERT INTO bara_mcp.StringFieldRecord (id, removed, FormRecord_id, StringField_id, value)
SELECT id, removed, FormRecord_id, StringField_id, value FROM bara_bekup.StringFieldRecord;

INSERT INTO bara_mcp.TextAreaFieldRecord (id, removed, FormRecord_id, TextAreaField_id, value)
SELECT id, removed, FormRecord_id, TextAreaField_id, value FROM bara_bekup.TextAreaFieldRecord;

INSERT INTO bara_mcp.AttachedFile (AttachmentFieldRecord_id, id, FileInfo_id, removed)
SELECT AttachmentFieldRecord_id, id, FileInfo_id, removed FROM bara_bekup.AttachedFile;

INSERT INTO bara_mcp.SelectedOption (MultiSelectFieldRecord_id, id, Option_id, removed)
SELECT MultiSelectFieldRecord_id, id, Option_id, removed FROM bara_bekup.SelectedOption;

INSERT INTO bara_mcp.Firm (id, name, identifier, suspended)
SELECT id, name, identifier, suspended FROM bara_bekup.Incubator;

INSERT INTO bara_mcp.Personnel (Firm_id, id, firstName, email, password, phone, joinTime, active, bio)
SELECT Incubator_id, id, name, email, password, phone, joinTime, NOT removed FROM bara_bekup.Personnel;

INSERT INTO bara_mcp.Manager (Firm_id, id, removed, name, email, password, phone, joinTime)
SELECT A.Incubator_id, A.id, A.removed, P.name, P.email, P.password, P.phone, P.joinTime
FROM bara_bekup.Admin A LEFT JOIN bara_bekup.Personnel P ON A.Personnel_id = P.id;

INSERT INTO bara_mcp.Client (Firm_id, id, firstName, email, password, signupTime, activated, activationCode, activationCodeExpiredTime, resetPasswordCode, resetPasswordCodeExpiredTime)
SELECT Incubator_id, id, name, email, password, signupTime, activated, activationCode, activationCodeExpiredTime, resetPasswordCode, resetPasswordCodeExpiredTime FROM bara_bekup.Founder;

INSERT INTO bara_mcp.FeedbackForm (Firm_id, id, Form_id, removed)
SELECT Incubator_id, id, Form_id, removed FROM bara_bekup.MentoringFeedbackForm;

-- INSERT INTO bara_mcp.ProfileForm (Firm_id, id, Form_id)
-- SELECT Incubator_id, id, Form_id FROM bara_bekup.ProfileForm;

INSERT INTO bara_mcp.ProfileForm (Firm_id, id, Form_id)
SELECT Incubator_id, id, Form_id FROM bara_bekup.TeamProfileForm;

INSERT INTO bara_mcp.Program (Firm_id, id, name, description, published, removed, participantTypes)
SELECT Incubator_id, id, name, description, published, removed, 'team' as participantTypes FROM bara_bekup.Program;

INSERT INTO bara_mcp.Team (Firm_id, id, name, createdTime, Client_idOfCreator)
SELECT Incubator_id, id, name, createdTime, Founder_idOfCreator FROM bara_bekup.Team;

INSERT INTO bara_mcp.WorksheetForm (Firm_id, id, Form_id, removed)
SELECT Incubator_id, id, Form_id, removed FROM bara_bekup.WorksheetForm;

INSERT INTO bara_mcp.ClientFileInfo (Client_id, id, FileInfo_id, removed)
SELECT Founder_id, id, FileInfo_id, removed FROM bara_bekup.FounderFileInfo;

INSERT INTO bara_mcp.ClientNotificationRecipient (Client_id, id, Notification_id, readStatus, notifiedTime)
SELECT FN.Founder_id, FN.id, FN.Notification_id, N.isRead, N.notifiedTime
FROM bara_bekup.FounderNotification FN LEFT JOIN bara_bekup.Notification N ON FN.Notification_id = N.id;

INSERT INTO bara_mcp.PersonnelFileInfo (Personnel_id, id, FileInfo_id, removed)
SELECT Personnel_id, id, FileInfo_id, removed FROM bara_bekup.PersonnelFileInfo;

INSERT INTO bara_mcp.PersonnelNotificationRecipient (Personnel_id, id, Notification_id, readStatus, notifiedTime)
SELECT FN.Personnel_id, FN.id, FN.Notification_id, N.isRead, N.notifiedTime
FROM bara_bekup.PersonnelNotification FN LEFT JOIN bara_bekup.Notification N ON FN.Notification_id = N.id;

INSERT INTO bara_mcp.Coordinator (Program_id, id, Personnel_id, active)
SELECT Program_id, id, Personnel_id, NOT removed FROM bara_bekup.Coordinator;

INSERT INTO bara_mcp.Consultant (Program_id, id, Personnel_id, active)
SELECT Program_id, id, Personnel_id, NOT removed FROM bara_bekup.Mentor;

INSERT INTO bara_mcp.ConsultationSetup (Program_id, id, name, sessionDuration, FeedbackForm_idForParticipant, FeedbackForm_idForConsultant, removed)
SELECT Program_id, id, name, sessionDuration, MentoringFeedbackForm_idForParticipant, MentoringFeedbackForm_idForMentor, removed FROM bara_bekup.Mentoring;

SET FOREIGN_KEY_CHECKS=0;
INSERT INTO bara_mcp.Mission (Program_id, id, parent_id, name, description, position, published, WorksheetForm_id)
SELECT Program_id, id, previousMission_id, name, description, position, published, WorksheetForm_id FROM bara_bekup.Mission;
SET FOREIGN_KEY_CHECKS=1;

INSERT INTO bara_mcp.Participant (Program_id, id, enrolledTime, active, note)
SELECT Program_id, id, acceptedTime, active, note FROM bara_bekup.Participant;

INSERT INTO bara_mcp.TeamParticipant (Team_id, id, Participant_id)
SELECT Team_id, id, id FROM bara_bekup.Participant;

INSERT INTO bara_mcp.Registrant (Program_id, id, registeredTime, concluded, note)
SELECT Program_id, id, appliedTime, concluded, note FROM bara_bekup.Registrant;

INSERT INTO bara_mcp.TeamRegistrant (Team_id, id, Registrant_id)
SELECT Team_id, id, id FROM bara_bekup.Registrant;

INSERT INTO bara_mcp.RegistrationPhase (Program_id, id, name, removed, startDate, endDate)
SELECT Program_id, id, name, removed, startDate, endDate FROM bara_bekup.RegistrationPhase;

INSERT INTO bara_mcp.ConsultantComment (Consultant_id, id, Comment_id)
SELECT Mentor_id, id, Comment_id FROM bara_bekup.MentorComment;

INSERT INTO bara_mcp.ConsultationRequest (ConsultationSetup_id, id, concluded, startDateTime, endDateTime, status, Participant_id, Consultant_id)
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
FROM bara_bekup.NegotiateSchedule;

INSERT INTO bara_mcp.ConsultationSession (ConsultationSetup_id, id, startDateTime, endDateTime, Participant_id, Consultant_id, cancelled)
SELECT Mentoring_id, id, startDateTime, endDateTime, Participant_id, Mentor_id, FALSE FROM bara_bekup.Schedule;

INSERT INTO bara_mcp.ConsultationRequestNotification (ConsultationRequest_id, id, Notification_id)
SELECT NegotiateSchedule_id, id, FounderNotification_id FROM bara_bekup.FounderNegotiateScheduleNotification

INSERT INTO bara_mcp.ConsultationRequestNotification (ConsultationRequest_id, id, Notification_id)
SELECT NegotiateSchedule_id, id, PersonnelNotification_id FROM bara_bekup.PersonnelNegotiateScheduleNotification

INSERT INTO bara_mcp.ConsultationSessionNotification (ConsultationSession_id, id, Notification_id)
SELECT Schedule_id, id, FounderNotification_id FROM bara_bekup.FounderScheduleNotification

INSERT INTO bara_mcp.ConsultationSessionNotification (ConsultationSession_id, id, Notification_id)
SELECT Schedule_id, id, PersonnelNotification_id FROM bara_bekup.PersonnelScheduleNotification

INSERT INTO bara_mcp.ConsultantFeedback (ConsultationSession_id, id, FormRecord_id)
SELECT Schedule_id, id, FormRecord_id FROM bara_bekup.MentorMentoringReport

INSERT INTO bara_mcp.ParticipantFeedback (ConsultationSession_id, id, FormRecord_id)
SELECT Schedule_id, id, FormRecord_id FROM bara_bekup.ParticipantMentoringReport

SET FOREIGN_KEY_CHECKS = 0;
INSERT INTO bara_mcp.Worksheet (Participant_id, id, parent_id, name, removed, FormRecord_id, Mission_id)
SELECT _a.Participant_id, _b.id, _c.parentId, _b.name, _a.removed, _b.FormRecord_id, _a.Mission_id
FROM bara_bekup.Journal _a
LEFT JOIN bara_bekup.Worksheet _b ON _a.Worksheet_id = _b.id
LEFT JOIN (
    SELECT __a.id journalId, __b.id parentId
    FROM bara_bekup.Journal __a
    LEFT JOIN bara_bekup.Worksheet __b ON __a.Worksheet_id = __b.id
)_c ON _c.journalId = _a.parentJournal_id
ON DUPLICATE KEY UPDATE FormRecord_id = _b.FormRecord_id;
SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO bara_mcp.LearningMaterial (Mission_id, id, name, content, removed)
SELECT Mission_id, id, name, content, removed FROM bara_bekup.LearningMaterial;

SET FOREIGN_KEY_CHECKS = 0;
INSERT INTO bara_mcp.Comment (Worksheet_id, id, message, submitTime, removed, parent_id)
SELECT Journal_id, id, message, submitTime, removed, parentComment_id FROM bara_bekup.Comment;
SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO bara_mcp.CommentNotification (Comment_id, id, Notification_id)
SELECT Comment_id, id, FounderNotification_id FROM bara_bekup.CommentFounderNotification;

INSERT INTO bara_mcp.T_Member (Team_id, id, position, joinTime, active, Client_id, anAdmin)
SELECT Team_id, id, position, joinTime, NOT `removed`, Founder_id, true FROM bara_bekup.Member;

INSERT INTO bara_mcp.TeamFileInfo (Team_id, id, removed, FileInfo_id)
SELECT Team_id, id, removed, FileInfo_id FROM bara_bekup.TeamFileInfo;

-- Skipped Data:
-- SysAdmin
-- [Founder]ProfileForm

-- Untransfered Data
-- MemberComment
-- MemberCandidate
-- MemberCandidateNotification
-- ParticipantNotification
-- MentorComment
-- TeamProfile
-- [Founder]Profile
-- TeamProfile
-- Mentor.introduction
-- Journal.id, Journal.Worksheet_id, Journal.createdTime, Journal.lastChangedTime, Journal.reviewed
-- Worksheet.removed, Worksheet.Team_id, Worksheet.WorksheetForm_id, Worksheet.createdTime, Worksheet.lastUpdatedTime

