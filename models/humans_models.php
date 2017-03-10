<?php

class Human extends Model
{
    public static $behaviours = [
        // 'pic' => [   //type & city - поля предназначенные для вызова через вьюшку соответствующих направлений.
            // 'key' => 'type_id', // как называется поле по которому мы связываемся в той модели.
            // 'class' => 'Type',
            // 'type' => 'one'],
         'city' => [   //type & city - поля предназначенные для вызова через вьюшку соответствующих направлений.
             'key' => 'city_id',
             'class' => 'City',
             'type' => 'one'],
        // 'link_tag' => [   //по аналогии
            // 'key' => 'id_house',
            // 'class' => 'Objects_link_tag', /// здесь нужен класс который обудет лезть в
            // 'type' => 'many',
            // 'relation_key' => 'house_id'],
        // 'tag' => [
            // 'key' => 'ALL', // для генерации исключения в where_condition
            // 'class' => 'Tag',
            // 'type' => 'many',
            // 'relation_key' => 'id_tag'],// id_tag
         'link_exp' =>[ //по аналогии
             'key' => 'id_human',
             'class' => 'Link_human_exp', /// здесь нужен модельный класс который обудет лезть
             'type' => 'many',
             'relation_key' => 'human_id'],
         'exp' => [
             'key' => 'ALL', // для генерации исключения в where_condition
             'class' => 'Exp',
             'type' => 'many',
             'relation_key' => 'id_exp']
    ];

    protected static $fields = array();
    protected static $field_types = array();

    public static function className () //найти нужный класс который обрабатывает ту или иную функцию
    {
        return 'Human';
    }

    public static function tableName ()
    {
        return 'hr_human';
    }

    public function add()
    {
        if (($this->name == '')) // не обязательно только это
        {

            return ($this->error = void);
        }

        return parent::add();
    }

    public function edit()
    {
        if (($this->name == '') || ($this->address == '') || ($this->price == '')) {

            return ($this->error = void);
        }

        parent::edit();

    }

    public function delete()
    {
        $this->error = null;
        // процедура проверки есть ли опыт у объекта и удаление их.
        $this->del_allexp();
        $this->del_allpic();

        // тут надо проверить есть ли у объекта картинки и удалить их из таблицы object_link_pics && pics && и на диске
        //if ($this->error != error) $this->del_allpic();

        if ($this->error != error)
        {
            if (parent::delete())
            {
                $this->error = success;
            }
            else
            {
                $this->error = error;
            }
        }

    }

    public function get_exp() // получаем удобный профопыт для вьюшки через драйвер БД
    {
        $load_exp = $this->link_exp; // грузим у человека связи в наличии {по названию связи behavior}
        $allexp = $this->exp;        // грузим вообще все теги подряд {по названию связи behavior }
        $exp_at_obj = array();

        if (count($load_exp) == 0) return $exp_at_obj;

        foreach ($load_exp as $key => $value)
        {
            $k = (int) $value->exp_id; // значение связи в exp из связанной таблицы.
            $p = (int) $value->id_link_human_exp; // primary key от связанной таблицы.

            array_push($exp_at_obj, ['exp_id' => $k, 'id_link_human_exp' => $p]); // массив айдишек опыта в таблице exp , увязанных с human_id
        }

        // тут работаем с сущностью `exp`
        foreach ($allexp as $key => $value)
        {
            $i=0;
            do // крутим тут exp_at_obj
            {
                if ( $exp_at_obj[$i]['exp_id'] == $value->id_exp )
                { // перечисляем все поля сущности exp
                    $exp_at_obj[$i]['org_name'] =  $value->org_name;
                    $exp_at_obj[$i]['region'] =  $value->region;
                    $exp_at_obj[$i]['prof_id'] =  $value->prof_id;
                    $exp_at_obj[$i]['hold_position'] =  $value->hold_position;
                    $exp_at_obj[$i]['start_date'] =  $value->start_date;
                    $exp_at_obj[$i]['end_date'] =  $value->end_date;
                    $exp_at_obj[$i]['just_now_flag'] =  $value->just_now_flag;
                    $exp_at_obj[$i]['job_text'] =  $value->job_text;
                }
                $i++;
            }while ($i<count($exp_at_obj));
        }

        return $exp_at_obj;
    }

    private function del_allexp() // Нужна для удаления объекта. Удаление у резюме всего опыта без спроса.
    {
        $id = static::$fields[0];
        $key = $this->data["$id"];

        $query = "SELECT * FROM `c18372_hr_helper`.`link_human_exp` LEFT JOIN `c18372_hr_helper`.`exp` ON `link_human_exp`.`exp_id` = `exp`.`id_exp` WHERE `human_id` = '$key'";

        $list = array();
        if ($result = mysqli_query(static::get_db(), $query)) //выполняем пересеченный запрос
        {
            while ($row = mysqli_fetch_assoc($result))
            {
                $list[] = $row; // list - массив записей из link_human_exp отфильтрованный по $human_id
            }
            if (count($list) == 0)
            {
                return ($this->error = success); // список записей с объектом id - пуст - ничего делать не надо.
            }
            else
            {
                foreach ($list as $key => $value) {

                    //здесь удаляется запись из таблицы objects_link_pics
                    $num = $list[$key]['id_link_human_exp'];
                    $q = "DELETE FROM `c18372_hr_helper`.`link_human_exp` WHERE `link_human_exp`.`id_link_human_exp` = '$num'";
                    $res = mysqli_query(static::get_db(), $q);
                    if (!$res)
                    {
                        echo 'Ошибка передачи данных в базу `link_human_exp`' . mysqli_error(static::get_db());
                        return ($this->error = error);
                    }


                    //здесь удаляется запись из таблицы pics
                    $n2 = $list[$key]['exp_id'];
                    $q2 = "DELETE FROM `c18372_hr_helper`.`exp` WHERE `id_exp` ='$n2'";
                    $r2 = mysqli_query(static::get_db(), $q2);
                    if (!$r2) {
                        echo 'Ошибка передачи данных в базу `exp`' . mysqli_error(static::get_db());
                        return ($this->error = error);
                    }

                }
                return ($this->error = success);
            }
        }
        else
        {
            return ($this->error = error);
        }
    }

    private function del_allpic() // Нужна для удаления объекта. Удаление у объекта всех картинок без спроса.
    {
        $out = array();
        $id = static::$fields[0];
        $key = $this->data["$id"];

        $query = "SELECT * FROM `c18372_hr_helper`.`pics` WHERE `human_id` = '$key'";
        $result = mysqli_query(self::get_db(), $query);
        if ($result)
        {
            while ($row = mysqli_fetch_assoc($result))
            {
                $out[] = $row;
            }
        }

        for ($i=0;$i<count($out);$i++)
        {
            if (!($out[$i]['picture'] === pic_default))  // удалять дефолтное изображение нельзя.
            {
                del_file($out[$i]['picture']);
            }

            $del = new Pic($out[$i]['id_pic']);
            if (!$del->is_loaded() )
            {
                return false;
            }
            $del->delete();
            unset ($del);
        }
        return true;

    }

}