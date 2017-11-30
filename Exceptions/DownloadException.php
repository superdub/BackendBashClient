<?php
/**
 * Created by PhpStorm.
 * User: dubrovin
 * Date: 11/26/17
 * Time: 1:40 PM
 */

class DownloadException extends BaseException
{
    /**
     * DownloadException constructor.
     */
    public function __construct()
    {
       parent::__construct('error downloading page');
    }
}