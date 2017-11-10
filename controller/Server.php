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
          $this->bash_s = new Bash_s($countQuotes);
        echo $this->bash_s->getQuotesById('35000');
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
  

