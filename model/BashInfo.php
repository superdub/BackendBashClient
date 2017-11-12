<?php
   include_once 'model/HtmlDownload.php';
   include_once 'model/HtmlParser.php';
   
   class BashInfo
   {
       
       static public $BASH_URL = 'http://bash.im/';

       static public $BASH_COUNT = 50;

       //получение количества сообщений на ёглавной странице
       static public function getCountQuotes()
       {
            $html_download = new HtmlDownload();
            $htmlParser = new HtmlParser();
            $html_page = $html_download ->download(self::$BASH_URL);
            $html_count = 0;
            $array_text = $htmlParser->parse(iconv("windows-1251", "UTF-8", $html_page),'div.text');

            foreach($array_text as $text)
            {
               $html_count++;
            }

           unset($array_text);
           unset($html_download);
           unset($htmlParser);
           return $html_count;
       }


       //получение количества лайков на главной странице
       static public function getCountLikes()
       {
            $html_download = new HtmlDownload();
            $htmlParser = new HtmlParser();
            $html_page = $html_download ->download(self::$BASH_URL);
            $html_count = 0;
            $array_text = $htmlParser->parse(iconv("windows-1251", "UTF-8", $html_page),'span.rating');

            foreach($array_text as $text)
            {
                $html_count++;
            }

           unset($array_text);
           unset($html_download);
           unset($htmlParser);
           return $html_count;

       }



       //получение количества дат на главной странице
       static public function getCountDates()
       {
           $html_download = new HtmlDownload();
           $htmlParser = new HtmlParser();
           $html_page = $html_download ->download(self::$BASH_URL);
           $html_count = 0;
           $array_text = $htmlParser->parse(iconv("windows-1251", "UTF-8", $html_page),'span.date');

           foreach($array_text as $text)
           {
               $html_count++;
           }

           unset($array_text);
           unset($html_download);
           unset($htmlParser);
           return $html_count;

       }
   }
