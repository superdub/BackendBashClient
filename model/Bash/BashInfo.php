<?php

   
   class BashInfo
   {
       
       static public $BASH_URL = 'http://bash.im/';
       static public $NAME = 'bash';
       static public $BASH_COUNT = 50;

       /*
        * ///////////////////////////////////////////////////////////////////////////////////////
        * ///////////////////////////////////////////////////////////////////////////////////////
        *
        * TAGS
        *
        * ///////////////////////////////////////////////////////////////////////////////////////
        * ///////////////////////////////////////////////////////////////////////////////////////
        */
       static public $TagId = 'a.id';
       static public $TagText = 'div.text';
       static public $TagRate = 'span.rating';
       static public $TagDate = 'span.date';


       /*
        * ///////////////////////////////////////////////////////////////////////////////////////
        * ///////////////////////////////////////////////////////////////////////////////////////
        *
        * COMMANDS
        *
        * ///////////////////////////////////////////////////////////////////////////////////////
        * ///////////////////////////////////////////////////////////////////////////////////////
        */
       static public $GET_QUOTES_WITH_MAIN_PAGE = 'get.bash.quotesMain';
       static public $GET_QUOTES_WITH_NUMBER = "get.bash.quotesMainNumber";
       static public $GET_RANDOM_QUOTES = "get.bash.quotesRandom";
       static public $GET_RATING_QUOTES_MAIN_PAGE = "get.bash.quotesRating";
       static public $GET_RATING_QUOTES_WITH_NUMBER = "get.bash.quotesRatingNumber";
       static public $GET_QUOTES_BY_ID = "get.bash.quotesId";
       static public $GET_ABYSS_QUOTES = "get.bash.quotesAbyss";
       static public $GET_TOP_ABYSS_QUOTES = "get.bash.quotesTopAbyss";
       static public $GET_COMICS_FOR_QUOTES = "get.bash.comicsForQuote";

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
