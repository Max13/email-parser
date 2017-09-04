<?php

namespace MX\Parser;

use MX\Base\Model;
use MX\Exception\ParseException;

class EmailParser extends Model
{
    /**
     * Headers
     *
     * @var MX\Parser\Email\Head
     */
    protected $headers = null;

    /**
     * Body
     *
     * @var MX\Parser\Email\Body
     */
    protected $body = null;

    /**
     * Raw
     *
     * @var string Raw body of parsed email
     */
    protected $raw = null;

    /**
     * Parse an email from string, file or STDIN
     * in EML format (CRLF terminated lines)
     *
     * @param  string|STDIN $eml
     * @return EmailParser
     */
    public static function parse($eml)
    {
        $parser = new EmailParser;
        $parser->raw = $eml;

        if ($eml === STDIN) {
            $arrayed = parseStdinToArray();
        } elseif (is_readable($eml)) {
            $arrayed = parseFileToArray($eml);
        } elseif (($type = gettype($eml)) == 'string') {
            $arrayed = parseStringToArray($eml);
        } else {
            throw new ParseException(0, "Can only parse string, file or STDIN, $type given");
        }

        $parser->headers = EmailHeadersParser::parse($arrayed);
        $parser->body = EmailBodyParser::parse($arrayed, $parser->headers);

        return $parser;
    }
}
