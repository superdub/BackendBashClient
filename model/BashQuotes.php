<?php
/**
 * Created by PhpStorm.
 * User: dubrovin
 * Date: 11.11.17
 * Time: 11:28
 */


class BashQuotes
{
    private $array;

  public function __construct()
  {
      $this->array = [];
  }

    /**
     * add new array in basic_array
     */
    public function Add(string $id, string $text, string $like, string $date)
  {
      $array = ['id' => $id,'text' =>$text,'like' => $like,'date' => $date];
      $this->array[] = $array;
  }


    /**
     * @return string json of array with Quotes
     */
  public function Get()
  {
      return json_encode($this->array,JSON_UNESCAPED_UNICODE);
  }

}