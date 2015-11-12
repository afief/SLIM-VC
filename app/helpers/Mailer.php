<?php

namespace helpers;

use Core;

class Mailer {

	private $app;
	private $mail;

	function __construct() {
		$this->app = Core::coreInstance();

		$this->app->loadLibrary("class.phpmailer");
		$this->app->loadLibrary("class.smtp");

		$this->mail             = new \PHPMailer();
		$this->mail->IsSMTP();
		$this->mail->Host       = SMTP_HOST; 
		$this->mail->SMTPAuth   = true;                
		$this->mail->Port       = SMTP_PORT;                   
		$this->mail->Username   = SMTP_USER; 
		$this->mail->Password   = SMTP_PASS;       
	}

	function sendRegisterNotification($email, $username, $key) {
		$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
		$body = '
		<!DOCTYPE html>
		<html>
		<head>

		</head>
		<body style="font-family: verdana; max-width: 400px; margin: 0px auto;">

			<div style="font-size: large; text-align: center; margin: 20px 0px;	padding: 20px 0px; border-bottom: solid 1px #444;">
				Halo ' . $username . ', selamat datang.
			</div>
			<div style="font-size: 0.9em; line-height: 1.5em;">
				Terimakasih telah bergabung dengan kami. Untuk dapat menggunakan akun di ESQ Virtual Training, Anda terlebih dahulu harus memverifikasi email dengan memasukkan kode berikut ini kedalam kotak verifikasi yang disediakan oleh aplikasi.
				<br>
				<br>
				<b> ' . $key . '</b>
				<br>
				<br>
				<br>
				<br>
				Salam Hangat Dari Kami,<br>
				<b>ESQ Virtual Training</b>
			</div>

		</body>
		</html>';

		$this->mail->SetFrom('noreply@'.$host, 'noreply@'.$host);
		$this->mail->AddReplyTo(SMTP_USER);

		$this->mail->Subject    = "ESQ Virtual Training - Verifikasi Email";
		$this->mail->AltBody    = "Halo " . $username . ", selamat datang. \r\n Terimakasih telah bergabung dengan kami. Untuk dapat menggunakan akun di ESQ Virtual Training, terlebih dahulu harus verifikasi email dengan klik berikut ini. <a href='" . BASE_URL . "/#/confirm/" . $username . '/' . $key . "'>Verifikasi Akun</a>";

		$this->mail->MsgHTML($body);
		$this->mail->AddAddress($email, $email);

		return $this->mail->Send();
	}
}

?>