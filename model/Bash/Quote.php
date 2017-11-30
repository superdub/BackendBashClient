<?php
/**
 * Created by PhpStorm.
 * User: dubrovin
 * Date: 11/26/17
 * Time: 3:11 PM
 */

class Quote implements JsonSerializable
{
   public $id;
   public $text;
   public $rate;
   public $date;

   public function __construct($id,$text,$rate,$date)
   {
       $this->id = $id;
       $this->text = $text;
       $this->rate = $rate;
       $this->date = $date;
   }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        $result = array(
            'id' => $this->id,
            'text' => $this->text,
            'rate' => $this->rate,
            'date' => $this->date
        );
        return $result;
    }
}