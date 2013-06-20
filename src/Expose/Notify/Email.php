<?php

namespace Expose\Notify;

class Email extends \Expose\Notify
{
    public function send($filterMatches)
    {
        $config = $this->getConfig();

        if (!isset($config['address'])) {
            throw new \InvalidArgumentExcepion('Invalid email address');
        }

        $toAddress = $config['address'];
        $headers = array(
            "From: notify@expose",
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

        mail($toAddress, $subject, $body, implode("\r\n", $headers));
    }
}