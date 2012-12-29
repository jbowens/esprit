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

    /* The subject line of the email. */
    protected $subject;

    /* The message of the email. */
    protected $message;

    /* The charset of the email. */
    protected $charset = 'utf-8';

    /* The content-type of the email. */
    protected $contentType = 'text/plain';

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
     * Sets the subject of the email.
     */
    public function setSubject( $subject )
    {
        $this->subject = $subject;
    }

    /**
     * Sets the message content of the email.
     */
    public function setMessage( $message )
    {
        $this->message = $message;
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
     * of addresses in RFC 2822 format.
     */
    public function getToAsString()
    {
        return implode(', ', array_map(function(EmailAddress $e) { return $e->getFormattedAddress() }, 
                                       $this->to));
    }

    /**
     * Returns the FROM field of the email as a string. It will be in RFC 2822 format.
     */
    public function getFromAsString()
    {
        return $this->from->getFormattedAddress();
    }

    /**
     * Returns the Reply-To field of the email as a string. It will be in RFC 2822 format.
     */
    public function getReplyToAsString()
    {
        return $this->replyTo->getFormattedAddress();
    } 

    /**
     * Gets the subject of the email.
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     *  Gets the message of the email.
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Retrieves the charset in which this email is encoded.
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Retrieves the content-type of this email's message body.
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Returns whether or not this email has any addresses CC'd.
     */
    public function usesCc()
    {
        return count($this->cc) > 0;
    }

    /**
     * Returns whether or not this email has any addressed BCC'd.
     */
    public function usesBcc()
    {
        return count($this->bcc) > 0;
    }

    /**
     * Returns the Cc field of the email as a string. It will be a comma separated list
     * of addresses in RFC 2822 format.
     */
    public function getCcAsString()
    {
        return implode(', ', array_map(function(EmailAddress $e) { return $e->getFormattedAddress() }, 
                                       $this->cc));
    }   
    
    /**
     * Returns the Bcc field of the email as a string. It will be a comma separated list
     * of addresses in RFC 2822 format.
     */
    public function getBccAsString()
    {
        return implode(', ', array_map(function(EmailAddress $e) { return $e->getFormattedAddress() }, 
                                       $this->bcc));
    }

}
