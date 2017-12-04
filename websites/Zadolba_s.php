<?php
/**
 * Created by PhpStorm.
 * User: dubrovin
 * Date: 07.11.17
 * Time: 17:27
 */

class Zadolba_s extends WebSite
{

    public function __construct()
    {
        $this->countElements = ZadolbaInfo::getCountStory();
    }

    /**
     * @return int main index
     */
    public function getMainIndex()
    {
       $htmlPage = HtmlDownload::download(ZadolbaInfo::$ZADOLBA_URL);
       $arrayPage = HtmlParser::parse($htmlPage,'body');
       $htmlPage = '';
       foreach ($arrayPage as $value) $htmlPage.=$value;

       $index = stripos($htmlPage,'>');
       $body = substr($htmlPage,0,$index+1);

       $indexBefore = stripos($body,'data-today-date=');
       $indexBefore+=17;

       $index = '';

       for ($i=$indexBefore;$i<$indexBefore+8;++$i)
       {
           $index.=$body[$i];
       }

       return $index;
    }

    /**
     * @return $index of main story
     */
    public function getMainIndexStory()
    {
        $htmlPage = HtmlDownload::download(ZadolbaInfo::$ZADOLBA_URL);
        $arrayPage = HtmlParser::parse($htmlPage,'div.id');
        $index = $arrayPage[0]->innertext;
        $index = str_replace([' <span>','</span> '],'',$index);
        return $index;
    }

    /**
     * return array of Story with first to last
     * @param $html_page
     * @param int $first
     * @param int $last
     * @param string $tagText
     * @param string $tagId
     * @param string $tagDate
     * @param string $tagRate
     * @param string $tagHead
     * @param string $tagTags
     * @return array|null
     */
    public function getStoriesOnPage(&$html_page, int $first, int $last , $tagText = 'div.text', $tagId = 'div.id', $tagDate = 'div.date-time', $tagRate = 'div.rating', $tagHead = 'h2', $tagTags = 'div.tags')
    {
        $array = [];
        if ($first == 0 || $last == 0) {echo 'first or last can\'t be 0  ';return null;}
        if ($first > $last) {echo "first($first) > last($last)";return null;}

        $first--;

        $parseArray = HtmlParser::parses($html_page, [$tagText, $tagId, $tagDate, $tagRate, $tagHead, $tagTags]);
        foreach ($parseArray as $value) {
            if($value->tag == 'h2')
            {
                $value = '(h2)'.substr($value,0,strlen($value));
                continue;
            }

            switch ($value->class) {
                case 'date-time':
                    {
                        //$value = '(date)'.substr($value,0,strlen($value));
                        $value = '44';
                        break;
                    }
                case 'id':
                    {
                        $value = '(id)'.substr($value,0,strlen($value));
                        break;
                    }
                case 'rating':
                    {
                        $value = '(rating)'.substr($value,0,strlen($value));
                        break;
                    }
                case 'text':
                    {
                        //$value = '(text)'.substr($value,0,strlen($value));
                        $value = 'text';
                        break;
                    }
                case 'tags':
                    {
                        $value = '(tags)'.substr($value,0,strlen($value));
                    }
            }

        }

        foreach ($parseArray as $value) echo $value,'      ';

        /*$story = null;
        $id = null;
        $date = null;
        $text = null;
        $rate = null;
        $head = null;
        $tags = null;
        $length = count($parseArray);


        for ($i = 0; $i < $length; ++$i) {

            if($parseArray[$i]->tag == 'h2')
            {
                $head = $parseArray[$i]->innertext;
                $index = stripos($head,'>');
                $head = str_replace([substr($head,0,$index+1),'</a>'],'',$head);
            }

            switch ($parseArray[$i]->class) {
                case 'date-time':
                {
                    $date = $parseArray[$i]->innertext;
                    break;
                }
                case 'id':
                {
                    $id = str_replace([' <span>', '</span> '], '', $parseArray[$i]->innertext);
                    break;
                }
                case 'rating':
                {
                    $rate = $parseArray[$i]->innertext;
                    $rate = str_replace(['<span>', '</span>'], '', $rate);
                    break;
                }
                case 'text':
                {
                    $text = $parseArray[$i]->innertext;
                    $text = str_replace(['&quot;','<br />','<br>','/>','\\','\'','\"','<\/p>  <p>','<\/p>',"\""],' ',$text);
                    $text = strip_tags($text);
                    break;
                }
                case 'tags':
                {
                    $tags = '&&';
                    foreach (HtmlParser::parse($parseArray[$i], 'ul li a') as $item) {
                        $item = str_replace(['<a href="/', '</a>'], '', $item);
                        $item = str_replace(['">',], '||', $item);
                        $tags .= $item;
                        $tags .= '&&';
                    }
                }
            }

            if ($id != null && $date != null && $rate != null && $text != null && $head != null && $tags != null) {
                $story = new Story($id, $date, $tags, $text, $rate, $head);
                $array[] = $story;
                $story = null;
                $id = null;
                $date = null;
                $text = null;
                $rate = null;
                $head = null;
                $tags = null;
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

        return $array1;*/

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

        $mainDateIndex = $this->getMainIndex();
        $mainDate = substr($mainDateIndex, 0, 4)."-".substr($mainDateIndex, 4, 2).'-'.substr($mainDateIndex, 6, 2);

        $mainDate = new DateTime($mainDate);

        $countPage = $this->getCountPages($count,$this->countElements);
        print_r($countPage);

       for ($i = 0;$i < $countPage[0] - 1;++$i)
        {
            $mainDate->modify("-$i day");
            $localDate = $mainDate->format('Y-m-d');
            $localDate = str_replace('-','',$localDate);

            $htmlPage = HtmlDownload::download(ZadolbaInfo::$ZADOLBA_URL.$localDate);

            $arr = $this->getStoriesOnPage($htmlPage,1,$this->countElements);
            $length = count($arr);
            for($j = 0;$j < $length;++$j) $array[] = $arr[$j];
            unset($arr);
            unset($htmlPage);
        }


        if($countPage[1] != 0)
        {
            $subDay = 1 - $countPage[0];
            $mainDate->modify("-$subDay day");
            $localDate = $mainDate->format('Y-m-d');
            $localDate = str_replace('-','',$localDate);
            $htmlPage = HtmlDownload::download(ZadolbaInfo::$ZADOLBA_URL.$localDate);
            $arr = $this->getStoriesOnPage($htmlPage,1,$countPage[1]);
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
    public function getElementsWithNumber(string $number, string $count)
    {
        // TODO: Implement getElementsWithNumber() method.
    }

    /**
     * return json array of elements on random page
     * @return string json array
     */
    public function getRandomElements()
    {
        // TODO: Implement getRandomElements() method.
    }

    /**
     * return json object by id
     * @param string $id
     * @return string json object
     */
    public function getElementById(string $id)
    {
        if($id == null) echo 'id isn\'t null';
        $linkPage = ZadolbaInfo::$ZADOLBA_URL.'story/'.$id;

        $htmlPage = HtmlDownload::download($linkPage);
        if($htmlPage==null) {return null;};

        //TODO: done this method

        //$jsonObject = json_encode($parseArray[0],JSON_UNESCAPED_UNICODE);

      //  return $jsonObject;
    }

    /**
     * return json array of elements starting with rating page
     * @param string $count
     * @return string json array
     */
    public function getRatingElementsWithMainPage(string $count)
    {
        // TODO: Implement getRatingElementsWithMainPage() method.
    }

    /**
     * return json array of elements starting with $number
     * @param string $number
     * @param string $count
     * @return string json array
     */
    public function getRatingElementsWithNumber(string $number, string $count)
    {
        // TODO: Implement getRatingElementsWithNumber() method.
    }

}