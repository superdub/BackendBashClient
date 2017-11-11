<?php
/**
 * Created by PhpStorm.
 * User: dubrovin
 * Date: 07.11.17
 * Time: 17:17
 */
include_once 'model/HtmlParser.php';
include_once 'model/HtmlDownload.php';
include_once 'data/BashQuotes.php';

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
     * @param $del count Quotes on one page
     * @return array of count page and quotes on last page
     */
    private function getCountPages(int $ost, int $del)
    {
        return [floor($ost/$del) + 1,$ost%$del];
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
        return null;
    }


    /**
     * @param $linkPageFirst
     * @param $number
     * @param $html_parser
     * @param $html_download
     * @return array  of Quotes from $number to over
     */
    private function getQuotesWithNumberToOver($linkPageFirst, $number, &$html_parser, &$html_download)
    {
        $html_page = $html_download -> download($linkPageFirst);
        $array = [];
        $_likes =[];
        $_texts =[];
        $_dates =[];
        $_ids =[];
        foreach ($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page), 'span.rating') as $text1) {
            $_likes[] = $text1->innertext;
        }
        foreach ($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page), 'div.text') as $text1) {
            $_texts[] = $text1->innertext;
        }
        foreach ($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page), 'span.date') as $text1) {
            $_dates[] = $text1->innertext;
        }
        foreach ($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page), 'a.id') as $text1) {
            $_ids[] = $text1->innertext;
        }

        for ($i = 0; $i < $number; $i++)
        {
            $_likes[$i] = null;
            $_texts[$i] = null;
            $_dates[$i] = null;
            $_ids[$i] = null;

        }
        $likes =[];
        $texts =[];
        $dates =[];
        $ids =[];
        foreach ($_texts as $text)
        {
            if($text != null)
            {
                $texts[] = $text;
            }
        }
        foreach ($_likes as $like)
        {
            if($like != null)
            {
                $likes[] = $like;
            }
        }
        foreach ($_dates as $date)
        {
            if($date != null)
            {
                $dates[] = $date;
            }
        }
        foreach ($_ids as $id)
        {
            if($id != null)
            {
                $ids[] = $id;
            }
        }
        $array['likes'] = $likes;
        $array['texts'] = $texts;
        $array['dates'] = $dates;
        $array['ids'] = $ids;

        return $array;
    }


    /**
     * @param $linkPageFirst
     * @param $number
     * @return array of Quotes from $number to over  with creating object HtmlParser and HtmlDownload
     */
    private function getQuotesWithNumberToOverWithParserAndDownload($linkPageFirst, $number)
    {
        $html_download = new HtmlDownload();
        $htmlParser = new HtmlParser();
        $html_page = $html_download -> download($linkPageFirst);
        $array = [];
        $_likes =[];
        $_texts =[];
        $_dates =[];
        $_ids =[];
        foreach ($htmlParser->parse(iconv("windows-1251", "UTF-8", $html_page), 'span.rating') as $text1) {
            $_likes[] = $text1->innertext;
        }
        foreach ($htmlParser->parse(iconv("windows-1251", "UTF-8", $html_page), 'div.text') as $text1) {
            $_texts[] = $text1->innertext;
        }
        foreach ($htmlParser->parse(iconv("windows-1251", "UTF-8", $html_page), 'span.date') as $text1) {
            $_dates[] = $text1->innertext;
        }
        foreach ($htmlParser->parse(iconv("windows-1251", "UTF-8", $html_page), 'a.id') as $text1) {
            $_ids[] = $text1->innertext;
        }

        for ($i = 0; $i < $number; $i++)
        {
            $_likes[$i] = null;
            $_texts[$i] = null;
            $_dates[$i] = null;
            $_ids[$i] = null;

        }
        $likes =[];
        $texts =[];
        $dates =[];
        $ids =[];
        foreach ($_texts as $text)
        {
            if($text != null)
            {
                $texts[] = $text;
            }
        }
        foreach ($_likes as $like)
        {
            if($like != null)
            {
                $likes[] = $like;
            }
        }
        foreach ($_dates as $date)
        {
            if($date != null)
            {
                $dates[] = $date;
            }
        }
        foreach ($_ids as $id)
        {
            if($id != null)
            {
                $ids[] = $id;
            }
        }
        $array['likes'] = $likes;
        $array['texts'] = $texts;
        $array['dates'] = $dates;
        $array['ids'] = $ids;
        unset($html_download);
        unset($htmlParser);
        return $array;
    }

    /**
     * @param string $parameters
     * @return string json of Quotes from Bash.im main web page with first quotes on main page
     */
    public function getQuotesWithMain(string $parameters)
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
            for($i=0;$i<$count[0]-1;$i++)
            {
                $index = $this->getMainIndexPage() - $i;
                $html_page = $html_download -> download(BashInfo::$BASH_URL.'index/'.$index);
                foreach($htmlParser->parse(iconv("windows-1251", "UTF-8", $html_page),'div.text') as $text1)
                {
                    $texts[] = $text1->innertext;
                }
                foreach($htmlParser->parse(iconv("windows-1251", "UTF-8", $html_page),'span.rating') as $text1)
                {
                    $likes[] = $text1->innertext;
                }
                foreach($htmlParser->parse(iconv("windows-1251", "UTF-8", $html_page),'span.date') as $text1)
                {
                    $dates[] = $text1->innertext;
                }
                foreach($htmlParser->parse(iconv("windows-1251", "UTF-8", $html_page),'a.id') as $text1)
                {
                    $ids[] = $text1->innertext;
                }
            }
            $index = $this->getMainIndexPage() - $count[0]+1;
            $html_page = $html_download -> download(BashInfo::$BASH_URL.'index/'.$index);
            $i = $count[1];
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
          //  $indexPageAfter = $this->getMainIndexPage() - $countPageAfterFirst[0]+1;

            $linkPageFirst = BashInfo::$BASH_URL.'index/'.$indexPageBefore;
           // $linkPageLast = BashInfo::$BASH_URL.'index/'.$indexPageAfter;


            $html_page = $html_download -> download($linkPageFirst);


            $array = new BashQuotes();
            $likes =[]; $_likes =[];
            $texts =[];  $_texts =[];
            $dates =[];  $_dates =[];
            $ids =[];  $_ids =[];




           if($countPageAfterFirst[1] > $countPageBeforeFirst[1] && $countPageAfterFirst[0]  == $countPageBeforeFirst[0]) {

               foreach($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page),'div.text') as $text1)
               {
                   $texts[] = $text1->innertext;
               }
               foreach($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page),'span.rating') as $text1)
               {
                   $likes[] = $text1->innertext;
               }
               foreach($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page),'span.date') as $text1)
               {
                   $dates[] = $text1->innertext;
               }
               foreach($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page),'a.id') as $text1)
               {
                   $ids[] = $text1->innertext;
               }

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


            $_array = $this->getQuotesWithNumberToOver($linkPageFirst,$countPageBeforeFirst[1],$html_parser,$html_download);

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
                $local_array = $this->getQuotesWithNumberToOver($link,0,$html_parser,$html_download);
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
                return $array->Get();
            }


            $index = $this->getMainIndexPage()-$countPageAfterFirst[0];

            $html_page = $html_download->download(BashInfo::$BASH_URL.'index/'.BashInfo::$BASH_URL.'index/'.$index);
            foreach($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page),'div.text') as $text1)
            {
                $_texts[] = $text1->innertext;
            }
            foreach($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page),'span.rating') as $text1)
            {
                $_likes[] = $text1->innertext;
            }
            foreach($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page),'span.date') as $text1)
            {
                $_dates[] = $text1->innertext;
            }
            foreach($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page),'a.id') as $text1)
            {
                $_ids[] = $text1->innertext;
            }
            for($i=$countPageAfterFirst[1];$i<$this->countQuotes;$i++)
            {
                    $_likes[$i] = null;
                    $_texts[$i] = null;
                    $_dates[$i] = null;
                    $_ids[$i] = null;
            }


            foreach ($_texts as $text)
            {
                if($text != null)
                {
                    $texts[] = $text;
                }
            }
            foreach ($_likes as $like)
            {
                if($like != null)
                {
                    $likes[] = $like;
                }
            }
            foreach ($_dates as $date)
            {
                if($date != null)
                {
                    $dates[] = $date;
                }
            }
            foreach ($_ids as $id)
            {
                if($id != null)
                {
                    $ids[] = $id;
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
            $_text = '';
            $_id = '';
            $_like = '';
            $_date = '';
            $url = BashInfo::$BASH_URL.'quote/'.$id;
            $html_page = $html_download -> download($url);
            if(strlen($html_page) <= 0) {echo 'wrong this quote don\'t found'; return null;}
            foreach ($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page), 'div.text') as $text1) {
                $_text .= $text1->innertext;
            }
            foreach ($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page), 'span.rating') as $text1) {
                $_like .= $text1->innertext;
            }
            foreach ($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page), 'span.date') as $text1) {
                $_date .= $text1->innertext;
            }
            foreach ($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page), 'a.id') as $text1) {
                $_id .= $text1->innertext;
            }
            $quote->Add($_id,$_text,$_like,$_date);
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
     * @return string json quotes by random page
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

        foreach($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page),'div.text') as $text1)
        {
            $texts[] = $text1->innertext;
        }
        foreach($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page),'span.rating') as $text1)
        {
            $likes[] = $text1->innertext;
        }
        foreach($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page),'span.date') as $text1)
        {
            $dates[] = $text1->innertext;
        }
        foreach($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page),'a.id') as $text1)
        {
            $ids[] = $text1->innertext;
        }


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


    public function getRandomQuotesWithNumber(string $number,string $count)
    {
        if(!is_numeric($number) || !is_numeric($count)) {echo 'parameters is not number'; return null;}
        if((int)$number == 0) {echo 'number of first quotes can not 0';return null;}
        if($count != null && strlen($count) > 0 && $number != null && strlen($number) > 0 && $number > 0 && $count > 0) {
            $html_parser = new HtmlParser();
            $html_download = new HtmlDownload();
            $html_page = $html_download->download(BashInfo::$BASH_URL.'random/');
            $array = new BashQuotes();
            $likes =[];
            $texts =[];
            $dates =[];
            $ids =[];

            foreach($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page),'div.text') as $text1)
            {
                $texts[] = $text1->innertext;
            }
            foreach($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page),'span.rating') as $text1)
            {
                $likes[] = $text1->innertext;
            }
            foreach($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page),'span.date') as $text1)
            {
                $dates[] = $text1->innertext;
            }
            foreach($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page),'a.id') as $text1)
            {
                $ids[] = $text1->innertext;
            }


            for ($i = $number; $i < ($number+$count); $i++)
            {
                if($i <count($texts))
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
        echo 'parameters is wrong or null';
        return null;
    }

}