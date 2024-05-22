<?php

namespace App\Enums;

enum TriggerType: string
{
    case HTTP_STATUS_CODE = 'http_status_code';
    case LATENCY = 'latency';
    case SSL = 'ssl';
    case MIXED_CONTENT = 'mixed_content';
    case CERTIFICATE_EXPIRY = 'certificate_expiry';
    case PORT = 'port';
    case KEYWORD = 'keyword';
    case HTTP_CONTENT = 'http_content';
    case DNS = 'dns';
    case SMTP = 'smtp';
    case POP3 = 'pop3';
    case IMAP = 'imap';
    case TCP = 'tcp';
    case UDP = 'udp';
    case PING = 'ping';
    case SSH = 'ssh';
    case SNMP = 'snmp';
    case FTP = 'ftp';
    case SFTP = 'sftp';
    case RSYNC = 'rsync';
    case TELNET = 'telnet';
    case CUSTOM = 'custom';

    public function getLabel(): string
    {
        return match ($this) {
            self::HTTP_STATUS_CODE => 'HTTP Status Code',
            self::LATENCY => 'Latency',
            self::SSL => 'SSL',
            self::MIXED_CONTENT => 'Mixed Content',
            self::CERTIFICATE_EXPIRY => 'Certificate Expiry',
            self::PORT => 'Port',
            self::KEYWORD => 'Keyword',
            self::HTTP_CONTENT => 'HTTP Content',
            self::DNS => 'DNS',
            self::SMTP => 'SMTP',
            self::POP3 => 'POP3',
            self::IMAP => 'IMAP',
            self::TCP => 'TCP',
            self::UDP => 'UDP',
            self::PING => 'Ping',
            self::SSH => 'SSH',
            self::SNMP => 'SNMP',
            self::FTP => 'FTP',
            self::SFTP => 'SFTP',
            self::RSYNC => 'Rsync',
            self::TELNET => 'Telnet',
            self::CUSTOM => 'Custom',
        };
    }
}
