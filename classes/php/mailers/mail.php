<?php

namespace autodeploy\php\mailers;

use autodeploy\php;

class mail extends php\mailer
{

    public function send($something)
    {
        if ($this->to === null)
        {
            throw new \RuntimeException('To is undefined');
        }

        if ($this->subject === null)
        {
            throw new \RuntimeException('Subject is undefined');
        }

        $headers = array();

        if ($this->from !== null)
        {
            $headers[] = 'From: ' . $this->from;
        }

        if ($this->replyTo !== null)
        {
            $headers[] = 'Reply-To: ' . $this->replyTo;
        }

        if ($this->xMailer !== null)
        {
            $headers[] = 'X-Mailer: ' . $this->xMailer;
        }

        if ($this->contentType !== null)
        {
            $headers[] = 'Content-Type: ' . $this->contentType[0] . '; charset="' . $this->contentType[1] . '"';
        }

        $this->adapter->file_put_contents('test.log', implode("\r\n", $headers) . "\r\n" . (string) $something);

        $this->adapter->mail($this->to, $this->subject, (string) $something, implode("\r\n", $headers));

        return $this;
    }

}