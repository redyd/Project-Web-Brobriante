<?php

namespace mail;

require_once __DIR__ . '/../../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class MailSender
{
    const MAIL_ADMIN = "t.soeur@student.helmo.be";
    const NO_REPLY = "no-reply.brobriante@helmo.be";
    const MY_MAIL = "timeosoeur@gmail.com";

    /**
     * Permet d'envoyer un mail.
     *
     * @param string $message Message d'erreur
     * @param string $subject Objet du mail
     * @param string $body Corps du mail
     * @param string $copyTo Mail à mettre en copie
     * @param string $from Envoyeur
     * @param string $from_name Adresse de réponse
     * @param string $to Destinataire
     * @return bool
     */
    public static function sendMail(string &$message, string $subject, string $body, string $copyTo = "", string $from = self::MAIL_ADMIN, string $from_name = self::NO_REPLY, string $to = self::MY_MAIL): bool
    {
        $mail = new PHPMailer(true);
        try {
            $mail->CharSet = 'UTF-8';
            $mail->setFrom($from);
            $mail->addAddress($to);
            $mail->addReplyTo($from_name);
            if (!empty($copyTo)) {
                $mail->addCC($copyTo);
            }
            $mail->isHTML(true);
            $mail->Subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');
            $mail->Body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
            return $mail->send();
        } catch (Exception $e) {
            $message = 'Erreur survenue lors de l\'envoi de l\'email<br>' . $mail->ErrorInfo;
            return false;
        }
    }

}