<?php

namespace esprit\core;

/**
 * An EmailSender that uses PHP's mail() function to send an email. As such, it's not
 * a great implementation for sending HTML emails or a large number of emails in a loop.
 * All of the mail() function's shortcomings apply to this class.
 *
 * @author jbowens
 * @since November 2012
 */
class PhpMailEmailSender implements EmailSender
{

    public function send(Email $email)
    {
        // Extract pertinent data
        $to = $email->getToAsString();
        $from = $email->getFromAsString();
        $subject = $email->getSubject();
        $message = $email->getMessage();

        // Setup the headers
        $headers = array();
        $headers[] = 'Content-type: ' . $email->getContentType() . '; charset=' . $email->getCharset();
        $headers[] = 'From: ' . $from;
        $headers[] = 'Reply-To: ' . $email->getReplyToAsString();
        if( $email->usesCc() )
            $headers[] = 'Cc: ' . $email->getCcAsString();
        if( $email->usesBcc() )
            $headers[] = 'Bcc: ' . $email->getBccAsString();
        $headers[] = 'X-Mailer: PHP/'.phpversion();

        // Send the email
        mail($to, $subject, $message, implode("\r\n", $headers));
    }

}
