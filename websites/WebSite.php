<?php


abstract class WebSite
{
    /**
     * count elements of one page
     * @var int
     */
     protected $countElements;


    /**
     * @return int main index
     */
    abstract public function getMainIndex();

    /**
     * @param $ost
     * @param $del
     * @return array : count page and elements on last page
     */
    public function getCountPages($ost,$del)
    {
        return [floor($ost/$del) + 1,$ost%$del];
    }

    /**
     * return json array of elements starting with the main page
     * @param string $count
     * @return string json array
     */
    abstract public function getElementsWithMainPage(string $count);

    /**
     * return json array of elements starting with $number
     * @param string $number
     * @param string $count
     * @return string json array
     */
    abstract public function getElementsWithNumber(string $number, string $count);

    /**
     * return json array of elements on random page
     * @return string json array
     */
    abstract public function getRandomElements();

    /**
     * return json object by id
     * @param string $id
     * @return string json object
     */
    abstract public function getElementById(string $id);

    /**
     * return json array of elements starting with rating page
     * @param string $count
     * @return string json array
     */
    abstract public function getRatingElementsWithMainPage(string $count);

    /**
     * return json array of elements starting with $number
     * @param string $number
     * @param string $count
     * @return string json array
     */
    abstract public function getRatingElementsWithNumber(string $number, string $count);


}