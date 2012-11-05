<?php

namespace esprit\core;

/**
 * A class that represents an email. An object of this type must be given to an 
 * EmailSender instance to send an email.
 *
 * @author jbowens
 * @since November 2012
 */
class Email
{

    /* An array of recipients */
    protected $to = array();

    /* An array of CC'd emails */
    protected $cc = array();

    /* An array of BBC'd emails */
    protected $bcc = array();

    /* The from address of the email */
    protected $from;

    /* The reply-to address for the email. */
    protected $replyTo;

    /**
     * Adds a recipient to the email.
     *
     * @param  $recipient an EmailAddress to send this email to
     */
    public function addRecipient( EmailAddress $recipient )
    {
        array_push( $to, $recipient );
    }

    /**
     * Adds an email to thie list of CC'd addresses.
     *
     * @param $address  an EmailAddress to CC on this email
     */
    public function addCarbonCopy( EmailAddress $ccAddress )
    {
        array_push( $cc, $ccAddress );
    }

    /**
     * Adds an email to the list of BCC'd addresses.
     *
     * @param $address  an EmailAddress to BCC on this email
     */
    public function addBlindCarbonCopy( EmailAddress $bccAddress )
    {
        array_push( $bcc, $bccAddress );
    }

    /**
     * Sets the from address of the email. Will set the replyTo address
     * if not already set.
     *
     * @param $address  an EmailAddress to use as the sender
     */
    public function setFrom( EmailAddress $address )
    {
        $this->from = $address;

        if( $this->replyTo == null )
            $this->replyTo = $address;
    }

    /**
     * Sets the reply-to address for the email.
     *
     * @param $address  an EmailAddress to sue as the reply-to address
     */
    public function setReplyTo( EmailAddress $address )
    {
        $this->replyTo = $address;
    }

    /**
     * Returns the TO field of the email as a string. It will be a comma separated list
     * of address in RFC 2822 format.
     */
    public function getTo()
    {
        return implode(', ', array_map(function(EmailAddress $e) { return $e->getFormattedAddress() }, 
                                       $this->to));
    }

}
