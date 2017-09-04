<?php

namespace MX\Parser;

use MX\Base\Model;

class EmailBodyParser extends Model
{
    /**
     * Attachments
     *
     * @var array of MX\Parser\Email\Attachment
     */
    protected $attachments = [];

    /**
     * HTML body
     *
     * @var string
     */
    protected $html = null;

    /**
     * Plain body
     *
     * @var string
     */
    protected $plain = null;

    /**
     * Raw body
     *
     * @var string
     */
    protected $raw = null;

    /**
     * Entry point to parse body
     *
     * @param  array                $rawBody
     * @param  EmailHeadersParser   $headers
     * @return EmailBodyParser
     */
    public static function parse(array &$rawBody, EmailHeadersParser $headers)
    {
        $bodyType = ucfirst($headers->type);
        $body = forward_static_call(array(self, "parse{$bodyType}Body"), $rawBody);

        return $body;
    }

    /**
     * Parse body from an fd starting at given line
     *
     * @param  string               $rawBody
     * @param  EmailHeadersParser   $headers
     * @return EmailBodyParser
     */
    protected static function parseTextBody(array &$rawBody)
    {
        $body = new self;

        while (($line = array_shift($rawBody)) !== null) {
            $body->raw .= $line;
        }

        $body->plain = strip_tags(($body->html = $body->raw));

        return $body;
    }
}
