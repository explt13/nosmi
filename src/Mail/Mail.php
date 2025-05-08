<?php

namespace Explt13\Nosmi\Mail;

use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Interfaces\MailInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mail implements MailInterface
{
    private bool $recipient_is_added = false;
    private bool $with_empty_content = false;
    private array $errors = [];
    private ConfigInterface $config;
    protected $mail;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
        $this->mail = new PHPMailer(true);
        $this->setMailer();                                      // Send using SMTP
        $this->mail->SMTPAuth   = true;                             // Enable SMTP authentication
        $this->mail->SMTPSecure = $this->setEncryption();   // Enable implicit TLS encryption
        $this->mail->Host       = $this->config->get("MAIL_HOST");        // Set the SMTP server to send through e.g smtp.gmail.com
        $this->mail->Port       = $this->config->get("MAIL_PORT");        // TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        $this->mail->Username   = $this->config->get("MAIL_USERNAME");    // SMTP username e.g bar@gmail.com
        $this->mail->Password   = $this->config->get("MAIL_PASSWORD");    // SMTP password
        $this->mail->setFrom($this->config->get("MAIL_FROM_ADDRESS"), $this->config->get("MAIL_FROM_NAME"));
        $this->mail->addReplyTo($this->config->get("MAIL_REPLY_TO"));
    }

    public function withSubject(string $subject): static
    {
        $this->mail->Subject = $subject;
        return $this;
    }

    public function withHtml(string $html, ?string $is_path = null): static
    {
        $this->mail->isHTML(true);                                        //Set email format to HTML
        if ($is_path) {
            ob_start();
            require $html;
            $html = ob_get_clean();
        }
        $this->mail->Body = $html;
        return $this;
    }

    public function withPlain(string $plain, ?string $is_path = null): static
    {
        if ($is_path) {
            ob_start();
            require $plain;
            $plain = ob_get_clean();
        }
        $this->mail->Body = $plain;
        return $this;
    }

    public function withAlt(string $alt, ?string $is_path = null): static
    {
        if ($is_path) {
            ob_start();
            require $alt;
            $alt = ob_get_clean();
        }
        $this->mail->AltBody = $alt;
        return $this;
    }

    public function withVerboseDebug(): static
    {
        $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;   
        return $this;
    }

    public function withRecipient(string $recipient_email): static
    {
        $this->recipient_is_added = true;
        $this->mail->addAddress($recipient_email);
        return $this;
    }

    public function withRecipients(array $recipient_emails): static
    {
        $this->recipient_is_added = true;
        foreach($recipient_emails as $email) {
            $this->mail->addAddress($email);
        }
        return $this;
    }

    public function allowEmptyContent(): static
    {
        $this->with_empty_content = true;
        return $this;
    }

    public function send(): void
    {
        $this->validate();
        $this->mail->send();
    }

    private function setMailer(): void
    {
        $mailer = $this->config->get('MAILER');
        switch ($mailer) {
            case 'smtp':
                $this->mail->isSMTP();
                break;
            case 'mail':
                $this->mail->isMail();
                break;
            case 'qmail':
                $this->mail->isQmail();
                break;
            case 'sendmail':
                $this->mail->isSendmail();
                break;
            default:
                $this->errors[] = 'Undefined mailer type: ' . $mailer;
        }
    }

    private function setEncryption(): string
    {
        $encryption = $this->config->get('MAIL_ENCRYPTION');
        if ($encryption === 'tls') {
            return PHPMailer::ENCRYPTION_STARTTLS;
        }
        if ($encryption === 'ssl') {
            return PHPMailer::ENCRYPTION_SMTPS;
        }
        $this->errors[] = "Undefined mail encryption: $encryption";
        return "";
    }

    private function validate(): void
    {
        if (!$this->with_empty_content && empty($this->mail->Subject)) {
            $this->errors[] = "Mail subject is not set, use Mail::withSubject to set";
        }
        if (!$this->with_empty_content && empty($this->mail->Body)) {
            $this->errors[] = "Mail body is not set, use Mail::withHtml or Mail::withPlain to set";
        }
        if (is_null($this->mail->Username))
        {
            $this->errors[] = "Some mail config parameters are not set, check .env or any other config file";
        }
        if (is_null($this->mail->Password))
        {
            $this->errors[] = "Some mail config parameters are not set, check .env or any other config file";
        }

        if ($this->recipient_is_added === false) {
            $this->errors[] = "Mail recipient(s) is(are) not set, use Mail::withRecipient or Mail::withRecipients to set";
        }

        if (!empty($this->errors)) {
            throw new \RuntimeException(
                sprintf("Errors has occured while sending mail.\n Errors:\n %s", implode(PHP_EOL, $this->errors))
            );
        }
    }

    

}