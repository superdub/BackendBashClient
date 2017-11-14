<?php
  include_once 'model/HtmlParser.php';
  include_once 'model/HtmlDownload.php';
  include_once 'model/BashInfo.php';
  include_once 'websites/Bash_s.php';


  class Server
  {
      public $bash_s;
      public $ithappens_s;
      public $zadolba_s;

      function __construct($countQuotes)
      {
          $this->bash_s = new Bash_s();
          //echo $this->bash_s->getQuotesWithNumber('0','10');
          //echo $this->bash_s->getQuotesById('447600');
          //echo $this->bash_s->getQuotesWithMain('30');
          //echo $this->bash_s->getRandomQuotes();
          //echo $this->bash_s->getRandomQuotesWithNumber('303');
          //echo $this->bash_s->getRatingQuotesWithMain('10');
      }


      public function HandlerUser(string $command = '',string $parameters = '')
      {
          if($command != null && strlen($command) > 1)
          {
              switch($command)
              {
                  case 'Bush.Message.Get':
                  {
                    
                  }
                      
              }
          }
      }



  }
  

