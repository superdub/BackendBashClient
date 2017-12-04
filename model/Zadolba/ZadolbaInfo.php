<?php

class ZadolbaInfo
{

    public static $ZADOLBA_URL = 'http://zadolba.li/';
    public static $NAME = 'zadolba';

    /*
      * ///////////////////////////////////////////////////////////////////////////////////////
      * ///////////////////////////////////////////////////////////////////////////////////////
      *
      * TAGS
      *
      * ///////////////////////////////////////////////////////////////////////////////////////
      * ///////////////////////////////////////////////////////////////////////////////////////
      */
    static public $TagId = 'div.id';
    static public $TagText = 'div.text';
    static public $TagRate = 'div.rating';
    static public $TagDate = 'div.date-time';
    static public $TagTags = 'div.tags';
    static public $TagHead = 'h2';

    /**
     * @return int count story on one page
     */
    public static function getCountStory()
    {
        $htmlPage = HtmlDownload::download(self::$ZADOLBA_URL);
        $arrayPage = HtmlParser::parse($htmlPage,self::$TagText);
        unset($htmlPage);
        return count($arrayPage);
    }
}