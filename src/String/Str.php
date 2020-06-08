<?php


namespace Bedivierre\Craftsman\Poetry;


use Bedivierre\Craftsman\Masonry\BaseDataObject;

class Str implements \ArrayAccess, \Iterator
{
    public const _default = '';

    private $_position = 0;
    private $_length = 0;
    private $value = '';

    public function __construct(string $str = '')
    {
        $this->value = $str;
        $this->_length = $this->length();
    }
    public function __toString()
    {
        return $this->toString();
    }
    public function toInt() : int{
        return (int) $this->value;
    }
    public function toString() : string{
        return $this->value;
    }
    public function length() : int {
        if(!$this->_length)
            return $this->_length = mb_strlen($this->value);
        return $this->_length;
    }
    public function at(int $index):Str{
        return $this->substring($index, 1);
    }


    public function substring(int $start, int $length = 0) : Str{
        return new Str(mb_substr($this->value, $start, $length <= 0 ? null : $length));
    }
    public function trim() : Str{
        return new Str(trim($this->value));
    }
    public function lrim() : Str{
        return new Str(ltrim($this->value));
    }
    public function rtrim() : Str{
        return new Str(rtrim($this->value));
    }

    public function md5() : Str{
        return new Str(md5($this->value));
    }
    public function sha1() : Str{
        return $this->hash("sha1");
    }
    public function hash(string $algo = "sha256") : Str{
        return new Str(hash($algo, $this->value));
    }
    public function crypt(string $salt = '') : Str{
        return new Str(crypt($this->value, $salt));
    }
    public function crc32 () : int{
        return crc32($this->value);
    }

    public function chunk_split (?int $chunk_len = null, ?string $end = "\n" )  : Str{
        return new Str(chunk_split($this->value, $chunk_len, $end));
    }

    /**
     * @param string $delimiter
     * @param int $max_count
     * @return BaseDataObject|Str[]
     */
    public function explode(string $delimiter, int $limit = PHP_INT_MAX): BaseDataObject{
        $b = new BaseDataObject();
        $arr = explode($delimiter, $this->value, $limit);
        foreach ($arr as $v)
            $b->push(new Str($v));
        return $b;
    }

    /**
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @return BaseDataObject
     */
    public function get_csv(string $delimiter = ',', string $enclosure = '"', string $escape = "\\") : BaseDataObject{
        $b = new BaseDataObject();
        $arr = str_getcsv($this->value, $delimiter, $enclosure, $escape);
        foreach ($arr as $v) {
            if(is_array($v))
                $b->push(new BaseDataObject($v));
            elseif (is_string($v))
                $b->push(new Str($v));
            else
                $b->push($v);
        }
        return $b;
    }

    /**
     * @param int $length
     * @return BaseDataObject|Str[]
     */
    public function split(int $length = 1):BaseDataObject {
        $b = new BaseDataObject();
        $arr = str_split($this->value, $length);
        foreach ($arr as $v)
            $b->push(new Str($v));
        return $b;
    }

    public function html_entity_decode(?int $flags = null, ?string $encoding = null) : Str{
        $flags = $flags === null ? ENT_COMPAT | ENT_HTML401 : $flags;
        $encoding = $encoding === null ? ini_get("default_charset") : $encoding;
        return new Str(html_entity_decode($this->value, $flags, $encoding));
    }
    public function htmlentities(?int $flags = null, ?string $encoding = null) : Str{
        $flags = $flags === null ? ENT_COMPAT | ENT_HTML401 : $flags;
        $encoding = $encoding === null ? ini_get("default_charset") : $encoding;
        return new Str(htmlentities($this->value, $flags, $encoding));
    }
    public function htmlspecialchars_decode (?int $flags = null) : Str{
        $flags = $flags === null ? ENT_COMPAT | ENT_HTML401 : $flags;
        return new Str(htmlspecialchars_decode ($this->value, $flags));
    }
    public function htmlspecialchars(?int $flags = null, ?string $encoding = null) : Str{
        $flags = $flags === null ? ENT_COMPAT | ENT_HTML401 : $flags;
        $encoding = $encoding === null ? ini_get("default_charset") : $encoding;
        return new Str(htmlspecialchars($this->value, $flags, $encoding));
    }
    public function quotemeta() : Str{
        return new Str(quotemeta($this->value));
    }

    /**
     * @param string|array $search
     * @param string|array $replace
     * @param int|null $count
     * @return Str
     */
    public function replace($search, $replace, ?int $count = null):Str{
        return new Str(str_replace($search, $replace, $this->value, $count));
    }/**
     * @param string|array $search
     * @param string|array $replace
     * @param int|null $count
     * @return Str
     */
    public function ireplace($search, $replace, ?int $count = null):Str{
        return new Str(str_ireplace($search, $replace, $this->value, $count));
    }
    public function repeat(int $count):Str{
        return new Str(str_repeat($this->value, $count));
    }
    public function shuffle():Str{
        return new Str(str_shuffle($this->value));
    }

    public function strpos(string $value, int $offset = 0):int{
        return mb_strpos($this->value, $value, $offset);
    }
    public function compare(string $value):int{
        return strcmp($this->value, $value);
    }
    public function toLower():Str{
        return new Str(mb_strtolower($this->value));
    }
    public function toUpper():Str{
        return new Str(mb_strtoupper($this->value));
    }



    public function uuencode() : Str{
        return new Str(convert_uuencode($this->value));
    }
    public function uudecode() : Str{
        return new Str(convert_uudecode($this->value));
    }
    public function base64encode() : Str{
        return new Str(base64_encode($this->value));
    }
    public function base64decode() : Str{
        $v = base64_decode($this->value);
        return $v === false ? Str::empty() : new Str( $v);
    }




    public static function char(int $byte) : Str{
        return new Str(chr($byte));
    }
    public static function byte(string $string) : Str{
        return new Str(ord($string));
    }
    public static function implode(string $glue, array $pieces) : Str{
        return new Str(implode($glue, $pieces));
    }
    public static function join(string $glue, array $pieces) : Str{
        return new Str(join($glue, $pieces));
    }
    public static function empty() : Str{
        return new Str(Str::_default);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        if(is_string($offset) || $offset instanceof Str){
            return $this->strpos($offset) !== false;
        }
        if(is_int($offset))
            return $this->length() > $offset;
        return false;
    }

    /**
     * @param mixed $offset
     * @return Str
     */
    public function offsetGet($offset)
    {
        if(is_int($offset))
            return $this->at($offset);
        return $this->at((int)$offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {

    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {

    }

    /**
     * @return mixed
     */
    public function current()
    {
        return $this->at($this->_position);
    }

    /**
     *
     */
    public function next()
    {
        ++$this->_position;
    }

    /**
     * @return bool|float|int|string|null
     */
    public function key()
    {
        return $this->_position;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->_position < $this->length();
    }

    /**
     *
     */
    public function rewind()
    {
        $this->_position = 0;
    }
}