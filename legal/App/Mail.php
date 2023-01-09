<?php

namespace App;

use App\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Flash;

/**
 * Mail
 *
 * PHP version 7.0
 */
class Mail
{

    /**
     * Send a message
     *
     * @param string $to Recipient
     * @param string $subject Subject
     * @param string $text Text-only content of the message
     * @param string $html HTML content of the message
     *
     * @return mixed
     */
    public static function send($to, $name, $subject, $text, $html)
    {
      $mail = new PHPMailer(true);
      $mail->CharSet = "UTF-8";

      try {
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = Config::HOST_SMTP;                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = Config::USER_MAIL;                     //SMTP username
        $mail->Password   = Config::PASS_MAIL;                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    
        //Recipients
        $mail->setFrom('contactenos@liganatacionbogota.com', 'Sistema de Inscripciones Torneos');
        $mail->addAddress($to, $name);     //Add a recipient se pueden agregar más correos con más linesas de estas        
        $mail->addReplyTo('info@ecoapplet.co', 'Information');
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');
    
        //Attachments
        //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
    
        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $html;     //'This is the HTML message body <b>in bold!</b>';
        $mail->AltBody = $text;     //'This is the body in plain text for non-HTML mail clients';
    
        $mail->send();
        Flash::addMessage('Un mensaje ha sido enviado a su correo', Flash::INFO);
      } catch (Exception $e) {
        Flash::addMessage('El mensaje no pudo ser enviado. Error: {$mail->ErrorInfo}', Flash::WARNING);
      }   
        
    }
}