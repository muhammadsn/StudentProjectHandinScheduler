$(function(){

    let isValid = false;
    var sched_i = $(".table_row").length+1;
    $("#project_password").hide();

    function validate(input, show) {
        let errors = 0;
        let message = "";
        if (input.val().length === 0){
            message = "لطفاً این فیلد را پر کنید";
            errors = errors + 1;
        }
        if (input.attr('type') === 'number' || input.attr('type') === 'tel') {
            let max = input.attr('maxlength');
            let min = input.attr('minlength');
            if (errors === 0) {
                if (input.val().length > max) {
                    message = "بیشتر از " + max + " کاراکتر وارد نکنید";
                    errors = errors + 1;
                }
                else if (input.val().length < min) {
                    message = "کمتر از " + min + " کاراکتر وارد نکنید";
                    errors = errors + 1;
                }
            }
        }
        if (errors !== 0 && show) {
            let current_message = input.parent().find('span');
            current_message.remove();

            input.parent().append("<span class='form_error_message'>"+ message +"</span>");
        }
        else if (!show) {
            let current_message = input.parent().find('span');
            current_message.remove();
        }
        // if (errors !== 0) {
        //     let current_message = input.parent().find('span');
        //     current_message.remove();
        //
        //     input.parent().append("<span class='form_error_message'>"+ message +"</span>");
        // }

        return errors;
    }

    $("#searchbtn").on('click', function () {
        $("#searchform").submit();
    });

    $('#submit').on('click', function () {
        $('.input').filter('[required]').each(function () {
            validate($(this), true);
        });
        if (isValid) {
            // show_message('success');
            $("#login_form").submit();
        }
    });

    $('#projectsubmit').on('click', function () {
        $("#project_form").submit();
    });

    $('.input').on('keyup', function () {
        let e = 0;
        $('.input').filter('[required]').each(function () {
            e += validate($(this), false);
        });
        let err = 0;
        if ($(this).prop('required')) {
            err = validate($(this), true);
        }
        if (!err){
            e -= 0;
        }

        if (e === 0){
            $('#submit').removeClass('disabled').addClass('active');
            isValid = true;
        }
        else {
            $('#submit').removeClass('active').addClass('disabled');
        }
    });

    $('#add_time').on('click', function () {
        $("#default_row").remove();
        let sbmt_date = $("#date").val();
        let sbmt_st = $("#start_time").val();
        let sbmt_et = $("#end_time").val();
        let x = "<tr class='table_row del_"+sched_i+"'>" +
            "<td><input class='table_input' type='text' name='sched_"+sched_i+"[]' value='" + sbmt_date +"' readonly></td>" +
            "<td><input class='table_input' type='text' name='sched_"+sched_i+"[]' value='" + sbmt_st +"' readonly></td>" +
            "<td><input class='table_input' type='text' name='sched_"+sched_i+"[]' value='" + sbmt_et +"' readonly></td>" +
            "<td><a href='#' id='del_"+sched_i+"' class='table_tool delete' onclick=\"delete_row('del_"+sched_i+"')\" title='حذف این زمان'><i class='zmdi zmdi-close'></i></a></td></tr>";
        $("#time_table").append(x);
        sched_i++;
    });

    $("#checkbox").change(function() {
        if(this.checked) {
            $("#project_password").fadeIn(500);
        }
        else {
            $("#project_password").fadeOut(500);
        }
    });

    function show_message(status) {
        let sbmt = $('#submit');
        sbmt.removeClass('active').addClass('disabled').css('cursor', 'default');
        sbmt.find('span').fadeOut(500);
        setTimeout(function () {
            $('.box_content').slideUp(700);
            sbmt.css({'height': 100, 'bottom':-48, 'background-position-y':327});
        },100);

        setTimeout(function () {
            let icon = $('.box_title_icon');
            icon.find('i').fadeOut(500);
            setTimeout(function () {
                icon.css('background-color', '#ffffff').addClass('loading');
                icon.find('i').removeClass('zmdi-file-text').css('color', '#000000').fadeIn(500);
                $('.form_title').text('در حال ارسال اطلاعات');
            }, 500);
        }, 500);

        setTimeout(function () {
            if (status === 'success') {
                setTimeout(function () {
                    let icon = $('.box_title_icon');
                    icon.find('i').fadeOut(500);
                    setTimeout(function () {
                        icon.css('background-color', '#00a693').removeClass('loading');
                        icon.find('i').addClass('zmdi-check').css('color', '#ffffff').fadeIn(500);
                        $('.form_title').text('اطلاعات با موفقیت ثبت شد');
                    }, 500);
                }, 500);
            }
            else {
                setTimeout(function () {
                    let icon = $('.box_title_icon');
                    icon.find('i').fadeOut(500);
                    setTimeout(function () {
                        icon.css('background-color', '#a62000').removeClass('loading');
                        icon.find('i').addClass('zmdi-close').css('color', '#ffffff').fadeIn(500);
                        $('.form_title').text('عملیات با خطا مواجه شد');
                    }, 500);
                }, 500);
            }
        },2000);
    }

});

function delete_row(x) {
    let id = "."+x;
    $(id).remove();
    if ($("#time_table").children().length===1 && $(".table_row").length===0) {
        $("#time_table").append(" <tr id='default_row' class='table_row'><td colspan='4'>هیچ زمانی اضافه نشده است</td></tr>");
    }
}