<?php

Class Pic extends Model // бегаем по сущности Pics
{
    protected static $fields = array();
    protected static $field_types = array();

    public static function className()
    {
        return 'Pic';
    }

    public static function tableName()
    {
        return 'pics';
    }

    public static function addpic ($pic,$id_human) //безадресно внешне добавляет новую картинку с названием pic в сущность pic
    {// используется в object controller в методе add

        $pic = mysqli_real_escape_string(static::get_db(), $pic);
        $id_human = mysqli_real_escape_string(static::get_db(), $id_human);
        $query = "INSERT INTO `pics`( `picture`, `human_id` ) VALUES ( '$pic',$id_human )";
        $result = mysqli_query(static::get_db(), $query);
        if (!$result) {
            echo 'Ошибка передачи данных в базу ';
            echo mysqli_error(static::get_db());
        }
        return true;
    }

    public static function get_pics ($id_human)
    {
        $out = array();
        $id_human = mysqli_real_escape_string(static::get_db(), $id_human);
        $query = "SELECT * FROM `".static::tableName()."` WHERE `human_id` = '$id_human'";
        $result = mysqli_query(static::get_db(), $query);
        if (!$result)
        {
            return mysqli_error(static::get_db());
        }
        while ($row = mysqli_fetch_assoc($result))
        {
            $out[] = $row;
        }
        return $out;
    }
    
    public static function isPics_double($picname_arr,$id) // возвращает false, если в массиве $picname нет аналогичнычных названий картинок для уже существующих записей с этим  $id_obj
    {// используется в контроллере объектов в методе edit() для добавления картинок к объекту

        $double_flag = 0; // копий названий нет
        foreach ($picname_arr as $key => $value) // 
        {
            $filename = $id."_{$value}";
            $query = "SELECT count(*) FROM `pics` WHERE `picture` = '$filename'";

            if ($result = mysqli_query(static::get_db(), $query)) {
                while ($row = mysqli_fetch_all($result))
                {
                    if ($row[0][0] != 0)
                    {
                        $double_flag = 1;
                    }
                }
            }
            else
            {
                echo 'Ошибка передачи данных в базу ' . mysqli_error(static::get_db());
                die();
            }
        }

        if ($double_flag == 0)
        {
            return false; // одинаковых файлов нет - это нормальный ход.
        }

        return true;
    }

    public static function del_by_id ($id) // метод удаления из таблицы по id_pic
    {
        $picture = new Pic($id);

        if (!$picture->is_loaded() )
        {
            return false;
        }
        $picture->delete();

        return true;
    }
}
