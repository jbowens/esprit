<?php

namespace esprit\core;

/**
 * An interface for an email sender.
 */
interface EmailSender
{

    /**
     * Sends the given email.
     */
    public function send(Email $email);

}
