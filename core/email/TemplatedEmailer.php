<?php

namespace esprit\core\email;

use \esprit\core\TemplateParser;

/**
 * A class designed for sending emails based on templates. It can use any
 * EmailSender implementation to actually send the email.
 *
 * @author jbowens
 * @since December 2012
 */
class TemplatedEmailer
{

    /* The default email sender is the PHP one, since it's guaranteed on all
     * installations. */
    static protected $defaultSender = new PhpMailEmailSender();

    /* The EmailSender implementation that handles the logicistics of actually
     * sending an email through the smtp servers. */
    protected $emailSender = static::$defaultSender;

    // A \esprit\core\TemplateParser for evaluating the email templates
    protected $templateParser;

    // The prefix for email templates
    protected $templatePrefix;

    /**
     * Constructor for the TemplatedEmailer. It requires a TemplateParser instance
     * to handle parsing and evaluating templates.
     *
     * @param TemplateParser $templateParser  the TemplateParser to use when evaluating
     *                                        templates
     * @param $templatePrefix  a string to be prepended to template names whenever loading
     *                         a template.
     */
    public function __construct( TemplateParser $templateParser, $templatePrefix = '' )
    {
        $this->templateParser = $templateParser;
        $this->templatePrefix = $templatePrefix;
    }

    /**
     * Sets the EmailSender implementation to the given EmailSender object.
     *
     * @param $newEmailSender  the new EmailSender implementation to use
     */
    public function setEmailSender( EmailSender $newEmailSender )
    {
        $this->emailSender = $newEmailSender;
    }

    /**
     * Sends the given email with the message text gained by evaluating the given
     * template with the given parameters. Note: This *does* modify the provided email
     * object. If you're sending multiple emails with the same values, you should 
     * clone the email for each call to sendEmail().
     *
     * @param Email $email  an email object populated with the required information.
     *                      the email's message will be overridden with the evaluated
     *                      template text.
     * @param $template     the template to use. The template prefix, if set, will be
     *                      prepended to retrieve the actual template identifier
     * @param array $params  an associative array defining the template parameters to
     *                       use while evaluating the template
     */
    public function sendEmail( Email $email, $template, array $params )
    {

        // Add the template prefix.
        $templateIdentifier = $this->templatePrefix . $template;

        // Ensure that this template actually exists
        if( ! $this->templateParser->templateExists( $templateIdentifier ) )
        {
            throw new \InvalidArgumentException("The email template " . $templateIdentifier . " does not exist.");
        }

        // Remove any old parameters on the template parser
        $this->templateParser->clear();

        // Set the provided template parameters
        foreach( $params as $key => $val )
        {
            $this->templateParser->setVariable( $key, $val );
        }

        // Evaluate the template, settings the email's message text
        $email->setMessage( $this->templateParser->evaluateTemplate( $templateIdentifier ) );

        // Send the email using the email sender
        $this->emailSender->send( $email );

    } 

}
