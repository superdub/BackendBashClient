<?php


   class HtmlParser
   {

   /**
    *  return array of elements by parameters
    */
    public static function parse(string $html = '', string $tg = '')
    {
        if(strlen($tg) > 1 && strlen($html) > 1)
        {
          $html = str_get_html($html);
          return HtmlParser::find($html,$tg);
        }
        return null;
    }



    private static function find(simple_html_dom $html,string $tg)
    {
      if(count($html->find($tg)))
        {
            $array = [];
            foreach ($html->find($tg) as $div) 
            $array[] = $div;
            return $array;
        }
        return null;
    }

   }

 ?>