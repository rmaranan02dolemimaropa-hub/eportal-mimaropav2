<?php

namespace App\Libraries;

use Config\Services;

class Mailer
{
    protected $email;


    public function __construct()
    {
        $this->email = Services::email();
    }


    public function send($to, $subject, $message)
    {

        $this->email->clear(true);


        $this->email->setFrom(
            config('Email')->fromEmail,
            config('Email')->fromName
        );


        $this->email->setTo($to);


        // No Reply
        $this->email->setReplyTo(
            'noreply.dolemimaropa@gmail.com',
            'DOLE MIMAROPA E-Portal (No Reply)'
        );


        $this->email->setSubject($subject);


        $this->email->setMessage($message);


        if($this->email->send())
        {
            return true;
        }


        log_message(
            'error',
            $this->email->printDebugger(['headers'])
        );


        return false;
    }
}