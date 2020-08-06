<?php


namespace Bedivierre\Craftsman\Appraise;


use Bedivierre\Craftsman\Masonry\BaseDataObject;

/**
 * Class CheckResult
 * @package Bedivierre\Alfastrah\Data
 * @property BaseDataObject $data
 * @property bool $result
 * @property string $message
 *
 */
class CheckResult{
    public $data;
    public $result;
    public $message;
    public function __construct(bool $result, string $message = '', BaseDataObject $data = null)
    {
        $this->result = $result;
        $this->message = $message;
        $this->data = $data == null ? new BaseDataObject() : $data;
    }
}