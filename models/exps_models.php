<?php

Class Exp extends Model // бегаем по сущности Pics
{
    protected static $fields = array();
    protected static $field_types = array();

    public static function className()
    {
        return 'Exp';
    }

    public static function tableName()
    {
        return 'exp';
    }

    public static function addexp ($ex) //безадресно внешне добавляет новую картинку с названием pic в сущность pic
    {// используется в object controller в методе add

        foreach ($ex as $k => $v)
        {
            $exp[$k] = mysqli_real_escape_string(static::get_db(), $v);
        }

        $query = "INSERT INTO `exp` ( `org_name`,`region`, `prof_id`, `hold_position`, `start_date`, `end_date` , `just_now_flag`, `job_text`  ) VALUES ( '{$exp['org_name']}',{$exp['region']},{$exp['prof_id']},'{$exp['hold_position']}','{$exp['start_date']}','{$exp['end_date']}',{$exp['just_now_flag']},'{$exp['job_text']}')";

        $result = mysqli_query(static::get_db(), $query);
        if (!$result) {
            echo 'Ошибка передачи данных в базу ';
            echo mysqli_error(static::get_db());
            return false;
        }
        return true;
    }

    public static function get_last_id_exp() // возвращает последнее установленное значение AutoIncrement для таблицы pics
    {
        $query = "SELECT * FROM `information_schema`.`TABLES` WHERE `TABLE_NAME` = 'exp'";
        $out = array();

        if ($result = mysqli_query(static::get_db(), $query)) {
            while ($row = mysqli_fetch_assoc($result)) {
                $out[] = $row;
            }
            $res = $out[0]['AUTO_INCREMENT'] - 1;
            return (int)$res;
        } else return error;
    }

    public function edit()
    {
        return parent::edit();
    }

}