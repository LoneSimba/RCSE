<?php
declare(strict_types=1);

namespace RCSE\Core\Control;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;

class Mail
{
    private array $conf;
    private bool $isMailingList;

    private PHPMailer $mailer;
    private Log $log;

    public function __construct(Log $log, array $from, array $recipients, string $replyTo = "", bool $mailingList = false)
    {
        $this->mailer = new PHPMailer();
        $this->log = $log;
        $this->conf = (new Config())->getConfig('mailer')['smtp'];
        $this->isMailingList = $mailingList;
        $this->configure($from, $recipients, $replyTo);
    }

    public function setRecipients(array $recipients) : void
    {
        foreach ($recipients as $row)
        {
            try
            {
                $this->mailer->addAddress($row[0], $row[1]);
            }
            catch (Exception $e)
            {
                $this->log->log(Log::NOTICE, "Skipped invalid or already added mail address: ". $row[0], self::class);
                continue;
            }
        }
    }

    public function setMessage(string $subject, string $contents) : void
    {
        $this->mailer->Subject = $subject;
        $this->mailer->msgHTML($contents);
    }

    public function addAttachments()
    {}

    public function send() : bool
    {
        try
        {
            return $this->mailer->send();
        }
        catch (Exception $e)
        {
            $this->log->log(Log::ERROR, "Failed to send mail message(s)!", self::class);
            $this->mailer->getSMTPInstance()->reset();
            return false;
        }
    }

    private function configure(array $from, array $recipients, string $replyTo) : void
    {
        $this->mailer->isSMTP();
        $this->mailer->SMTPAuth = true;
        $this->mailer->SMTPDebug = SMTP::DEBUG_OFF; //@todo Remove on release
        $this->mailer->SMTPKeepAlive = $this->isMailingList;
        $this->mailer->Host = $this->conf['smtp-srv'];
        $this->mailer->Port = $this->conf['smtp-prt'];
        $this->mailer->Username = $this->conf['smtp-usr'];
        $this->mailer->Password = $this->conf['smtp-pss'];

        try
        {
            $this->mailer->setFrom($from[0], $from[1]);
        }
        catch (Exception $e)
        {
            $this->log->log(Log::ERROR, "Unable to use mail address: ". $from[0], self::class);
            throw $e; //@todo replace with MailAddressException
        }

        if (!empty($replyTo))
        {
            $this->mailer->addReplyTo($replyTo);
        }

        $this->setRecipients($recipients);
    }

}