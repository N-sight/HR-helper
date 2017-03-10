<?php

Class Link_human_exp extends Model
{
    protected static $fields = array();
    protected static $field_types = array();

    public static function className()
    {
        return 'Link_human_exp';
    }

    public static function tableName()
    {
        return 'link_human_exp';
    }

    public static function add_link($id_human, $id_exp) // добавляет добавляет запись о связи опыта работы с резюме в сущность link_human_exp
    {// используется в human controller в методе add

        $query = "INSERT INTO `".self::tableName()."` (`human_id`,`exp_id`) VALUES ($id_human,$id_exp)";

        $res = mysqli_query(static::get_db(), $query);
        if (!$res)
        {
            echo 'Ошибка передачи данных в базу Тут ошибка' . mysqli_error(static::get_db());
            echo $id_human. " " . $id_exp . "<br>";
            return false;
        }
        return true;
    }
}