<?php

namespace Masterminds\HTML5\Parser;

use Masterminds\HTML5\Entities;

/**
 * Manage entity references.
 *
 * This is a simple resolver for HTML5 character reference entitites.
 * See \Masterminds\HTML5\Entities for the list of supported entities.
 */
class CharacterReference
{

    protected static $numericMask = array(
        0x0,
        0x2FFFF,
        0,
        0xFFFF
    );

    /**
     * Given a name (e.g. 'amp'), lookup the UTF-8 character ('&')
     *
     * @param string $name The name to look up.
     *
     * @return string The character sequence. In UTF-8 this may be more than one byte.
     */
    public static function lookupName($name)
    {
        // Do we really want to return NULL here? or FFFD
        return isset(Entities::$byName[$name]) ? Entities::$byName[$name] : null;
    }

    /**
     * Given a decimal number, return the UTF-8 character.
     *
     * @param int $int Entity code
     *
     * @return string
     */
    public static function lookupDecimal($int)
    {
        $entity = '&#' . $int . ';';
        // UNTESTED: This may fail on some planes. Couldn't find full documentation
        // on the value of the mask array.
        return mb_decode_numericentity($entity, static::$numericMask, 'utf-8');
    }

    /**
     * Given a hexidecimal number, return the UTF-8 character.
     *
     * @param string $hexDec HEX code of symbol
     *
     * @return string
     */
    public static function lookupHex($hexDec)
    {
        return static::lookupDecimal(hexdec($hexDec));
    }
}
