<?php
declare(strict_types=1);

namespace RCSE\Core\Control;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;

class Mail
{
    private array $conf;
    private PHPMailer $mailer;
    private Config $config;

    public function __construct()
    {
        $this->config = new Config();
        $this->mailer = new PHPMailer();
        $this->conf = $this->config->getConfig('mailer');
        $this->mailer->isSMTP();
        $this->mailer->SMTPAuth = true;
        $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER; //@todo Remove on release
        $this->mailer->Host = $this->conf['smtp-srv'];
        $this->mailer->Port = $this->conf['smtp-prt'];
        $this->mailer->Username = $this->conf['smtp-usr'];
        $this->mailer->Password = $this->conf['smtp-pss'];
    }

}