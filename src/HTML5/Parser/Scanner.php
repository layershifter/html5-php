<?php

namespace Masterminds\HTML5\Parser;

use Masterminds\HTML5\Parser\Interfaces\InputStream;

/**
 * The scanner.
 *
 * This scans over an input stream.
 */
class Scanner
{

    /**
     * Constant that includes all HEX-chars
     */
    const CHARS_HEX = 'abcdefABCDEF01234567890';
    /**
     * Constant that includes all alpha and numeric chars.
     */
    const CHARS_ALPHA_NUM = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890';
    /**
     * Constant that includes only alpha chars.
     */
    const CHARS_ALPHA = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    /**
     * @var InputStream Instance of InputStream
     */
    protected $inputStream;
    /**
     * @var boolean Flipping this to true will give minisculely more debugging info.
     */
    public $debug = false;

    /**
     * Create a new Scanner.
     *
     * @param InputStream $inputStream An InputStream to be scanned.
     */
    public function __construct(InputStream $inputStream)
    {
        $this->inputStream = $inputStream;
    }

    /**
     * Get the current position.
     *
     * @return int The current int byte position.
     */
    public function position()
    {
        return $this->inputStream->key();
    }

    /**
     * Take a peek at the next character in the data.
     *
     * @return string The next character.
     */
    public function peek()
    {
        return $this->inputStream->peek();
    }

    /**
     * Get the next character.
     *
     * Note, his advances the pointer.
     *
     * @return string The next character.
     */
    public function next()
    {
        $this->inputStream->next();

        if ($this->inputStream->valid()) {
            if ($this->debug) {
                fprintf(STDOUT, "> %s\n", $this->inputStream->current());
            }

            return $this->inputStream->current();
        }

        return false;
    }

    /**
     * Get the current character.
     *
     * Note, this does not advance the pointer.
     *
     * @return string The current character.
     */
    public function current()
    {
        if ($this->inputStream->valid()) {
            return $this->inputStream->current();
        }

        return false;
    }

    /**
     * Silently consume N chars.
     *
     * @param int $count Number of chars.
     *
     * @return void
     */
    public function consume($count = 1)
    {
        for ($i = 0; $i < $count; ++$i) {
            $this->next();
        }
    }

    /**
     * Unconsume some of the data.
     *
     * Note, this moves the data pointer backwards.
     *
     * @param int $howMany The number of characters to move the pointer back.
     *
     * @return void
     */
    public function unconsume($howMany = 1)
    {
        $this->inputStream->unconsume($howMany);
    }

    /**
     * Get the next group of that contains hex characters.
     *
     * Note, along with getting the characters the pointer in the data will be moved as well.
     *
     * @return string The next group that is hex characters.
     */
    public function getHex()
    {
        return $this->inputStream->charsWhile(Scanner::CHARS_HEX);
    }

    /**
     * Get the next group of characters that are ASCII Alpha characters.
     *
     * Note, along with getting the characters the pointer in the data will be moved as well.
     *
     * @return string The next group of ASCII alpha characters.
     */
    public function getAsciiAlpha()
    {
        return $this->inputStream->charsWhile(Scanner::CHARS_ALPHA);
    }

    /**
     * Get the next group of characters that are ASCII Alpha characters and numbers.
     *
     * Note, along with getting the characters the pointer in the data will be moved as well.
     *
     * @return string The next group of ASCII alpha characters and numbers.
     */
    public function getAsciiAlphaNum()
    {
        return $this->inputStream->charsWhile(Scanner::CHARS_ALPHA_NUM);
    }

    /**
     * Get the next group of numbers.
     *
     * Note, along with getting the characters the pointer in the data will be moved as well.
     *
     * @return string The next group of numbers.
     */
    public function getNumeric()
    {
        return $this->inputStream->charsWhile('0123456789');
    }

    /**
     * Consume whitespace.
     *
     * Whitespace in HTML5 is: formfeed, tab, newline, space.
     *
     * @return string The next group after whitespace.
     */
    public function whitespace()
    {
        return $this->inputStream->charsWhile("\n\t\f ");
    }

    /**
     * Returns the current line that is being consumed.
     *
     * @return int The current line number.
     */
    public function currentLine()
    {
        return $this->inputStream->currentLine();
    }

    /**
     * Read chars until something in the mask is encountered.
     *
     * @param string $mask
     *
     * @return boolean|int Returns index on success and false on failure.
     */
    public function charsUntil($mask)
    {
        return $this->inputStream->charsUntil($mask);
    }

    /**
     * Read chars as long as the mask matches.
     *
     * @param string $mask
     *
     * @return string The next group that not matches mask.
     */
    public function charsWhile($mask)
    {
        return $this->inputStream->charsWhile($mask);
    }

    /**
     * Returns the current column of the current line that the tokenizer is at.
     *
     * Newlines are column 0. The first char after a newline is column 1.
     *
     * @return int The column number.
     */
    public function columnOffset()
    {
        return $this->inputStream->columnOffset();
    }

    /**
     * Get all characters until EOF.
     *
     * This consumes characters until the EOF.
     *
     * @return int The number of characters remaining.
     */
    public function remainingChars()
    {
        return $this->inputStream->remainingChars();
    }
}
