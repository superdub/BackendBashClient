<?php

  class Server
  {
      public $bash_s;
      public $ithappens_s;
      public $zadolba_s;

      public function  __construct()
      {

          //TODO test methods

          $this->bash_s = new Bash_s();
          //not echo $this->bash_s->getMainIndex();
          //not echo $this->bash_s->getRandomElements();
          //not echo $this->bash_s->getElementsWithMainPage(50);
          //not echo $this->bash_s->getElementById('4555');
          //not echo $this->bash_s->getRatingElementsWithMainPage(3000);
          //not echo $this->bash_s->getElementsWithNumber(145,345);
          //not echo $this->bash_s->getRatingElementsWithNumber(808,80);
          //not echo $this->bash_s->getAbyssElements();
          //not echo $this->bash_s->getTopAbyssElements();
         // echo $this->bash_s->getComicsElements(1,5);
          echo $this->bash_s->getComicsForQuotes('390648');
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
  

