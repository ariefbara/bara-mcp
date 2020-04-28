<?php

namespace Client\Domain\Event;

use Resources\ {
    Application\Listener\CanBeMailedEvent,
    Domain\Model\Mail,
    Domain\Model\Mail\Recipient
};

class ClientActivationCodeGenerated implements CanBeMailedEvent
{

    const EVENT_NAME = "ClientActivationCodeGenerated";

    protected $clientName, $clientEmailAddress, $activationCode;

    function __construct(string $clientName, string $clientEmailAddress, string $activationCode)
    {
        $this->clientName = $clientName;
        $this->clientEmailAddress = $clientEmailAddress;
        $this->activationCode = $activationCode;
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

    public function getMail(): Mail
    {
//        $baseUri = $_SERVER['SERVER_NAME'];
        $baseUri = "https://innov.id";
        $subject = "bara-MCP account activation";
        $body = <<<_BODY_MESSAGE
this basic message will be shown if client cannot render html mail
$baseUri/activate-client-account/{$this->clientEmailAddress}/{$this->activationCode}
_BODY_MESSAGE;
        $alternativeBody = <<<_ALTERNATIVE_BODY_MESSAGE
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>bara-MCP Email Notification</title>
    </head>

    <body>

        <table style="margin-top:50px;" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">

            <!--HEADER -->

            <tbody><tr>
                    <td align="center">
                        <table class="col-600" width="600" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tbody><tr>
                                    <td align="center" valign="top" bgcolor="#249c90" style="background-size:cover; background-position:top;height=" 400""="">
                                        <table class="col-600" width="600" height="400" border="0" align="center" cellpadding="0" cellspacing="0">

                                            <tbody><tr>
                                                    <td height="40"></td>
                                                </tr>


                                                <tr>
                                                    <td align="center" style="line-height: 0px;">
                                                    </td>
                                                </tr>



                                                <tr>
                                                    <td align="center" style="font-family: 'Raleway', sans-serif; font-size:37px; color:#ffffff; line-height:24px; font-weight: bold; letter-spacing: 5px;">
                                                        Account <span style="font-family: 'Raleway', sans-serif; font-size:37px; color:#ffffff; line-height:39px; font-weight: 300; letter-spacing: 5px;">Activation</span>
                                                    </td>
                                                </tr>





                                                <tr>
                                                    <td align="center" style="font-family: 'Lato', sans-serif; font-size:15px; color:#ffffff; line-height:24px; font-weight: 300;">
                                                        Now you will recive Email everytime you register from the web
                                                    </td>
                                                </tr>


                                                <tr>
                                                    <td height="50"></td>
                                                </tr>
                                            </tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>
                    </td>
                </tr>


                <!-- END HEADERR -->


                <!-- START SHOWCASE -->

                <tr>
                    <td align="center">
                        <table class="col-600" width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="margin-left:20px; margin-right:20px; border-left: 1px solid #dbd9d9; border-right: 1px solid #dbd9d9; border-bottom: 1px solid #dbd9d9;padding-bottom:20px">
                            <tbody><tr>
                                    <td height="35"></td>
                                </tr>

                                <tr>
                                    <td align="center" style="font-family: 'Raleway', sans-serif; font-size:22px; font-weight: bold; color:#333;">Klik The Link Below</td>
                                </tr>

                                <tr>
                                    <td height="10"></td>
                                </tr>


                                <tr>
                                    <td align="center" style="font-family: 'Lato', sans-serif; font-size:14px; color:#757575; line-height:24px; font-weight: 300;">
                                        $baseUri/activate-client-account/{$this->clientEmailAddress}/{$this->activationCode}
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </td>
                </tr>
        </table>
    </body>
</html>	
_ALTERNATIVE_BODY_MESSAGE;

        $recipient = new Recipient($this->clientEmailAddress, $this->clientName);
        return new Mail($subject, $body, $alternativeBody, $recipient);
    }
}
