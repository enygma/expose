<?php

namespace Expose\Notify;

class Email extends \Expose\Notify
{
    /**
     * To email address for notifications
     * @var string
     */
    private $toAddress = null;

    /**
     * From address for notifications
     * @var string
     */
    private $fromAddress = 'notify@expose';

    /**
     * Set the "To" address for the notification
     *
     * @param string $emailAddress Email address
     */
    public function setToAddress($emailAddress)
    {
        if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL) !== $emailAddress) {
            throw new \InvalidArgumentExcepion('Invalid email address: '.$emailAddress);
        }
        $this->toAddress = $emailAddress;
    }

    /**
     * Get the current "To" address for notification
     *
     * @return string Email address
     */
    public function getToAddress()
    {
        return $this->toAddress;
    }

    /**
     * Set the current "From" email address on notifications
     * 
     * @param string $emailAddress Email address
     */
    public function setFromAddress($emailAddress)
    {
        if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL) !== $emailAddress) {
            throw new \InvalidArgumentExcepion('Invalid email address: '.$emailAddress);
        }
        $this->fromAddress = $emailAddress;
    }

    /**
     * Return the current "From" address setting
     * 
     * @return string Email address
     */
    public function getFromAddress()
    {
        return $this->fromAddress;
    }

    /**
     * Send the notification to the given email address
     * 
     * @param array $filterMatches Set of filter matches from execution
     * @return boolean Success/fail of sending email
     */
    public function send($filterMatches)
    {
        $toAddress = $this->getToAddress();
        $fromAddress = $this->getFromAddress();

        if ($toAddress === null) {
            throw new \InvalidArgumentExcepion('Invalid "to" email address');
        }

        if ($fromAddress === null) {
            throw new \InvalidArgumentExcepion('Invalid "from" email address');
        }

        $headers = array(
            "From: ".$fromAddress,
            "Content-type: text/html; charset=iso-8859-1"
        );
        $totalImpact = 0;

        $body = '<html><body><table cellspacing="0" cellpadding="3" border="0">';
        $body .= '<tr><td><b>Impact</b></td><td><b>Description</b></td></tr>';

        foreach ($filterMatches as $match) {
            $body .= '<tr><td align="center">'.$match->getImpact().'</td><td>'
                .$match->getDescription().' ('.$match->getId().')</td></tr>';
            $body .= '<tr><td>&nbsp;</td><td><b>Tags:</b> '.implode(', ', $match->getTags()).'</td></tr>';
            $body .= '<tr><td>&nbsp;</td><td><b>Impact:</b> '.$match->getImpact().'</td></tr>';
            $body .= '<tr><td colspan="2">&nbsp;</td></tr>';

            $totalImpact += $match->getImpact();
        }

        $body .= '</table></body></html>';
        $subject = 'Expose Notification - Impact Score '.$totalImpact;

        return mail($toAddress, $subject, $body, implode("\r\n", $headers));
    }
}
