<?php


class Bash_s extends WebSite
{
    /**
     * get Count Quotes on One page
     * Bash_s constructor.
     */
    public function __construct()
    {
        $this->countElements = BashInfo::getCountQuotes();
    }


    /**
     * @param $html
     * @return int count texts on any page
     */
    private function getCountQuotesOnPage($html)
    {
        $array_text = HtmlParser::parse(iconv("windows-1251", "UTF-8", $html),BashInfo::$TagText);
        return count($array_text);
    }


    /**
     * @return $index of main page
     */
    public function getMainIndex()
    {
        $html_page = HtmlDownload::download(BashInfo::$BASH_URL);

        foreach(HtmlParser::parse(iconv("windows-1251", "UTF-8", $html_page),'div.pager') as $key=>$text1) {
            foreach (HtmlParser::parse(iconv("windows-1251", "UTF-8", $text1), 'form') as $key2=>$text2) {
                $mas = HtmlParser::parse(iconv("windows-1251", "UTF-8", $text2), '.page');
                $str = (int)strripos($mas[0], 'value');
                $str = substr($mas[0], $str, $str + 5);
                $str = str_replace(['value="','" />'], '', $str);
                return (int)$str;
            }
        }
        return null;
    }

    /**
     * return array of quotes
     * @param $html_page
     * @param int $first
     * @param int $last
     * @return array
     */
    public function getQuotesOnPage(&$html_page, int $first, int $last ,$tagText = 'div.text',$tagId = 'a.id',$tagDate = 'span.date',$tagRate = 'span.rating')
    {
        $array = [];
        if($first==0 || $last == 0) {echo 'first or last can\'t be 0  ';return null;}
        if($first > $last){echo "first($first) > last($last)";return null;}
        $parseArray = HtmlParser::parses(iconv("windows-1251", "UTF-8", $html_page),[$tagDate,$tagId,$tagText,$tagRate]);

        $first--;

        $quote = null;$id = null;$date = null;$text = null;$rate = null;
        $length = count($parseArray);

        for ($i = 0;$i < $length;++$i)
        {
            switch ($parseArray[$i]->class)
            {
                case 'date':
                {
                    $date = $parseArray[$i]->innertext; break;
                }
                case 'id':
                {
                    $id = $parseArray[$i]->innertext; break;
                }
                case 'rating':
                {
                    $rate = $parseArray[$i]->innertext; break;
                }
                case 'text':
                {
                    $text = $parseArray[$i]->innertext;$text = str_replace(['&quot;','<br />','<br>','/>','\\','\'','\"'],' ',$text); break;
                }
            }

            if($id != null && $date != null && $rate != null && $text != null)
            {
                $quote = new Quote($id,$text,$rate,$date);
                $quote->text = $text;
                $array[] = $quote;
                $quote = null;$id = null;$date = null;$text = null;$rate = null;
            }
        }

        for ($i = 0;$i < $first;++$i)
        {
            unset($array[$i]);
        }
        for ($i = $last;$i < $length;++$i)
        {
            unset($array[$i]);
        }

        $array1 = [];
        foreach ($array as $key => $value)
            $array1[] = $value;

        return $array1;
    }

    /**
     * return json array of elements starting with the main page
     * @param string $count
     * @return string json array
     */
    public function getElementsWithMainPage(string $count)
    {
        if(!is_numeric($count)) {echo 'number of count\'s quotes is not number'; return null;}
        if((int)$count == 0) {echo 'count can not 0'; return null;}

        $array = [];

        $mainIndex = $this->getMainIndex();

        $countPage = $this->getCountPages($count,$this->countElements);

        for ($i = 0;$i < $countPage[0] - 1;++$i)
        {
          $index = $mainIndex - $i;
          $htmlPage = HtmlDownload::download(BashInfo::$BASH_URL.'index/'.$index);
          $arr = $this->getQuotesOnPage($htmlPage,1,$this->countElements);
          $length = count($arr);
          for($j = 0;$j < $length;++$j) $array[] = $arr[$j];
          unset($arr);
          unset($htmlPage);
        }

        if($countPage[1] != 0){
            $htmlPage = HtmlDownload::download(BashInfo::$BASH_URL.'index/'.($mainIndex-$countPage[0] + 1));
            $arr = $this->getQuotesOnPage($htmlPage,1,$countPage[1]);
            $length = count($arr);
            for($j = 0;$j < $length;++$j) $array[] = $arr[$j];}

        echo count($array),'   ';

        return json_encode($array,JSON_UNESCAPED_UNICODE);
    }

    /**
     * return json array of elements starting with $number
     * @param string $number
     * @param string $count
     * @return string json array
     */
    public function getElementsWithNumber(string $number, string $count)
    {
        if(!is_numeric($count) || !is_numeric($number)) {echo 'number of count\'s quotes is not number'; return null;}
        if((int)$count == 0 || (int)$number == 0) {echo 'number or count can not 0'; return null;}

        $array = [];

        $countPageBeforeNumber = $this->getCountPages($number,$this->countElements);
        $countAllPages = $this->getCountPages($count,$this->countElements);

        $mainIndex = $this->getMainIndex();

        $indexFirstPage = $mainIndex - $countPageBeforeNumber[0] + 1;
        $indexLastPage = $indexFirstPage - $countAllPages[0];

        $htmlPage = HtmlDownload::download(BashInfo::$BASH_URL.'index/'.$indexFirstPage);

      if($countPageBeforeNumber[1] + $count - 1 <= $this->countElements)
      {
          $arr = $this->getQuotesOnPage($htmlPage,$countPageBeforeNumber[1],$count+$countPageBeforeNumber[1]- 1);

          foreach ($arr as $value => $item) $array[] = $item;

          return json_encode($array,JSON_UNESCAPED_UNICODE);
      }


        $arr = $this->getQuotesOnPage($htmlPage,$countPageBeforeNumber[1],$this->countElements - 1);
        $length = count($arr);
        for($j = 0;$j < $length;++$j) if(count($array) < $count) $array[] = $arr[$j];

        for ($i = $indexFirstPage-1;$i > $indexLastPage;--$i)
        {
            $htmlPage = HtmlDownload::download(BashInfo::$BASH_URL.'index/'.$i);
            $arr = $this->getQuotesOnPage($htmlPage,1,$this->countElements);
            $length = count($arr);
            for($j = 0;$j < $length;++$j) if(count($array) < $count) $array[] = $arr[$j];
            unset($arr);
            unset($htmlPage);
            if(count($array) == $count) return json_encode($array,JSON_UNESCAPED_UNICODE);
        }

        if(count($array) == $count) return json_encode($array,JSON_UNESCAPED_UNICODE);

        if($countAllPages[1] != 0)
        {
            $htmlPage = HtmlDownload::download(BashInfo::$BASH_URL.'index/'.$indexLastPage);
            $arr = $this->getQuotesOnPage($htmlPage,1,$countAllPages[1]);
            $length = count($arr);
            for($j = 0;$j < $length;++$j){ if(count($array) < $count) $array[] = $arr[$j]; }
        }

      return json_encode($array,JSON_UNESCAPED_UNICODE);
    }

    /**
     * return json array of elements on random page
     * @return string json array
     */
    public function getRandomElements()
    {
        $linkPage = BashInfo::$BASH_URL.'random';

        $htmlPage = HtmlDownload::download($linkPage);

        $arrayQuotes = $this->getQuotesOnPage($htmlPage,1,$this->countElements);

        unset($htmlPage);
        return json_encode($arrayQuotes,JSON_UNESCAPED_UNICODE);
    }

    /**
     * return json array of elements starting with rating page
     * @param string $count
     * @return string json array
     */
    public function getRatingElementsWithMainPage(string $count)
    {
        if(!is_numeric($count)) {echo 'number of count\'s quotes is not number  '; return null;}
        if((int)$count == 0) {echo 'count can not 0  '; return null;}

        $array = [];
        $linkPage = BashInfo::$BASH_URL.'byrating/';

        $countPage = $this->getCountPages($count,$this->countElements);


        for ($i = 1;$i < $countPage[0];++$i)
        {
            $link = $linkPage;
            $link.=$i;
            $htmlPage = HtmlDownload::download($link);
            $arr = $this->getQuotesOnPage($htmlPage,1,$this->countElements);
            $length = count($arr);
            for($j = 0;$j < $length;++$j) $array[] = $arr[$j];
            unset($arr);
            unset($htmlPage);
        }

        $htmlPage = HtmlDownload::download($linkPage.$countPage[0]);
        if($countPage[1]!=0)
        {
            $arr = $this->getQuotesOnPage($htmlPage,1,$countPage[1]);
            $length = count($arr);
            for($j = 0;$j < $length;++$j) $array[] = $arr[$j];
        }

        return json_encode($array,JSON_UNESCAPED_UNICODE);

    }

    /**
     * return json array of elements starting with $number
     * @param string $number
     * @param string $count
     * @return string json array
     */
    public function getRatingElementsWithNumber(string $number, string $count)
    {
        if(!is_numeric($count) || !is_numeric($number)) {echo 'number of count\'s quotes is not number'; return null;}
        if((int)$count == 0 || (int)$number == 0) {echo 'number or count can not 0'; return null;}

        $array = [];

        $countPageBeforeNumber = $this->getCountPages($number,$this->countElements);
        $countAllPages = $this->getCountPages($count,$this->countElements);

        $indexFirstPage = $countPageBeforeNumber[0];
        $indexLastPage = $indexFirstPage + $countAllPages[0];


        $htmlPage = HtmlDownload::download(BashInfo::$BASH_URL.'byrating/'.$indexFirstPage);

        if($countPageBeforeNumber[1] + $count - 1 <= $this->countElements)
        {
            $arr = $this->getQuotesOnPage($htmlPage,$countPageBeforeNumber[1],$count+$countPageBeforeNumber[1]- 1);
            foreach ($arr as $value => $item) $array[] = $item;
            return json_encode($array,JSON_UNESCAPED_UNICODE);
        }

        $arr = $this->getQuotesOnPage($htmlPage,$countPageBeforeNumber[1],$this->countElements - 1);

        $length = count($arr);
        for($j = 0;$j < $length;++$j) if(count($array) < $count) $array[] = $arr[$j];

        for ($i = $indexFirstPage+1;$i < $indexLastPage;++$i)
        {
            $htmlPage = HtmlDownload::download(BashInfo::$BASH_URL.'byrating/'.$i);
            $arr = $this->getQuotesOnPage($htmlPage,1,$this->countElements);

            $length = count($arr);
            for($j = 0;$j < $length;++$j) if(count($array) < $count) $array[] = $arr[$j];

            unset($arr);
            unset($htmlPage);
            if(count($array) == $count) return json_encode($array,JSON_UNESCAPED_UNICODE);
        }

        if(count($array) == $count) return json_encode($array,JSON_UNESCAPED_UNICODE);

        if($countAllPages[1] != 0)
        {
            $htmlPage = HtmlDownload::download(BashInfo::$BASH_URL.'byrating/'.$indexLastPage);
            $arr = $this->getQuotesOnPage($htmlPage,1,$countAllPages[1]);
            $length = count($arr);
            for($j = 0;$j < $length;++$j){ if(count($array) < $count) $array[] = $arr[$j]; }
        }

        return json_encode($array,JSON_UNESCAPED_UNICODE);
    }

    /**
     * return json object by his id
     * @return string json object
     */
    public function getElementById(string $id)
    {
        $linkPage = BashInfo::$BASH_URL.'quote/'.$id;

        $htmlPage = HtmlDownload::download($linkPage);
        if($htmlPage==null) {return null;};

        $parseArray = $this->getQuotesOnPage($htmlPage,1,1);
        $jsonObject = json_encode($parseArray[0],JSON_UNESCAPED_UNICODE);

        return $jsonObject;
    }

    /**
     * return json array of elements on abyss page
     * @return string json array
     */
    public function getAbyssElements()
    {
        $linkPage = BashInfo::$BASH_URL.'abyss';

        $htmlPage = HtmlDownload::download($linkPage);
        $countQuotes = $this->getCountQuotesOnPage($htmlPage);

        $arrayQuotes = $this->getQuotesOnPage($htmlPage,1,$countQuotes,BashInfo::$TagText,'span.id');

        unset($htmlPage);
        return json_encode($arrayQuotes,JSON_UNESCAPED_UNICODE);
    }

    /**
     * return json array of elements on top abyss page
     * @return string json array
     */
    public function getTopAbyssElements()
    {
        $linkPage = BashInfo::$BASH_URL.'abysstop';

        $htmlPage = HtmlDownload::download($linkPage);

        $array = [];

        $parseArray = HtmlParser::parses(iconv("windows-1251", "UTF-8", $htmlPage),[BashInfo::$TagText,'span.abysstop','span.abysstop-date']);

        $quote = null;$id = null;$date = null;$text = null;
        $length = count($parseArray);

        for ($i = 0;$i < $length;++$i)
        {
            switch ($parseArray[$i]->class)
            {
                case 'abysstop-date':
                {
                    $date = $parseArray[$i]->innertext; break;
                }
                case 'abysstop':
                {
                    $id = $parseArray[$i]->innertext; break;
                }
                case 'text':
                {
                    $text = str_replace(['&quot;', '<br />', '<br>', '/>', '\\', '\'', '\"'], ' ', $parseArray[$i]->innertext);
                    break;
                }
            }
            if($id != null && $date != null && $text != null)
            {
                $quote = new Quote($id,$text,null,$date);
                $array[] = $quote;
                $quote = null;$id = null;$date = null;$text = null;
            }
        }

       unset($htmlPage);
       return json_encode($array,JSON_UNESCAPED_UNICODE);
    }

    /**
     * return array links on picture with number to count
     * @param $number
     * @param $count
     * @return string json array
     */
    public function getComicsElements($number, $count)
    {
        //TODO done method
        $htmlPage = HtmlDownload::download(BashInfo::$BASH_URL.'comics/');
        $array = HtmlParser::parse(iconv("windows-1251", "UTF-8", $htmlPage),'img[id=cm_strip]');
        $str = $array[0];
        $str= str_replace(['img src="','"','id=cm_strip />','<'],'',$str);

        return json_encode($array,JSON_UNESCAPED_UNICODE);
    }

    public function getComicsForQuotes($id)
    {

        //TODO done method
        $linkPage = BashInfo::$BASH_URL.'quote/'.$id;

        $htmlPage = HtmlDownload::download($linkPage);
        if($htmlPage==null) {return null;};

        $parseArray = $this->getQuotesOnPage($htmlPage,1,1);

        $date = $parseArray[0]->date;

        $length = strlen($date);
        $time = substr($date,$length-5);
        $date = str_replace([$time,'-',' '],'',$date);

        $comicsPage = HtmlDownload::download(BashInfo::$BASH_URL.'comics/'.$date);

        $arr = HtmlParser::parse(iconv("windows-1251", "UTF-8", $comicsPage),'title');

        $title = '';
        foreach ($arr as $value) $title.=$value->innertext;


        $beginId = 0;$countId = 0;$findId = false;

        for ($i = 0,$length = strlen($title);$i < $length;++$i)
        {
            if($findId && !is_numeric($title[$i])) {break;}
            if($title[$i] == '#') {$beginId = $i;$findId = true;}

            if($findId) $countId++;
        }



        $title = substr($title,$beginId,$countId);
        $title = str_replace('#','',$title);

        if($title == $id)
        {

        }

        echo $title;

       // $jsonObject = json_encode($parseArray[0],JSON_UNESCAPED_UNICODE);
    }
}