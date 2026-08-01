<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Log;
use Mail;

class EmailController extends Controller
{

	public function sendRegistrationEmail($member, $password){
		$email_template = 'emails.register';
		$email = $member->email;
		$subject = 'Registration Has Been Completed';
		$array_obj = array('email'=>$member->email, 'name'=>$member->firstname.' '.$member->lastname, 'username'=>$member->username, 'password'=>$password);
		$this->sendEmail($email_template, $email, $subject, $array_obj);
	}

	public function sendForgotpassword($email, $username, $password){
		$email_template = 'emails.forget-password';
		$email = $email;
		$subject = '['.$username.'] Password Reset Successful!';
		$array_obj = array('email'=>$email, 'username'=>$username, 'password'=>$password);
		$this->sendEmail($email_template, $email, $subject, $array_obj);
	}
	
	public function sendOTPMaster($subject, $email, $member, $otp){
		$email_template = 'emails.otp-master';
		$email = $email;
		$array_obj = array('email'=>$email, 'otp_type' => $subject, 'user'=>($member->firstname.' '.$member->lastname.' #'.$member->username), 'otp'=>$otp);
		$this->sendEmail($email_template, $email, $subject, $array_obj);
	}
	
	/*=============================================================================================================================================================*/

	private function sendEmail($email_template, $email, $subject, $array_obj)
	{
		Mail::send($email_template, $array_obj, function($message) use ($email, $subject)
        {
			$message->subject($subject);
			$message->to($email);
        });
	}
}
