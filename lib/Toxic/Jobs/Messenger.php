<?php

namespace Toxic\Jobs;

class Messenger {

    public static function sendMessage(string $message) {
        // Create the Transport
        $transport = (new \Swift_SmtpTransport(
                $_SERVER['smtp.host'], $_SERVER['smtp.port'], $_SERVER['smtp.security']))
                ->setUsername($_SERVER['smtp.username'])
                ->setPassword($_SERVER['smtp.password']);

        // Create the Mailer using your created Transport
        $mailer = new \Swift_Mailer($transport);

        // Create a message
        $message = (new \Swift_Message('new jobs'))
                ->setFrom([$_SERVER['smtp.username'] => 'JobsBot'])
                ->setTo($_SERVER['smtp.username'])
                ->setBody($message, "text/plain");

        // Send the message
        return $mailer->send($message);
    }

}
