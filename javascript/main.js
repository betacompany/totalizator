var speed = "fast";

function make(o) {
    var score1 = $(o).parent().children('input[name=score1]').val();
    var score2 = $(o).parent().children('input[name=score2]').val();
    var id = $(o).parent().children('input[name=match_id]').val();
    $.ajax({
        url:'/processor.php',
        data:{
            action:'make_stake',
            match_id:id,
            score1:score1,
            score2:score2
        },
        success:function (data) {
            $(o).parent().parent().slideUp(speed);
            var divMenu = $(o).parent().parent().parent();
            divMenu.html(data);
            divMenu.parent().removeAttr("data-without-my").attr("data-my", "true");
        },
        error:function () {
            alert('Oooops');
        }
    });
}

function makeStake(o) {
    $(o).slideUp(speed);
    $(o).parent().children('div.stake').slideDown(speed);
}

function showStakes(match_id, o) {
    var response = $(o).parentsUntil('#matches', 'li').children('div.response');
    if (response.text().length > 0) {
        return;
    }

    $(o).fadeOut(speed);
    $.ajax({
        url:'/processor.php',
        data:{
            action:'get_stakes',
            match_id:match_id
        },
        success:function (data) {
            response.append(data);
            response.slideDown(speed);
        }
    });
}

function hideStakes(o) {
    $(o).parent().slideUp(speed, function () {
        $(this).children('ul').remove();
        $(this).parent().children('u').slideDown(speed);
        $(o).remove();
    });
}

var selected = 'available';
function loadMatches(type) {
    $('#show_all').show();
    $.ajax({
        url:'/processor.php',
        data:{
            action:'get_matches',
            type:type
        },
        beforeSend:function () {
            $('#menu_' + selected).removeClass();
            $('#menu_' + type).addClass('active');
            selected = type;
        },
        success:function (data) {
            $('#matches')
                .slideUp(speed, function () {
                    $(this)
                        .html(data)
                        .slideDown(speed, function () {
                            $('.top_m .btn.active').click();
                        });
                })
        }
    });
}

function refreshMatches() {
    loadMatches(selected);
}

function userClick(o, uid, comp_id) {
    if ($(o).data('opened') == undefined) {
        loadStakesForUser(uid, comp_id);
        $(o).data('opened', true);
    } else {
        if ($(o).data('opened')) {
            closeStakesForUser(uid);
            $(o).data('opened', false);
        } else {
            loadStakesForUser(uid, comp_id);
            $(o).data('opened', true);
        }
    }
}

function closeStakesForUser(uid) {
    $('#stakes_user_' + uid).slideUp(speed);
}

function loadStakesForUser(uid, comp_id) {
    $.ajax({
        url:'/processor.php',
        data:{
            action:'load_stakes_for_uid',
            quid:uid,
            comp_id:comp_id
        },
        success:function (data) {
            $('#stakes_user_' + uid)
                .slideUp(speed, function () {
                    $(this)
                        .html(data)
                        .slideDown(speed);
                });
        }
    });
}

var selected_full = 0;
function loadStakesForUserFull(uid, comp_id) {
    $.ajax({
        url:'/processor.php',
        data:{
            action:'load_stakes_for_uid',
            full:true,
            quid:uid,
            comp_id:comp_id
        },
        success:function (data) {
            $('#menu_' + selected_full).removeClass();
            $('#menu_' + comp_id).addClass('active');
            selected_full = comp_id;
            $('#matches > ul')
                .slideUp(speed, function () {
                    $(this)
                        .html(data)
                        .slideDown(speed);
                });
        }
    });
}

var selected_rating = 0;
function loadRating(comp_id) {
    $.ajax({
        url:'/processor.php',
        data:{
            action:'load_rating',
            comp_id:comp_id
        },
        beforeSend:function () {
            $('#menu_' + selected_rating).removeClass();
            $('#menu_' + comp_id).addClass('active');
            selected_rating = comp_id;
        },
        success:function (data) {
            $('#people')
                .slideUp(speed, function () {
                    $(this)
                        .html(data)
                        .slideDown(speed);
                })
        },
        error:function () {
            alert('Oooops!');
        }
    });
}

function openAllMatches(o) {
    $('.stakes-btn').click();
    $(o).fadeOut(speed);
}

function filter(o, items, filter) {
    $(o).parent().find('.btn').removeClass('active');
    $(o).addClass('active');
    $('#show_all').show();
    $(items).each(function () {
        if (!filter) {
            $(this).slideDown(speed);
        } else {
            if (this.hasAttribute(filter)) {
                $(this).slideDown(speed);
            } else {
                $(this).slideUp(speed);
            }
        }
    })
}
