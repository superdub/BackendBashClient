<?php

   
   class BashInfo
   {
       
       static public $BASH_URL = 'http://bash.im/';

       static public $BASH_COUNT = 50;


       static public $TagId = 'a.id';
       static public $TagText = 'div.text';
       static public $TagRate = 'span.rating';
       static public $TagDate = 'span.date';

       /**
        * get count of quotes on one page
        */
       static public function getCountQuotes()
       {
           $htmlPage = HtmlDownload::download(self::$BASH_URL);
           $array_text = HtmlParser::parse(iconv("windows-1251", "UTF-8", $htmlPage),self::$TagText);
           unset($htmlPage);

           return count($array_text);
       }


   }
