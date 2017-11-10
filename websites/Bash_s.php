<?php
/**
 * Created by PhpStorm.
 * User: dubrovin
 * Date: 07.11.17
 * Time: 17:17
 */
include_once 'model/HtmlParser.php';
include_once 'model/HtmlDownload.php';

class Bash_s
{

    private $countQuotes;

    function __construct($countQuotes)
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
        $_array = [];
        $array = [];
        foreach ($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page), 'div.text') as $text1) {
            $_array[] = $text1->innertext;
        }
        for ($i = 0; $i < $number; $i++)
            $_array[$i] = null;
        foreach ($_array as $text)
        {
            if($text!=null) $array[] = $text;
        }
        return $array;
    }


    /**
     * @param $linkPageFirst
     * @param $number
     * @return array of Quotes from $number to over  with creating object HtmlParser and HtmlDownload
     */
    private function getQuotesWithNumberToOverWithParserAndDownload($linkPageFirst, $number)
    {
        $html_parser = new HtmlParser();
        $html_download = new HtmlDownload();
        $html_page = $html_download -> download($linkPageFirst);
        $_array = [];
        $array = [];
        foreach ($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page), 'div.text') as $text1) {
            $_array[] = $text1->innertext;
        }
        for ($i = 0; $i <= $number; $i++)
            $array[$i] = $_array[$i];
        unset($html_page);
        unset($html_download);
        unset($html_parser);
        return $array;
    }

    /**
     * @param string $parameters
     * @return array of Quotes from Bash.im main web page with first quotes on main page
     */
    public function getQuotesWithMain(string $parameters)
    {

        if($parameters != null && strlen($parameters) > 0)
        {
            $html_download = new HtmlDownload();
            $htmlParser = new HtmlParser();
            $parameters = (int)$parameters;
            $count = $this->getCountPages($parameters,$this->countQuotes);
            $array = [];
            for($i=0;$i<$count[0]-1;$i++)
            {
                $index = $this->getMainIndexPage() - $i;
                $html_page = $html_download -> download(BashInfo::$BASH_URL.'index/'.$index);
                foreach($htmlParser->parse(iconv("windows-1251", "UTF-8", $html_page),'div.text') as $text1)
                {
                    $array[] = $text1;
                }
            }
            $index = $this->getMainIndexPage() - $count[0]+1;
            $html_page = $html_download -> download(BashInfo::$BASH_URL.'index/'.$index);
            $i = $count[1];
            foreach($htmlParser->parse(iconv("windows-1251", "UTF-8", $html_page),'div.text') as $text1)
            {
                if(!$i) break;
                $i--;
                $array[] = $text1;
            }

            unset($html_download);
            unset($htmlParser);
            return $array;
        }
        return null;
    }


    /**
     * @param string $number
     * @param string $count
     * @return array quotes from $number to $count with main page
     */
    public function getQuotesWithNumber(string $number,string $count)
    {
        if(!is_numeric($number) || !is_numeric($count)) {echo 'parameters is not number'; return null;}
        if($count != null && strlen($count) > 0 && $number != null && strlen($number) > 0) {

            $html_parser = new HtmlParser();
            $html_download = new HtmlDownload();


            $number = (int)$number;
            $count = (int)$count;


            $countPageBeforeFirst = $this->getCountPages($number,$this->countQuotes);
            $countPageAfterFirst = $this->getCountPages($number+$count,$this->countQuotes);


            $indexPageBefore = $this->getMainIndexPage() - $countPageBeforeFirst[0]+1;
            $indexPageAfter = $this->getMainIndexPage() - $countPageAfterFirst[0]+1;

            $linkPageFirst = BashInfo::$BASH_URL.'index/'.$indexPageBefore;
            $linkPageLast = BashInfo::$BASH_URL.'index/'.$indexPageAfter;


            $html_page = $html_download -> download($linkPageFirst);

            $_array = [];
            $array = [];

            if($countPageAfterFirst[1] > $countPageBeforeFirst[1] && $countPageAfterFirst[0]  == $countPageBeforeFirst[0]) {

                foreach ($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page), 'div.text') as $text1) {
                    $_array[] = $text1->innertext;
                }
                for ($i = $countPageBeforeFirst[1]-1; $i < $countPageAfterFirst[1]-1; $i++)
                    $array[$i] = $_array[$i];
                unset($html_page);
                unset($html_download);
                unset($html_parser);
                echo count($array);
             return $array;
            }

            $array = $this->getQuotesWithNumberToOver($linkPageFirst,$countPageBeforeFirst[1],$html_parser,$html_download);
            if(count($array) == $count) return $array;

            for($i=$countPageBeforeFirst[0]+1;$i<$countPageAfterFirst[0];$i++)
            {
                $index = $indexPageBefore - $i;
                $link = BashInfo::$BASH_URL.'index/'.$index;
                $local_array = $this->getQuotesWithNumberToOver($link,0,$html_parser,$html_download);
                foreach ($local_array as $text1) $array[] = $text1;
            }
            if(count($array) == $count) return $array;

            $last_array = [];
            $index = $this->getMainIndexPage()-$countPageAfterFirst[0];

            $html_page = $html_download->download(BashInfo::$BASH_URL.'index/'.BashInfo::$BASH_URL.'index/'.$index);
            foreach ($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page), 'div.text') as $text1) {
                $last_array[] = $text1->innertext;
            }
            for($i=$countPageAfterFirst[1];$i<$this->countQuotes;$i++)
              $last_array[$i] = null;

            foreach ($last_array as $item) {
                if($item!=null)
                $array[] = $item;
            }
            unset($html_page);
            unset($html_download);
            unset($html_parser);
            return $array;

        }
        echo 'parameters is wrong or null';
        return null;
    }


    /**
     * @param string $id
     * @return string quote by id but can not find quote
     */
    public function getQuotesById(string $id)
    {
        if(!is_numeric($id)) {echo 'parameters is not number'; return null;}
        if($id != null && strlen($id) > 0 && (int)$id != 0 && (int)$id < 0) {
            $html_parser = new HtmlParser();
            $html_download = new HtmlDownload();
            $text = '';
            $url = BashInfo::$BASH_URL.'quote/'.$id;
            echo $url,'   ';
            $html_page = $html_download -> download($url);
            if(strlen($html_page) <= 0) {echo 'wrong'; return null;}
            foreach ($html_parser->parse(iconv("windows-1251", "UTF-8", $html_page), 'div.text') as $text1) {
                $text .= $text1->innertext;
            }
            unset($html_page);
            unset($html_download);
            unset($html_parser);
            return $text;
        }
        echo 'parameters is null or wrong'; return null;
    }


}