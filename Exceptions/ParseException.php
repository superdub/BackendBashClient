<?php
/**
 * Created by PhpStorm.
 * User: dubrovin
 * Date: 11/26/17
 * Time: 1:32 PM
 */

class ParseException extends BaseException
{
    /**
     * ParseException constructor.
     */
    public function __construct()
    {
        parent::__construct('error parsing page');
    }
}