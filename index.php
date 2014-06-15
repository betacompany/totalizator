<?

require_once 'lib/access.php';

if (accessTest()) {
    Header('Location: /main.php');
    exit(0);
}

?><!DOCTYPE html>
<html lang="ru">

<? include 'templates/head.php'; ?>

<body>

<div class="container">
    <? include 'templates/menu.php'; ?>

    <div class="span6 offset3">
        <div class="hero-unit">
            <h1>Приветствуем!</h1>

            <p>
                Используйте Ваши логин и пароль от сайта пайпа.
            </p>

            <form action="<?= MAIN_SITE_URL ?>/authorize.php" method="post" class="form-inline">
                <input type="hidden" name="method" value="sign_in"/>
                <input type="hidden" name="remember" value="yes"/>

                <div class="control-group">
                    <label class="control-label" for="input01">Логин</label>

                    <div class="controls">
                        <input id="input01" type="text" name="login"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="input02">Пароль</label>

                    <div class="controls">
                        <input id="input02" type="password" name="password"/>
                    </div>
                </div>
                <div class="control-group">
                    <button type="submit" class="btn btn-primary">Войти</button>
                    <a class="btn" href="<?= MAIN_SITE_URL ?>/sign_up?ret=http%3A%2F%2Ftotal.pipeinpipe.info">Зарегистрироваться</a>
                </div>
            </form>
        </div>
    </div>
</div>

<? require_once "templates/bottom.php"; ?>

</body>
</html>
