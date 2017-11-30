<?php
/**
 * Created by PhpStorm.
 * User: dubrovin
 * Date: 11/26/17
 * Time: 1:27 PM
 */

class BaseException extends Exception
{
    /**
     * BaseException constructor.
     * @param string $message
     * @param int $code
     */
    public function __construct($message = '', $code = 0) {
        parent::__construct($message,0);
    }
}