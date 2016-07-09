<?

require_once dirname(__FILE__) . '/lib/access.php';
if (!accessTest()) {
    Header('Location: /index.php?code=69');
    exit(0);
}

require_once dirname(__FILE__) . '/classes/User.php';

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<? include dirname(__FILE__) . '/templates/head.php'; ?>

<body>

<div class="container">
    <? include 'templates/menu.php'; ?>
    <div class="row">
        <div class="span8 offset2 well">
            <div class="page-header">
                <h1>Правила нашего тотализатора</h1>
            </div>
            <ol>
                <li>
                    Список матчей:
                    <ul>
                        <li>
                            <b>Доступные</b> &mdash; ещё не начавшиеся матчи.
                        </li>
                        <li>
                            <b>Активные</b> &mdash; матчи, которые уже начались, но
                            данные о результате которых ещё не занесены на сайт, в
                            том числе несыгранные.
                        </li>
                        <li>
                            <b>Сыгранные</b> &mdash; матчи, результат которых занесён на сайт,
                            а рейтинговые очки за ставки пользователей посчитаны.
                        </li>
                    </ul>
                </li>
                <li>
                    Приём ставок завершается за 2 минуты до официального начала матча.
                </li>
                <li>
                    Сделанную ставку изменить или удалить нельзя.
                </li>
                <li>
                    После выполнения ставки на матч, а также после начала матча все
                    сделанные ставки доступны для просмотра пользователям.
                </li>
                <li>
                    Ставки на матчи плей-офф принимаются только на основное время (90 минут).
                </li>
                <li>
                    За угаданные результаты игроки получают рейтинговые очки по следующей схеме:
                    <ul>
                        <li>
                            <b>4 очка</b> &mdash; если угадан точный счет матча.
                        </li>
                        <li>
                            <b>2 очка</b> &mdash; если угадан исход и «близкая» разница мячей. То есть:
                            <ul>
                                <li>угадан исход матча (победа/ничья/поражение) и разница голов в матче;</li>
                                <li>количество голов каждой из команд и в ставке и результате матча отличаются на единицу в любую сторону.</li>
                            </ul>
                            <div>Пример 1: результат матча 4:2</div>
                            <div>Ставка 3:1 = 2 очка, ставка 5:3 = 2 очка, ставка 2:0 = 1 очко, ставка 6:4 = 1 очко.</div>
                        </li>
                        <li>
                            <b>2 очка</b> &mdash; если угадана «близкая» и «уверенная» победа одной из команд. То есть:
                            <ul>
                                <li>угадан победитель матча;</li>
                                <li>и в ставке и по результатам матча одна из команд одержала победу с преимуществом в 2 и более мячей;</li>
                                <li>количество голов одной из команд угадано точно, а другой – отличается на единицу в любую сторону.</li>
                            </ul>
                            <div>Пример 1: результат матча 3:1.</div>
                            <div>Ставка 4:1 = 2 очка, ставка 3:2 = 1 очко, ставка 5:1 = 1 очко.</div>
                            <div>Пример 2: результат матча 3:0.</div>
                            <div>Ставка 2:0 = 2 очка, ставка 3:1 = 2 очка, ставка 4:0 = 2 очка, ставка 3:2 = 1 очко.</div>
                        </li>
                        <li>
                            <b>3 очка</b> &mdash; если  угадана «близкая» и «разгромная» победа одной из команд. То есть:
                            <ul>
                                <li>угадан победитель матча;</li>
                                <li>и в ставке и по результатам матча одна из команд одержала победу с преимуществом в 3 и более мячей;</li>
                                <li>количество голов победившей команды и в ставке и по результатам матча больше или равно 4;</li>
                                <li>количество голов одной из команд угадано точно, а другой – отличается на единицу в любую сторону.</li>
                            </ul>
                            <div>Пример: результат матча 4:1.</div>
                            <div>Ставка 4:0 = 3 очка, ставка 5:1 = 3 очка, ставка 3:0 = 2 очка, ставка 5:0 = 1 очко.</div>
                        </li>
                        <li>
                            <b>1 очко</b> &mdash; во всех остальных случаях при угаданном исходе матча.
                        </li>
                    </ul>
                    <div>
                        Для удобства ниже представлена таблица некоторых счетов, 
                        где на пересечении ставки и результата матча стоит количество очков.
                    </div>
                    <img src="img/stakes.png" alt="Таблица очков за ставки"/>
                </li>
                <li>
                    При равенстве очков игроки сортируются по наибольшему количеству угаданных матчей в наиболее высокой очковых категорий.
                </li>
            </ol>
        </div>
    </div>
</div>

<? include dirname(__FILE__) . "/templates/bottom.php"; ?>

</body>
</html>
