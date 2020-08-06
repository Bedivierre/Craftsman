<?php


namespace Bedivierre\Craftsman\Appraise;


use Bedivierre\Craftsman\Masonry\BaseDataObject;

/**
 * Class CheckData
 * @package Bedivierre\Alfastrah\Data
 * @property string $type
 * @property bool $required
 * @property string $pattern
 * @property Callable $check должна принимать проверяемое значение и возвращать true в случае успеха и
 *      строку с текстом ошибки в ином случае.
 * @property float $max
 * @property float $min
 */
class CheckData{
    public $type;
    public $pattern;
    public $required;
    public $check;
    public $min;
    public $max;

    const TYPE_BOOLEAN = 'boolean';
    const TYPE_STRING = 'string';
    const TYPE_INT = 'int';
    const TYPE_FLOAT = 'float';
    const TYPE_ARRAY = 'array';
    const TYPE_OBJECT = 'object';

    public function __construct(bool $required = true, string $type = '', string $pattern = '')
    {
        $this->required = $required;
        $this->pattern = $pattern;
        $this->type = $type;
        if(!$this->type && $this->pattern)
            $this->type = self::TYPE_STRING;
        $this->min = null;
        $this->max = null;
    }

    public function checkData($v){
        if(is_null($v)){
            if ($this->required)
                return 'Пустое значение';
            if(is_callable($this->check))
                return call_user_func($this->check, $v);
            return true;
        }

        if($this->type){
            switch ($this->type){
                case self::TYPE_STRING:
                    if(!is_string($v)) return "Значение не является строкой";
                    break;
                case self::TYPE_ARRAY:
                    if(!is_array($v)) return "Значение не является массивом";
                    break;
                case self::TYPE_BOOLEAN:
                    if(!is_bool($v)) return "Значение не является булевым значением";
                    break;
                case self::TYPE_INT:
                    if(!is_int($v)) return "Значение не является целым числом";
                    break;
                case self::TYPE_FLOAT:
                    if(!is_float($v)) return "Значение не является числом с плавающей точкой";
                    break;
                case self::TYPE_OBJECT:
                    if(!is_object($v)) return "Значение не является объектом";
                    break;
                default:
                    if(gettype($v) !== $this->type) return "Значение не является типом {$this->type}";
                    break;
            }
        }

        if(is_string($v) && $this->pattern){
            $pattern = "~^{$this->pattern}$~";
            if(!preg_match($pattern, $v)) {
                return 'Значение не совпадает с шаблоном';
            }
        }
        if((is_int($v) || is_float($v)) && $this->max !== null){
            if($v > $this->max) {
                return "Значение '{$v}' больше максимально допустимого '{$this->max}'";
            }
        }
        if((is_int($v) || is_float($v)) && $this->min !== null){
            if($v < $this->min) {
                return "Значение '{$v}' меньше минимально допустимого '{$this->min}'";
            }
        }
        if(is_callable($this->check))
            return call_user_func($this->check, $v);
        return true;
    }

    public static function transformToCheckData($v){
        if($v instanceof CheckData)
            return $v;
        if(is_bool($v)){
            return new CheckData();
        }
        if(is_string($v)){
            return new CheckData(true, $v);
        }
        if(is_callable($v)){
            $ch = new CheckData();
            $ch->check = $v;
            return $ch;
        }
        if(is_array($v) || ($v instanceof BaseDataObject)){
            $ch = new CheckData();
            if(isset($v['required']))
                $ch->required = (bool)$v['required'];
            if(isset($v['pattern']) && is_string($v['pattern'])){
                $ch->pattern = $v['pattern'];
                if(!$ch->type && $ch->pattern)
                    $ch->type = CheckData::TYPE_STRING;
            }
            if(isset($v['type']) && is_string($v['type']))
                $ch->type = $v['type'];
            if(isset($v['min']))
                $ch->min = $v['min'];
            if(isset($v['max']))
                $ch->max = $v['max'];
            if(isset($v['check']) && is_callable($v['check']))
                $ch->check = $v['max'];
            return $ch;
        }
        return new CheckData(false);
    }
}