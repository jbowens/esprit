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

        // TODO: Implement

    }

}
