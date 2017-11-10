<?php

    include_once 'library/simple_html_dom.php';
 
   class HtmlParser
   {

    public function parse(string $html = '',string $tg = '')
    {
        if(strlen($tg) > 1 && strlen($html) > 1)
        {
         $html = str_get_html($html); 
         return $this->find($html,$tg);
        }
        echo 'error parse';
        return null;
    }

    private function find( $html = '',string $tg = '')
    {
      if(count($html->find($tg)))
        {
            $array = [];
            foreach ($html->find($tg) as $div) 
            $array[] = $div;
            return $array;
        }
        echo 'error find';
          return null;
    }

   }

 ?>