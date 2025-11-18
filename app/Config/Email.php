<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail = '';
    public string $fromName = '';
    public string $recipients = '';
    public string $protocol = 'smtp';
    public string $mailPath = '/usr/sbin/sendmail';
    public string $SMTPHost = '';
    public string $SMTPUser = '';
    public string $SMTPPass = '';
    public int $SMTPPort = 587;
    public int $SMTPTimeout = 10;
    public bool $SMTPKeepAlive = false;
    public string $SMTPCrypto = 'tls';
    public bool $wordWrap = false;
    public int $wrapChars = 76;
    public string $mailType = 'text';
    public string $charset = 'utf-8';
    public bool $validate = true;
    public int $priority = 3;
    public string $CRLF = "\r\n";
    public string $newline = "\r\n";
    public bool $BCCBatchMode = false;
    public int $BCCBatchSize = 200;
    public bool $DSN = false;

    public function __construct()
    {
        parent::__construct();

        $this->fromEmail  = env('email.fromEmail', '');
        $this->fromName   = env('email.fromName', '');
        $this->protocol   = env('email.protocol', 'smtp');
        $this->SMTPHost   = env('email.SMTPHost', '');
        $this->SMTPUser   = env('email.SMTPUser', '');
        $this->SMTPPass   = env('email.SMTPPass', '');
        $this->SMTPPort   = (int)env('email.SMTPPort', 587);
        $this->SMTPCrypto = env('email.SMTPCrypto', 'tls');
        $this->SMTPTimeout = (int)env('email.SMTPTimeout', 10);
        $this->mailType   = 'text';
        $this->charset    = 'utf-8';
        $this->wordWrap   = false;
        $this->newline = "\r\n";
        $this->CRLF = "\r\n";
    }
}