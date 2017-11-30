<?php


   class HtmlParser
   {

   /**
    *  return array elements by tag
    */
    public static function parse($html = '',  $tg = '')
    {
        try {
            if (strlen($tg) > 1 && strlen($html) > 1) {
                $html = str_get_html($html);
                return HtmlParser::find($html, $tg);
            }
        }
        catch (ParseException $e){echo $e->getMessage();}
        return null;
    }

   /**
    * @param string $html
    * @param array $array
    * @return array elements by tags
    */
    public static function parses($html = '', array $array)
    {
        try {
            $tags = $array[0];
            $length = count($array);
            for ($i = 1; $i < $length; ++$i) $tags .= ", $array[$i]";

            if (strlen($tags) > 1 && strlen($html) > 1) {
                $html = str_get_html($html);
                return HtmlParser::find($html, $tags);
            }
        }
        catch (ParseException $e){echo $e->getMessage();}
        return null;
    }

       /**
        * return array of string elements
        * @param simple_html_dom $html
        * @param string $tg
        * @return array|null
        */
       private static function find(simple_html_dom &$html, string $tg)
    {
       $arr = $html->find($tg);
       $lengthArr = count($arr);
       if($lengthArr)
        {
            $array = [];
            for ($i = 0;$i < $lengthArr;++$i) $array[] = $arr[$i];
            return $array;
        }
        return null;
    }



   }

 ?>