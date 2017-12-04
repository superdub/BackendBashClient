<?php

  class Server
  {
      public $bash_s;
      public $ithappens_s;
      public $zadolba_s;


      public function  __construct()
      {
          $this->bash_s = new Bash_s();
          $this->zadolba_s = new Zadolba_s();

          $html = HtmlDownload::download(ZadolbaInfo::$ZADOLBA_URL);
        //  echo $this->zadolba_s->getElementsWithMainPage('5');
            echo $this->zadolba_s->getStoriesOnPage($html,1,5);
      }

      public function HandlerUser($command = '', $parameters = '')
      {
          if($command != null && strlen($command) > 1)
          {
              switch ($command)
              {
                  case BashInfo::$GET_QUOTES_WITH_MAIN_PAGE:
                  {
                      $param = json_decode($parameters,true);
                      $count = $param['count'];
                      echo $this->bash_s->getElementsWithMainPage($count);
                      break;
                  }
                  case BashInfo::$GET_QUOTES_WITH_NUMBER:
                  {
                      $param = json_decode($parameters,true);
                      $number = $param['number'];
                      $count = $param['count'];
                      echo $this->bash_s->getElementsWithNumber($number,$count);
                      break;
                  }
                  case BashInfo::$GET_RATING_QUOTES_MAIN_PAGE:
                  {
                      $param = json_decode($parameters,true);
                      $count = $param['count'];
                      echo $this->bash_s->getRatingElementsWithMainPage($count);
                      break;
                  }
                  case BashInfo::$GET_RATING_QUOTES_WITH_NUMBER:
                  {
                      $param = json_decode($parameters,true);
                      $number = $param['number'];
                      $count = $param['count'];
                      echo $this->bash_s->getRatingElementsWithNumber($number,$count);
                      break;
                  }
                  case BashInfo::$GET_ABYSS_QUOTES:
                  {
                      echo $this->bash_s->getAbyssElements();
                      break;
                  }
                  case BashInfo::$GET_QUOTES_BY_ID:
                  {
                      $param = json_decode($parameters,true);
                      $id = $param['id'];
                      echo $this->bash_s->getElementById($id);
                      break;
                  }
                  case BashInfo::$GET_TOP_ABYSS_QUOTES:
                  {
                      echo $this->bash_s->getTopAbyssElements();
                      break;
                  }
                  case BashInfo::$GET_RANDOM_QUOTES:
                  {
                      echo $this->bash_s->getRandomElements();
                      break;
                  }
                  case BashInfo::$GET_COMICS_FOR_QUOTES:
                  {
                      $param = json_decode($parameters,true);
                      $id = $param['id'];
                      echo $this->bash_s->getComicsForQuotes($id);
                      break;
                  }
              }
          }
      }



  }
  

