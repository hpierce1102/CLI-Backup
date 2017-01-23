<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backup\Notifier;

class EmailNotifier implements NotifierInterface
{
    /** @var  String */
    protected $from;

    /** @var  String[] */
    protected $emails;

    /** @var \Swift_Mailer */
    protected $mailer;

    public function __construct($from, $emails, $smtpHost, $smtpPort, $smtpUserName, $smtpPassword, $smtpEncryption = null)
    {
        $this->from = $from;
        $this->emails = $emails;
        $transport = \Swift_SmtpTransport::newInstance($smtpHost, $smtpPort, $smtpEncryption)
            ->setUsername($smtpUserName)
            ->setPassword($smtpPassword);

        $this->mailer = \Swift_Mailer::newInstance($transport);
    }


    public static function getName()
    {
        return "EmailNotifier";
    }

    public function sendNotification(String $command, Bool $success, String $note)
    {
        if($success){
            $subject = sprintf('Backup-CLI - %s succeeded', $command);
            $body = "Backup-CLI succeeded during execution.\n";
        } else {
            $subject = sprintf('[ACTION-REQUIRED] Backup-CLI - %s FAILED', $command);
            $body = "Backup-CLI CRASHED during execution.\n";
        }

        $body .= $note;

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->from)
            ->setTo($this->emails)
            ->setBody($body);

        $this->mailer->send($message);
    }

    public static function initFromConfig($config)
    {
        $emailNotifier = $config['Notifier']['EmailNotifier'];

        $from = $emailNotifier['fromAddress'];
        $emails = $emailNotifier['toAddresses'];
        $smtpHost = $emailNotifier['SMTPHost'];
        $smtpPort = $emailNotifier['SMTPPort'];
        $smtpEncryption = $emailNotifier['SMTPEncryption'] ?? null;
        $smtpUserName = $emailNotifier['SMTPUserName'];
        $smtpPassword = $emailNotifier['SMTPPassword'];

        return new static($from, $emails, $smtpHost, $smtpPort, $smtpUserName, $smtpPassword, $smtpEncryption);
    }
}