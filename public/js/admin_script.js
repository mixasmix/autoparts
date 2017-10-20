

$(document).ready(function () {

    $("select[name=status]").change(function () {
        if (confirm('Изменить статус заказа?')) {
            var select = $(this);
            var status_id = $(this).val();
            var order_id = $(this).attr('data-order_id');
            $.ajax({
                url: "/panelcontrol/panelcontrol/setParam/setOrderStatus",
                type: "POST",
                data: {id_status: status_id, id_order: order_id},
                success: function (r) {
                    if (r) {
                        select.parent().addClass('has-success');
                    } else {
                        select.parent().addClass('has-error');
                    }
                }
            });
        }
    });

    //Выбор всех чекбоксов

    // Выбор всех
    //При клике на ссылку "Все", активируем checkbox
    $("input[name=checkedAll]").click(function () {
        var parentCheckbox = $($(this).parent().parent().parent().find('input.order_list_checkbox'));
        if ($(this).prop('checked')) {
            parentCheckbox.prop('checked', true);
        } else {
            parentCheckbox.prop('checked', false);
        }


    });

    //При клике на кнопку удалить выбранные позиции
    $('.deleted_checked_position').click(function () {
        var checked_position = $($(this).parent().find('.order_list_checkbox:checked'));
        //массив значений выбранных элементов
        var array_values_selected_checbox = checked_position.map(function () {
            return this.value;
        }).get();
        //выводим предупреждение
        if (confirm('Удалить выбранные позиции(' + checked_position.length + ')?')) {
            //если ответ положительный отправляем запрос на сервер
            $.ajax({
                url: "/panelcontrol/panelcontrol/setParam/deleteThisPosition",
                type: "POST",
                data: {id_positions: array_values_selected_checbox},
                success: function (r) {
                    if (r) {
                        //если все успешно, то удаляем элементы из таблицы
                        array_values_selected_checbox.forEach(function (item, i, arr) {
                            $('tr.row_id_' + item).remove();
                        });
                    }
                }
            });

        }

    });

    $("select[name=order_item_status]").change(function () {
        if (confirm('Изменить статус позиции?')) {
            var select = $(this);
            var status_id = $(this).val();
            var pos_id = $(this).attr('data-position_id');
            $.ajax({
                url: "/panelcontrol/panelcontrol/setParam/setPositionStatus",
                type: "POST",
                data: {id_status: status_id, id_position: pos_id},
                success: function (r) {
                    if (r) {
                        select.parent().addClass('has-success');
                    } else {
                        select.parent().addClass('has-error');
                    }
                }
            });
        }
    });

    //редактируемые поля позиции
    $('.edited_information').dblclick(function () {
        var cont = $(this).text();//текущее содержание элемента
        var position_id = $(this).attr('data-position_id');
        var field_name = $(this).attr('data-name_field')//имя поля
        var field_type = $(this).attr('data-field_type');
        var field_length = ($(this).attr('data-content_lenght') > 20) ? 20 : $(this).attr('data-content_lenght');
        $(this).text('').html('<input type="' + field_type + '" size="' + field_length + '" name="' + field_name + '" value="' + cont + '" class="edited_inf_input"/>');
        $(this).mouseleave(function () {
            var new_cont = $(this).find('input').val();
            $(this).html(new_cont);
            if (confirm('Изменить значение?')) {

            } else {
                $(this).html(cont);
            }
        });
    });

    //выбор артикула при вводе нового
    $('.new_art').on('input', function () { //назначаем на поле артикула событие ввода
        var element = $(this);
            element.popover('destroy');
        //отправляем запрос на получение данных с сервера
        if (element.val().length >= 3) {
            
            $.ajax({
                url: "/panelcontrol/panelcontrol/ajaxActions/getFindedArtikuls",
                type: "POST",
                data: {art: $(this).val()},
                success: function (r) {
                    var result = $.parseJSON(r);
                    if (result) {
                        var links = '';
                        $(result).each(function () {
                            links += "<a class='modal_new_position__link_finding_position' data-artikul='" + this.artikul + "' data-brand='" + this.name + "' data-desc='" + this.description + "' href='#'>" + this.artikul + ' ' + this.name + ' ' + this.description + "</a><hr>";
                        });
                        console.log(links);
                        element.popover({
                            html: true,
                            content: links
                        });
                        element.popover('show');
                        $('.modal_new_position__link_finding_position').on('click', function () {
                            console.log('click');
                            $('input[name=new_art]').val($(this).attr('data-artikul'));
                            $('input[name=new_brand]').val($(this).attr('data-brand'));
                            $('input[name=new_desc]').val($(this).attr('data-desc'));
                            $('input').popover('hide');
                        });
                    }
                }
            });
            //выводим в поповер ответ сервера
            //при клике на один из результатов подставляем все в соответствующие поля
        }

    });

    $('.modal_new_position__link_finding_position').on('click', function () {
        conslole.log('click');
        $('input[name=new_art]').val($(this).attr('data-artikul'));
        $('input[name=new_brand]').val($(this).attr('data-brand'));
        $('input[name=new_desc]').val($(this).attr('data-desc'));
        $('input').popover('hide');
    });
});
