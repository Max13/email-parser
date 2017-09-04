<?php

namespace MX\Parser;

use MX\Base\Model;
use MX\Exception\ParseException;

class EmailHeadersParser extends Model
{
    /**
     * Shorthand for boundary (Content-Type parameter)
     *
     * @var string
     */
    protected $boundary = null;

    /**
     * Shorthand for charset (Content-Type parameter)
     *
     * @var string
     */
    protected $charset = 'ascii';

    /**
     * Shorthand for encoding (Content-Transfer-Encoding)
     *
     * @var string
     */
    protected $encoding = '7bit';

    /**
     * All headers as indexed array
     *
     * @var array
     */
    protected $indexed = [];

    /**
     * Shorthand for Content-Type type (first part, first side)
     *
     * @var string
     */
    protected $type = 'text';

    /**
     * Shorthand for Content-Type subtype (first part, second side)
     *
     * @var string
     */
    protected $subtype = 'plain';

    /**
     * Raw headers
     *
     * @var string
     */
    protected $raw = null;

    /**
     * Entry point to parse headers
     *
     * @param  array $rawHeaders
     * @return EmailHeadersParser
     */
    public static function parse(array &$rawHeaders)
    {
        $headers = new self;
        $key = null;

        while (($line = array_shift($rawHeaders)) !== null && $line != "\r\n") {
            $headers->raw .= $line;

            if (in_array($line[0], [' ', "\t", "\n", "\r", "\x0B"])) { // Follows previous line
                $headers->$key .= ' '.substr(ltrim($line), 0, -2);
                $headers->indexed[$key][count($headers->indexed[$key]) - 1] = $headers->$key;
            } elseif (strpos($line, ':') > 0) { // New header
                list($key, $val) = explode(':', $line, 2);

                $headers->$key = substr(ltrim($val), 0, -2);
                $headers->indexed[$key][] = $headers->$key;
            } else {
                throw new ParseException('Unrecognized line', $l, $line);
            }
        }

        return $headers;
    }

    /**
     * Get an attribute by key, accepts snake case (convert to kebab-case)
     *
     * @param  string  $key
     * @return mixed   $value
     */
    public function __get($key)
    {
        return parent::__get(str_replace('_', '-', $key));
    }

    /**
     * Set an attribute by key, also setting the shorthands
     *
     * @param  string  $key
     * @param  mixed   $value
     */
    public function __set($key, $value)
    {
        parent::__set($key, $value);

        if ($key == 'Content-Type') {
            $ct = explode('/', explode(';', $value, 2)[0], 2);
            $this->type = $ct[0];
            $this->subtype = $ct[1];

            if (($pos = strpos($this->Content_Type, 'boundary')) !== false) {
                $boundary = substr($this->Content_Type, $pos + 9);

                if ($boundary[0] == '"' && $boundary[strlen($boundary) - 1] == '"') {
                    $boundary = substr($boundary, 1, -1);
                }

                $this->boundary = $boundary;
            }

            if (($pos = strpos($this->Content_Type, 'charset')) !== false) {
                $charset = substr($this->Content_Type, $pos + 8);

                if ($charset[0] == '"' && $charset[strlen($charset) - 1] == '"') {
                    $charset = substr($charset, 1, -1);
                }

                $this->charset = $charset;
            }
        }

        if ($key == 'Content-Transfer-Encoding') {
            $this->encoding = $value;
        }
    }
}
