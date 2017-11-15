<?php
/**
 * Created by PhpStorm.
 * User: dubrovin
 * Date: 07.11.17
 * Time: 17:17
 */


class Bash_s
{

    private $countQuotes;

    public function __construct()
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
     * @param $ost
     * @param $del
     * @return array of count page and quotes on last page
     */
    private function getCountPages(int $ost, int $del)
    {
        return [floor($ost/$del) + 1,$ost%$del];
    }


    /**
     * this function parse page for BashQuotes
     */
    private function parseForBashQuotes(&$html_page, &$dates, &$texts, &$likes, &$ids)
    {
        if($html_page != null)
        {
            try {
                echo strlen($html_page),'      ';
                foreach (HtmlParser::parse(iconv("windows-1251", "UTF-8", $html_page), 'div.text') as $text1) {
                    $texts[] = $text1->innertext;
                }
                foreach (HtmlParser::parse(iconv("windows-1251", "UTF-8", $html_page), 'span.rating') as $text1) {
                    $likes[] = $text1->innertext;
                }
                foreach (HtmlParser::parse(iconv("windows-1251", "UTF-8", $html_page), 'span.date') as $text1) {
                    $dates[] = $text1->innertext;
                }
                foreach (HtmlParser::parse(iconv("windows-1251", "UTF-8", $html_page), 'a.id') as $text1) {
                    $ids[] = $text1->innertext;
                }
            }
            catch (Exception $exception){echo $exception;return null;}
        }
    }


    /**
     * @return int index Bash.im main page
     */
    private function getMainIndexPage()
    {
        $html_page = HtmlDownload::download(BashInfo::$BASH_URL);
        try
        {
        foreach(HtmlParser::parse(iconv("windows-1251", "UTF-8", $html_page),'div.pager') as $text1) {
            foreach (HtmlParser::parse(iconv("windows-1251", "UTF-8", $text1), 'form') as $text2) {
                $mas = HtmlParser::parse(iconv("windows-1251", "UTF-8", $text2), '.page');
                $str = (int)strripos($mas[0], 'value');
                $str = substr($mas[0], $str, $str + 5);
                $str = str_replace('value="', '', $str);
                $str = str_replace('" />', '', $str);
                return (int)$str;
               }
            }
        }
        catch (Exception $exception){echo $exception;return null;}
        return null;
    }


    /**
     *  add Quotes in $arrays
     */
    private function getQuotesWithNumberToCount(&$html_page, $number, $count,&$texts,&$likes,&$dates,&$ids)
    {

        $_likes =[];
        $_texts =[];
        $_dates =[];
        $_ids =[];

        $this->parseForBashQuotes($html_page,$_dates,$_texts,$_likes,$_ids);


        for ($i = $number; $i <= $count; $i++)
        {
           if($i>=0 && $i < count($_texts))
           {
               $likes[] = $_likes[$i];
               $texts[] = $_texts[$i];
               $dates[] = $_dates[$i];
               $ids[] = $_ids[$i];
           }
        }
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
            $array = new BashQuotes();

            $parameters = (int)$parameters;

            $count = $this->getCountPages($parameters,$this->countQuotes);


            $likes =[];
            $texts =[];
            $dates =[];
            $ids =[];

            $MainIndex = $this->getMainIndexPage();

           for($i=0,$length=$count[0]-1;$i<$length;$i++)
            {
                $index = $MainIndex - $i;
                $html_page = HtmlDownload::download(BashInfo::$BASH_URL.'index/'.$index);
                $this->parseForBashQuotes($html_page,$dates,$texts,$likes,$ids);
                unset($html_page);
            }


            $index = $MainIndex -$count[0] +1;
            $html_page = HtmlDownload::download(BashInfo::$BASH_URL.'index/'.$index);


            $this->getQuotesWithNumberToCount($html_page,0,$count[1]-1,$texts,$likes,$dates,$ids);

            unset($html_page);

            for ($i=0;$i<$parameters;$i++)
            {
                $array->Add($ids[$i],$texts[$i],$likes[$i],$dates[$i]);
            }

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

            $number = (int)$number;
            $count = (int)$count;

            $array = new BashQuotes();

            $likes =[]; $_likes =[];
            $texts =[];  $_texts =[];
            $dates =[];  $_dates =[];
            $ids =[];  $_ids =[];


            $countPageBeforeFirst = $this->getCountPages($number,$this->countQuotes);
            $countPageAfterFirst = $this->getCountPages($number+$count,$this->countQuotes);


            $indexPageBefore = $this->getMainIndexPage() - $countPageBeforeFirst[0] + 1;


            $linkPageFirst = BashInfo::$BASH_URL.'index/'.$indexPageBefore;


            $html_page_first = HtmlDownload::download($linkPageFirst);



           if($countPageAfterFirst[1] > $countPageBeforeFirst[1] && $countPageAfterFirst[0]  == $countPageBeforeFirst[0]) {

               $this->parseForBashQuotes($html_page_first,$dates,$texts,$likes,$ids);

               for ($i = $countPageBeforeFirst[1]-1; $i < $countPageAfterFirst[1]-1; $i++)
               {
                   $array->Add($ids[$i],$texts[$i],$likes[$i],$dates[$i]);
               }

               unset($html_page_first);
               unset($likes);
               unset($texts);
               unset($dates);
               unset($ids);
               return $array->Get();
            }


             $this->getQuotesWithNumberToCount($html_page_first,$countPageBeforeFirst[1],$this->countQuotes-1,$texts,$likes,$dates,$ids);

             unset($html_page_first);

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
                $this->getQuotesWithNumberToCount($link,0,$this->countQuotes-1,$texts,$likes,$dates,$ids);
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



            $html_page_first = HtmlDownload::download(BashInfo::$BASH_URL.'index/'.$index);


            $this->getQuotesWithNumberToCount($html_page_first,0,$countPageAfterFirst[1]-1,$texts,$likes,$dates,$ids);


            for($i=0;$i<$count;$i++)
            {
                $array->Add($ids[$i],$texts[$i],$likes[$i],$dates[$i]);
            }

            unset($html_page_first);
            unset($texts); unset($_texts);
            unset($likes);  unset($_likes);
            unset($dates); unset($_dates);
            unset($ids); unset($_ids);
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