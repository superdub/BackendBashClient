<?php
/**
 * Created by PhpStorm.
 * User: dubrovin
 * Date: 07.11.17
 * Time: 17:17
 */
include_once 'model/HtmlParser.php';
include_once 'model/HtmlDownload.php';
include_once 'model/BashQuotes.php';

class Bash_s
{

    private $countQuotes;

    function __construct()
    {
        try {
            $this->countQuotes = BashInfo::getCountQuotes();
        }
        catch (Exception $exception)
        {
            $this->countQuotes = BashInfo::$BASH_COUNT;
        }
    }


    /**
     * @param $ost count of Quotes
     * @param $del count of Quotes on one page
     * @return array of count page and quotes on last page
     */
    private function getCountPages(int $ost, int $del)
    {
        return [floor($ost/$del) + 1,$ost%$del];
    }


    /**
     * this function parse page for BashQuotes
     */
    private function parseForBashQuotes(HtmlParser &$parser, &$html_page, &$dates, &$texts, &$likes, &$ids)
    {
        foreach($parser->parse(iconv("windows-1251", "UTF-8", $html_page),'div.text') as $text1)
        {
            $texts[] = $text1->innertext;
        }
        foreach($parser->parse(iconv("windows-1251", "UTF-8", $html_page),'span.rating') as $text1)
        {
            $likes[] = $text1->innertext;
        }
        foreach($parser->parse(iconv("windows-1251", "UTF-8", $html_page),'span.date') as $text1)
        {
            $dates[] = $text1->innertext;
        }
        foreach($parser->parse(iconv("windows-1251", "UTF-8", $html_page),'a.id') as $text1)
        {
            $ids[] = $text1->innertext;
        }
    }


    /**
     * @return int index Bash.im main page
     */
    private function getMainIndexPage()
    {
        $html_download = new HtmlDownload();
        $htmlParser = new HtmlParser();
        $html_page = $html_download -> download(BashInfo::$BASH_URL);
        foreach($htmlParser->parse(iconv("windows-1251", "UTF-8", $html_page),'div.pager') as $text1) {
            foreach ($htmlParser->parse(iconv("windows-1251", "UTF-8", $text1), 'form') as $text2) {
                $mas = $htmlParser->parse(iconv("windows-1251", "UTF-8", $text2), '.page');
                $str = (int)strripos($mas[0], 'value');
                $str = substr($mas[0], $str, $str + 5);
                $str = str_replace('value="', '', $str);
                $str = str_replace('" />', '', $str);
                unset($html_download);
                unset($htmlParser);
                return (int)$str;
            }
        }
        echo 'error; must not get index of main page';
        return null;
    }


    /**
     * @return array  of Quotes from $number to count
     */
    private function getQuotesWithNumberToOver($linkPageFirst, $number, $count, HtmlParser &$html_parser,HtmlDownload &$html_download)
    {
        $html_page = $html_download->download($linkPageFirst);

        $array = [];
        $_likes =[];
        $_texts =[];
        $_dates =[];
        $_ids =[];

        $this->parseForBashQuotes($html_parser,$html_page,$_dates,$_texts,$_likes,$_ids);


        $likes =[];
        $texts =[];
        $dates =[];
        $ids =[];


        for ($i = $number; $i <= $count; $i++)
        {
           if($_texts[$i]!=null)
           {
               $likes[] = $_likes[$i];
               $texts[] = $_texts[$i];
               $dates[] = $_dates[$i];
               $ids[] = $_ids[$i];
           }
        }


        $array['likes'] = $likes;
        $array['texts'] = $texts;
        $array['dates'] = $dates;
        $array['ids'] = $ids;


        return $array;
    }


    /**
     * @param string $parameters
     * @return string json of Quotes from Bash.im main web page with first quotes on main page
     */
    public function getQuotesWithMain(string $parameters)
    {
        if(!is_numeric($parameters)) {echo 'parameters is not number'; return null;}

        if((int)$parameters == 0) {echo 'parameters can not 0'; return null;}

        if($parameters != null && strlen($parameters) > 0)
        {
            $html_download = new HtmlDownload();
            $htmlParser = new HtmlParser();
            $array = new BashQuotes();

            $parameters = (int)$parameters;

            $count = $this->getCountPages($parameters,$this->countQuotes);

            $likes =[];
            $texts =[];
            $dates =[];
            $ids =[];

            for($i=0;$i<$count[0]-1;$i++)
            {
                $index = $this->getMainIndexPage() - $i;
                $html_page = $html_download -> download(BashInfo::$BASH_URL.'index/'.$index);
                $this->parseForBashQuotes($htmlParser,$html_page,$dates,$texts,$likes,$ids);
            }


            $index = $this->getMainIndexPage() - $count[0]+1;
            $html_page = $html_download -> download(BashInfo::$BASH_URL.'index/'.$index);



            $last_array = $this->getQuotesWithNumberToOver($html_page,0,$count[1],$htmlParser,$html_download);

            foreach ($last_array as $item => $value)
            {

                if($item == 'likes') {foreach ($value as $like) $likes[] = $like;}
                if($item == 'texts') {foreach ($value as $text) $texts[] = $text;}
                if($item == 'dates') {foreach ($value as $date) $dates[] = $date;}
                if($item == 'ids') {foreach ($value as $id) $ids[] = $id;}
            }


            for ($i=0;$i<$parameters;$i++)
            {
                $array->Add($ids[$i],$texts[$i],$likes[$i],$dates[$i]);
            }


            unset($html_download);
            unset($htmlParser);
            unset($ids);
            unset($texts);
            unset($likes);
            unset($dates);
            return $array->Get();
        }
        echo 'parameters is null or wrong';
        return null;
    }


    /**
     * @param string $number
     * @param string $count
     * @return string json quotes from $number to $count with main page
     */
    public function getQuotesWithNumber(string $number,string $count)
    {
        if(!is_numeric($number) || !is_numeric($count)) {echo 'parameters is not number'; return null;}

        if((int)$number == 0) {echo 'number of first quotes can not 0';return null;}

        if($count != null && strlen($count) > 0 && $number != null && strlen($number) > 0 && $number > 0 && $count > 0) {

            $html_parser = new HtmlParser();
            $html_download = new HtmlDownload();


            $number = (int)$number;
            $count = (int)$count;


            $countPageBeforeFirst = $this->getCountPages($number,$this->countQuotes);
            $countPageAfterFirst = $this->getCountPages($number+$count,$this->countQuotes);


            $indexPageBefore = $this->getMainIndexPage() - $countPageBeforeFirst[0]+1;


            $linkPageFirst = BashInfo::$BASH_URL.'index/'.$indexPageBefore;


            $html_page = $html_download -> download($linkPageFirst);


            $array = new BashQuotes();

            $likes =[]; $_likes =[];
            $texts =[];  $_texts =[];
            $dates =[];  $_dates =[];
            $ids =[];  $_ids =[];




           if($countPageAfterFirst[1] > $countPageBeforeFirst[1] && $countPageAfterFirst[0]  == $countPageBeforeFirst[0]) {

               $this->parseForBashQuotes($html_parser,$html_page,$dates,$texts,$likes,$ids);

               for ($i = $countPageBeforeFirst[1]-1; $i < $countPageAfterFirst[1]-1; $i++)
               {
                   $array->Add($ids[$i],$texts[$i],$likes[$i],$dates[$i]);
               }

               unset($html_page);
               unset($html_download);
               unset($likes);
               unset($texts);
               unset($dates);
               unset($ids);
               unset($html_parser);
               return $array->Get();
            }


            $_array = $this->getQuotesWithNumberToOver($linkPageFirst,$countPageBeforeFirst[1],$this->countQuotes-1,$html_parser,$html_download);

           foreach ($_array as $item => $value)
           {

               if($item == 'likes') {foreach ($value as $like) $likes[] = $like;}
               if($item == 'texts') {foreach ($value as $text) $texts[] = $text;}
               if($item == 'dates') {foreach ($value as $date) $dates[] = $date;}
               if($item == 'ids') {foreach ($value as $id) $ids[] = $id;}
           }


            if(count($texts) == $count)
            {
                for($i=0;$i<$count;$i++)
                {
                    $array->Add($ids[$i],$texts[$i],$likes[$i],$dates[$i]);
                }
                return $array->Get();
            }

            for($i=$countPageBeforeFirst[0]+1;$i<$countPageAfterFirst[0];$i++)
            {
                $index = $indexPageBefore - $i;
                $link = BashInfo::$BASH_URL.'index/'.$index;
                $local_array = $this->getQuotesWithNumberToOver($link,0,$this->countQuotes-1,$html_parser,$html_download);
                foreach ($local_array as $item => $value)
                {

                    if($item == 'likes') {foreach ($value as $like) $likes[] = $like;}
                    if($item == 'texts') {foreach ($value as $text) $texts[] = $text;}
                    if($item == 'dates') {foreach ($value as $date) $dates[] = $date;}
                    if($item == 'ids') {foreach ($value as $id) $ids[] = $id;}
                }
            }
            if(count($texts) == $count)
            {
                for($i=0;$i<$count;$i++)
                {
                    $array->Add($ids[$i],$texts[$i],$likes[$i],$dates[$i]);
                }
                echo count($texts);
                return $array->Get();
            }


            $index = $this->getMainIndexPage()-$countPageAfterFirst[0];

            $html_page = $html_download->download(BashInfo::$BASH_URL.'index/'.BashInfo::$BASH_URL.'index/'.$index);
            $this->parseForBashQuotes($html_parser,$html_page,$_dates,$_texts,$_likes,$_ids);



            for($i=0;$i<=$countPageAfterFirst[1];$i++)
            {
                if($_texts[$i]!=null)
                {
                    $likes[] = $_likes[$i];
                    $texts[] = $_texts[$i];
                    $dates[] = $_dates[$i];
                    $ids[] = $_ids[$i];
                }
            }



            for($i=0;$i<$count;$i++)
            {
                $array->Add($ids[$i],$texts[$i],$likes[$i],$dates[$i]);
            }

            unset($html_page);
            unset($html_download);
            unset($html_parser);
            unset($texts); unset($_texts);
            unset($likes);  unset($_likes);
            unset($dates); unset($_dates);
            unset($ids); unset($_ids);
            unset($_array);
            unset($local_array);
            return $array->Get();

        }
        echo 'parameters is wrong or null';
        return null;
    }


    /**
     * @param string $id
     * @return string json quote by id but this function can not find quote
     */
    public function getQuotesById(string $id)
    {
        if(!is_numeric($id)) {echo 'parameters is not number'; return null;}
        if($id != null && strlen($id) > 0 && (int)$id != 0 && (int)$id > 0) {
            $html_parser = new HtmlParser();
            $html_download = new HtmlDownload();
            $quote = new BashQuotes();
            $text = [];
            $id = [];
            $like = [];
            $date = [];

            $url = BashInfo::$BASH_URL.'quote/'.$id;
            $html_page = $html_download -> download($url);

            if(strlen($html_page) <= 0) {echo 'wrong this quote don\'t found'; return null;}
            $this->parseForBashQuotes($html_parser,$html_page,$date,$text,$like,$id);
            $quote->Add($id[0],$text[0],$like[0],$date[0]);

            unset($html_page);
            unset($html_download);
            unset($html_parser);
            return $quote->Get();
        }
        echo 'parameters is null or wrong'; return null;
    }


    /**
     * @param string $number
     * @param string $count
     * @return string json all quotes by random page
     */
    public function getRandomQuotes()
    {

        $html_parser = new HtmlParser();
        $html_download = new HtmlDownload();
        $html_page = $html_download->download(BashInfo::$BASH_URL.'random/');
        $array = new BashQuotes();

        $likes =[];
        $texts =[];
        $dates =[];
        $ids =[];

        $this->parseForBashQuotes($html_parser,$html_page,$dates,$texts,$likes,$ids);


        for ($i = 0; $i < $this->countQuotes; $i++)
        {
            $array->Add($ids[$i],$texts[$i],$likes[$i],$dates[$i]);
        }

        unset($html_parser);
        unset($html_download);
        unset($html_page);
        unset($likes);
        unset($texts);
        unset($dates);
        unset($ids);
        return $array->Get();

    }


    /**
     * @param string $number
     * @param string $count
     * @return string json random some quotes
     */
    public function getRandomQuotesWithNumber(string $count)
    {
        if(!is_numeric($count)) {echo 'parameters is not number'; return null;}
        if((int)$count == 0) {echo 'number of first quotes can not 0';return null;}

        if($count != null && strlen($count) > 0 && $count > 0) {
            $html_parser = new HtmlParser();
            $html_download = new HtmlDownload();
            $count = (int)$count;

            $array = new BashQuotes();
            $count_page = $this->getCountPages($count,$this->countQuotes);

            $likes =[];
            $texts =[];
            $dates =[];
            $ids =[];
            $_likes =[];
            $_texts =[];
            $_dates =[];
            $_ids =[];

            for($i=0;$i<$count_page[0]-1;$i++)
            {

                $html_page = $html_download->download(BashInfo::$BASH_URL.'random/');

                $this->parseForBashQuotes($html_parser,$html_page,$dates,$texts,$likes,$ids);
            }

            $html_page = $html_download->download(BashInfo::$BASH_URL.'random/');
            $this->parseForBashQuotes($html_parser,$html_page,$_dates,$_texts,$_likes,$_ids);

            for($i = 0;$i<$count_page[1];$i++)
            {
                $likes[] = $_likes[$i];
                $texts[] = $_texts[$i];
                $dates[] = $_dates[$i];
                $ids[] = $_ids[$i];
            }
            for($i = 0;$i<$count;$i++)
            {
                $array->Add($ids[$i],$texts[$i],$likes[$i],$dates[$i]);
            }
            return $array->Get();
        }
        echo 'parameters is wrong or null';
        return null;
    }



    //todo дописать метод
    public function getRatingQuotesWithMain(string $parameters)
    {
        if(!is_numeric($parameters)) {echo 'parameters is not number'; return null;}
        if($parameters != null && strlen($parameters) > 0)
        {
            $html_download = new HtmlDownload();
            $htmlParser = new HtmlParser();
            $array = new BashQuotes();
            $parameters = (int)$parameters;
            $count = $this->getCountPages($parameters,$this->countQuotes);
            $likes =[];
            $texts =[];
            $dates =[];
            $ids =[];
            for($i=1;$i<$count[0]-1;$i++)
            {
                $index = $this->getMainIndexPage() + $i;
                $html_page = $html_download -> download(BashInfo::$BASH_URL.'byrating/'.$index);
                $this->parseForBashQuotes($html_parser,$html_page,$dates,$texts,$likes,$ids);
            }
            $index = 1 + $count[0]+1;
            $html_page = $html_download -> download(BashInfo::$BASH_URL.'byrating/'.$index);
            echo $index,'          ';
            $i = $count[1];


            //улучшить код

            foreach($htmlParser->parse(iconv("windows-1251", "UTF-8", $html_page),'div.text') as $text1)
            {
                if(!$i) break;
                $i--;
                $texts[] = $text1->innertext;
            }
            $i = $count[1];
            foreach($htmlParser->parse(iconv("windows-1251", "UTF-8", $html_page),'span.rating') as $text1)
            {
                if(!$i) break;
                $i--;
                $likes[] = $text1->innertext;
            }
            $i = $count[1];
            foreach($htmlParser->parse(iconv("windows-1251", "UTF-8", $html_page),'span.date') as $text1)
            {
                if(!$i) break;
                $i--;
                $dates[] = $text1->innertext;
            }
            $i = $count[1];
            foreach($htmlParser->parse(iconv("windows-1251", "UTF-8", $html_page),'a.id') as $text1)
            {
                if(!$i) break;
                $i--;
                $ids[] = $text1->innertext;
            }

            for ($i=0;$i<$parameters;$i++)
            {
                $array->Add($ids[$i],$texts[$i],$likes[$i],$dates[$i]);
            }

            unset($html_download);
            unset($htmlParser);
            unset($ids);
            unset($texts);
            unset($likes);
            unset($dates);
            return $array->Get();
        }
        echo 'parameters is null or wrong';
        return null;
    }

}