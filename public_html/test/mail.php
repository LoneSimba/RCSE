<?php
require ('../vendor/autoload.php');

use PHPMailer\PHPMailer\SMTP;
use RCSE\Core\Control\Config;

$mail = new PHPMailer\PHPMailer\PHPMailer();
$conf = (new Config())->getConfig('mailer')['smtp'];
$mailAddresses = (new Config())->getConfig("mailer")["addresses"];

$mail->isSMTP();
$mail->SMTPAuth = true;
$mail->SMTPDebug = SMTP::DEBUG_SERVER; //@todo Remove on release
$mail->SMTPKeepAlive = false;
$mail->Host = $conf['smtp-srv'];
$mail->Port = $conf['smtp-prt'];
$mail->Username = $conf['smtp-usr'];
$mail->Password = $conf['smtp-pss'];
$mail->setFrom($mailAddresses['no-reply'], 'NoReply');
$mail->addAddress('siluet-stalker99@yandex.ru', 'LS');
$mail->SMTPSecure = 'ssl';
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
$mail->Subject = 'Test';
$mail->msgHTML("Hello there");

$mail->send();