<?php
/**
 * Created by PhpStorm.
 * User: dubrovin
 * Date: 12/1/17
 * Time: 9:36 PM
 */

class Story implements JsonSerializable
{
    public $id;
    public $date;
    public $tags;
    public $text;
    public $rate;
    public $head;

    /**
     * Story constructor.
     * @param $id
     * @param $date
     * @param $tags
     * @param $text
     * @param $rate
     * @param $head
     */
    public function __construct($id, $date, $tags, $text, $rate, $head)
    {
        $this->id = $id;
        $this->date = $date;
        $this->tags = $tags;
        $this->text = $text;
        $this->rate = $rate;
        $this->head = $head;
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
            'head' =>$this->head,
            'text' => $this->text,
            'rate' => $this->rate,
            'date' => $this->date,
            'tags' => $this->tags
        );
        return $result;
    }
}