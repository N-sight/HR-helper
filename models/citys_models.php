<?php

class City // модель без БД ни от чего не наследуется. В полной иерархии, будет базовый класс модели, от него будет наследоваться модель с БД, от нее - все модели с БД. От базового класса наследуются все такие вот модели без MySQL
{
    private $id;
    public $title;

    public function __get($field)
    {
        if ($field === 'id') return $this->id;
        return Model::FIELD_NOT_EXIST;
    }

    public function __set($field, $value)
    {
        return Model::FIELD_NOT_EXIST;
    }

    public function __construct($id = NULL)
    {
        if ($id !== NULL)
        {
            $this->one($id);
        }
    }

    public function load($data = array())
    {
        $this->id = $data['id'];
        $this->title = $data['title'];
    }

    public static function all()
    {
        $file = fopen($_SERVER['DOCUMENT_ROOT'] . "/" . city_default, 'r');

        $i = 0;

        $all = [];

        while ($row = fgets($file))
        {
            $i++;

            $result = [
                'id' => $i,
                'title' => iconv("windows-1251", "utf-8", trim($row))
            ];

            $city = new City();
            $city->load($result);

            $all[] = $city;
        }
        fclose($file);
        return $all;
    }


    public function one($id)  // бывшая get_oneCity
    {

        $file = fopen($_SERVER['DOCUMENT_ROOT'] . "/" . "cities.txt", 'r');

        $i = 0;

        while ($row = fgets($file)) {
            $i++;

            $result = [
                'id' => $i,
                'title' => iconv("windows-1251", "utf-8", trim($row))
            ];
            if ((int)$i === (int)$id)
            {
                $this->load($result);
                return true;
            }
        }
        return false;
    }
}


