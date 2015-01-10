<?php

require_once 'lib-core.php';
require 'vendor/PHPMailer/PHPMailerAutoload.php';

class sgmail {
    /**
     * A class to send mail (wrapper around PHPMail).
     */
    public function sendmail($to, $subject, $message) {
        /**
         * Send an email to the given recipient, with the given subject and
         * message.
         */
        global $wsn;
        global $debug;
        try {
            global $smtpCfg;
            $smtpHost = $smtpCfg['servers'];
            $smtpFrom = $smtpCfg['from'];
            $smtpUsername = $smtpCfg['username'];
            $smtpPassword = $smtpCfg['password'];
        } catch (Exception $e) {
            echo "Email not properly configured. Contact the site owner to report this problem. <br />";
            showerror();
            return false;
        }

        $mail = new PHPMailer;
        if($debug) {
            mail->SMTPDebug = 3;
        }

        $mail->isSMTP();
        $mail->Host = $smtpHost;
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUsername;
        $mail->Password = $smtpPassword;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->From = $smtpFrom;
        $mail->FromName = $wsn;
        $mail->addAddress($to);

        $mail->WordWrap = 80;
        $mail->isHTML(true);

        $mail->Subject = $subject;
        $mail->Body    = $message;

        // In case the user's email client can't handle HTML messages, we
        // provide an alternative plain-text body.
        $mail->AltBody = strip_tags($message);

        if(!$mail->send()) {
            echo 'Email could not be sent.';
            if ($debug) {
                echo 'Mailer Error: ' . $mail->ErrorInfo;
            }
            showerror();
            return false;
        }
        return true;
    }
}
