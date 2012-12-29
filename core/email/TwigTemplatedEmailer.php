<?php

namespace esprit\core\email;

/**
 * A class designed for sending emails based on Twig templates. It can use any
 * EmailSender implementation to actually send the email.
 *
 * @author jbowens
 * @since December 2012
 */
class TwigTemplatedEmailer
{

    /* The default email sender is the PHP one, since it's guaranteed on all
     * installations. */
    static protected $defaultSender = new PhpMailEmailSender();

    /* The EmailSender implementation that handles the logicistics of actually
     * sending an email through the smtp servers. */
    protected $emailSender = static::$defaultSender;

    /**
     * Sets the EmailSender implementation to the given EmailSender object.
     *
     * @param $newEmailSender  the new EmailSender implementation to use
     */
    public function setEmailSender( EmailSender $newEmailSender )
    {
        $this->emailSender = $newEmailSender;
    }

}
