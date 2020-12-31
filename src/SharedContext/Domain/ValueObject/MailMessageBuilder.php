<?php

namespace SharedContext\Domain\ValueObject;

class MailMessageBuilder
{

    const CONSULTATION_REQUESTED = 11;
    const CONSULTATION_SCHEDULE_CHANGED = 12;
    const CONSULTATION_CANCELLED = 13;
    const CONSULTATION_REJECTED = 14;
    const CONSULTATION_ACCEPTED_BY_MENTOR = 15;
    const CONSULTATION_ACCEPTED_BY_PARTICIPANT = 16;
    const MEETING_CREATED = 21;
    const MEETING_INVITATION_SENT = 22;
    const MEETING_INVITATION_CANCELLED = 23;
    const MEETING_SCHEDULE_CHANGED = 24;

    public static function buildAccountActivationMailMessage(?string $domain, ?string $urlPath, ?string $logoPath): MailMessage
    {
        $subject = "Activate Account";
        $greetings = "Hi";
        $mainMessage = [
            "Your new account is almost ready.", 
            "Visit the following link to activate your account.", 
        ];
        return new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);
    }
    
    public static function buildAccountResetPasswordMailMessage(?string $domain, ?string $urlPath, ?string $logoPath): MailMessage
    {
        $subject = "Your Account Recovery Is Ready";
        $greetings = "Hi";
        $mainMessage = [
            "We received a request to reset your password.",
            "Visit the following link to change your password.",
            "",
            "Ignore this message if you never made this request.",
        ];
        return new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);
    }

    public static function buildConsultationMailMessageForMentor(
            int $state, ?string $participantName, ?string $timeDescription, ?string $media, ?string $location,
            ?string $domain, ?string $urlPath, ?string $logoPath): MailMessage
    {
        switch ($state) {
            case self::CONSULTATION_REQUESTED:
                $subject = "New Consultation Request";
                $introductionMessage = "Participant requested new consultation";
                break;
            case self::CONSULTATION_SCHEDULE_CHANGED:
                $subject = "Consultation Request Schedule Changed";
                $introductionMessage = "Participant changed consultation request schedule";
                break;
            case self::CONSULTATION_CANCELLED:
                $subject = "Consultation Request Cancelled";
                $introductionMessage = "Participant cancelled consultation request";
                break;
            case self::CONSULTATION_ACCEPTED_BY_MENTOR:
                $subject = "Consultation Scheduled";
                $introductionMessage = "You accepted consultation schedule";
                break;
            case self::CONSULTATION_ACCEPTED_BY_PARTICIPANT:
                $subject = "Consultation Scheduled";
                $introductionMessage = "Participant accepted consultation schedule suggestion";
                break;
            default:
                break;
        }

        $greetings = "Hi";
        $mainMessage = [
            $introductionMessage,
            "participant: {$participantName}",
            "schedule: {$timeDescription}",
            "media: {$media}",
            "location: {$location}",
        ];

        return new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath, $showLink = true);
    }

    public static function buildConsultationMailMessageForTeamMember(
            int $state, ?string $mentorName, ?string $memberName, ?string $teamName, ?string $timeDescription,
            ?string $media, ?string $location, ?string $domain, ?string $urlPath, ?string $logoPath): MailMessage
    {
        switch ($state) {
            case self::CONSULTATION_REQUESTED:
                $subject = "New Consultation Request";
                $introductionMessage = "Partner {$memberName} requested new consultation";
                break;
            case self::CONSULTATION_SCHEDULE_CHANGED:
                $subject = "Consultation Request Schedule Changed";
                $introductionMessage = "Partner {$memberName} changed consultation request schedule";
                break;
            case self::CONSULTATION_CANCELLED:
                $subject = "Consultation Request Cancelled";
                $introductionMessage = "Partner {$memberName} cancelled consultation request";
                break;
            case self::CONSULTATION_ACCEPTED_BY_MENTOR:
                $subject = "Consultation Scheduled";
                $introductionMessage = "Mentor accepted consultation request from your team";
                break;
            case self::CONSULTATION_ACCEPTED_BY_PARTICIPANT:
                $subject = "Consultation Scheduled";
                $introductionMessage = "Your team has scheduled a consultation";
                break;
            default:
                break;
        }

        $greetings = "Hi";
        $mainMessage = [
            $introductionMessage,
            "mentor: {$mentorName}",
            "team: {$teamName}",
            "schedule: {$timeDescription}",
            "media: {$media}",
            "location: {$location}",
        ];
        return new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath, $showLink = false);
    }

    public static function buildConsultationMailMessageForParticipant(
            int $state, ?string $mentorName, ?string $timeDescription, ?string $media, ?string $location,
            ?string $domain, ?string $urlPath, ?string $logoPath): MailMessage
    {
        switch ($state) {
            case self::CONSULTATION_SCHEDULE_CHANGED:
                $subject = "Consultation Request Schedule Changed";
                $introductionMessage = "Mentor offered consultation schedule";
                break;
            case self::CONSULTATION_REJECTED:
                $subject = "Consultation Request Rejected";
                $introductionMessage = "Mentor rejected consultation request";
                break;
            case self::CONSULTATION_ACCEPTED_BY_MENTOR:
                $subject = "Consultation Scheduled";
                $introductionMessage = "Mentor accepted consultation request";
                break;
            case self::CONSULTATION_ACCEPTED_BY_PARTICIPANT:
                $subject = "Consultation Scheduled";
                $introductionMessage = "You accepted consultation offered by mentor";
                break;
            default:
                break;
        }

        $greetings = "Hi";
        $mainMessage = [
            $introductionMessage,
            "mentor: {$mentorName}",
            "schedule: {$timeDescription}",
            "media: {$media}",
            "location: {$location}",
        ];
        return new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath, $showLink = true);
    }
    
    public static function buildConsultationMailMessageForCoordinator(
            ?string $mentorName, ?string $participantName, ?string $timeDescription, ?string $media, ?string $location,
            ?string $domain, ?string $urlPath, ?string $logoPath): MailMessage
    {
        $subject = "Consultation Scheduled";
        $greetings = "Hi";
        $mainMessage = [
            "A new consultation session scheduled",
            "participant: {$participantName}",
            "mentor: {$mentorName}",
            "schedule: {$timeDescription}",
            "media: {$media}",
            "location: {$location}",
        ];
        return new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath, $showLink = false);
    }

    public static function buildMeetingMailMessage(
            int $state, ?string $meetingType, ?string $meetingName, ?string $meetingDescription,
            ?string $timeDescription, ?string $location, ?string $domain, ?string $urlPath, ?string $logoPath): MailMessage
    {
        switch ($state) {
            case self::MEETING_CREATED:
                $subject = "New Meeting Schedule";
                $introductionMessage = "You or your partner scheduled a new meeting";
                break;
            case self::MEETING_INVITATION_SENT:
                $subject = "Meeting Invitation";
                $introductionMessage = "You are invited to a meeting";
                break;
            case self::MEETING_INVITATION_CANCELLED:
                $subject = "Meeting Invitation Cancelled";
                $introductionMessage = "Your meeting invitation has been cancelled";
                break;
            case self::MEETING_SCHEDULE_CHANGED:
                $subject = "Meeting Schedule Changed";
                $introductionMessage = "Meeting schedule has been changed";
                break;
            default:
                break;
        }
        $greetings = "Hi";
        $mainMessage = [
            $introductionMessage,
            "type: {$meetingType}",
            "meeting: {$meetingName} - {$meetingDescription}",
            "schedule: {$timeDescription}",
            "location: {$location}",
        ];
        return new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath, $showLink = false);
    }

    public static function buildWorksheetCommentMailMessageForParticipant(
            ?string $mentorName, ?string $missionName, ?string $worksheetName, ?string $message, ?string $domain,
            ?string $urlPath, ?string $logoPath): MailMessage
    {
        $subject = "New Comment";
        $greetings = "Hi";
        $mainMessage = [
            "Mentor commented on your worksheet",
            "mentor: {$mentorName}",
            "worksheet: {$missionName} - {$worksheetName}",
            "message: {$message}",
        ];
        return new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath, $showLink = false);
    }

    public static function buildWorksheetCommentMailMessageForMentor(
            ?string $participantName, ?string $missionName, ?string $worksheetName, ?string $message, ?string $domain,
            ?string $urlPath, ?string $logoPath): MailMessage
    {
        $subject = "Comment Replied";
        $greetings = "Hi";
        $mainMessage = [
            "Participant replied to your comment",
            "participant: {$participantName}",
            "worksheet: {$missionName} - {$worksheetName}",
            "message: {$message}",
        ];
        return new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath, $showLink = false);
    }

}
