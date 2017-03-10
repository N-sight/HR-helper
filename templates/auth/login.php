<?php

?>
<div class="row">
    <div class="col-lg-12 col-md-12">
        <h3 class="page-header">Авторизация</h3>
        <form action="" method="post">
            <input type="hidden" name="__action" value="login"/>
            <div class="form-group input-group">
                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                <input type="text" placeholder="Имя пользователя" class="form-control" name="username">
            </div>
            <div class="form-group input-group">
                <span class="input-group-addon"><i class="fa fa-key"></i></span>
                <input type="password" placeholder="Пароль" class="form-control" name="password">
            </div>


            <button class="btn btn-success" type="submit">Войти</button>
        </form>

    </div>
    <!-- /.col-lg-12 -->

</div>
<!-- /.row -->
