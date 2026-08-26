<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
| Gmail SMTP
| Gunakan App Password 16 karakter, bukan password akun Gmail biasa.
| Aktifkan 2-Step Verification dahulu di akun Google yang dipakai mengirim.
*/
$config['protocol'] = 'smtp';
$config['smtp_host'] = 'ssl://smtp.gmail.com';
$config['smtp_port'] = 465;
$config['smtp_user'] = 'ISI_DENGAN_EMAIL_GMAIL_ANDA@gmail.com';
$config['smtp_pass'] = 'ISI_DENGAN_GOOGLE_APP_PASSWORD';
$config['smtp_crypto'] = 'ssl';
$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['newline'] = "\r\n";
$config['crlf'] = "\r\n";
$config['wordwrap'] = TRUE;
$config['validate'] = TRUE;
