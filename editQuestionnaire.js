$(function() {
    require(['ui', 'editContent', 'message', 'editProperty', 'itemsList', 'picUploader', 'editor', 'banners'], function(ui, editContent, message, editProperty) {
        var questionList = $('#questions .quest-list');
        var moduleUrl = 'questionnaires';


        var editRules = function () {
            var form = $(this);

            $(".edit-quest .add-btn").click(function () {
                var newAnswer = $('.new-answer [name="new-answer"]').val(),
                lastNum = $('.new-answer').prev('.white-block-row').data('ans'),
                    to = '';
debugger;
                if(newAnswer) {
                    //добавляем
                    if(lastNum){
                        lastNum +=1;
                        var text = '<div class="white-block-row" data-ans="'+lastNum+'">\n' +
                            '                                <div class="w3">\n' +
                            '                                    <span>Ответ '+lastNum+'</span>\n' +
                            '                                </div>\n' +
                            '                                <div class="w7">\n' +
                            '                                    <input type="text"\n' +
                            '                                           name="answer['+lastNum+']"\n' +
                            '                                           value="'+newAnswer+'">\n' +
                            '                                </div>\n' +
                            '                                <div class="w2">\n' +
                            '                                    <div class="delete-object action-button w05">\n' +
                            '                                        <i class="icon-prop-delete"></i>\n' +
                            '                                    </div>';
                        to = $('.new-answer').prev('.white-block-row');
                        to.after(text);
                    }else{
                        lastNum = 1;
                        var text = '<div class="white-block-row" data-ans="'+lastNum+'">\n' +
                            '                                <div class="w3">\n' +
                            '                                    <span>Ответ '+lastNum+'</span>\n' +
                            '                                </div>\n' +
                            '                                <div class="w7">\n' +
                            '                                    <input type="text"\n' +
                            '                                           name="answer['+lastNum+']"\n' +
                            '                                           value="'+newAnswer+'">\n' +
                            '                                </div>\n' +
                            '                                <div class="w2">\n' +
                            '                                    <div class="delete-object action-button w05">\n' +
                            '                                        <i class="icon-prop-delete"></i>\n' +
                            '                                    </div>';
                        //to = $('.white-inner-cont.answers');
                        $('.new-answer').before(text);
                    }

                    //Очищаем новый
                    $('.new-answer [name="new-answer"]').val('');

                }
            });

            $(".edit-quest").on('click','.delete-object',function () {
                $(this).closest('.white-block-row').remove();
                debugger;
                var start = 1;
                $('.answers').children().each(function (e) {
                    debugger;
                    if($(this).hasClass('new-answer')) {
                        debugger;
                    }else {
                        debugger;
                        $(this).data('ans', start);
                        $(this).children('.w3').children('span').text('Ответ ' + start);
                        $(this).children('.w7').children('input').attr('name', 'answer[' + start + ']');
                        start += 1;
                    }
                    }
                )
            });

        };


        $('.page-main').on('click','.icon-save',function () {
            debugger;
            var form = $(".edit-quest");
            var answer_err = 0;

            if( $('[name = "question"]',form).val() ===""){
                message.errors({
                    errors: 'Заполните поле Вопрос'
                });
                return false;
            }

            if( $('[name = "check"]',form).val() ===""){
                message.errors({
                    errors: 'Заполните поле Правильные ответы'
                });
                return false;
            }

            $('.answers input:not([name = "new-answer"])',form).each(function () {
                if(( $('.answers input:not([name = "new-answer"])',form).length<2)||($(this).val()==='')){
                    answer_err+=1;
                }
            });

            if( answer_err>0){
                message.errors({
                    errors: 'Варианты ответа не заданы или их количество меньше двух'
                });
                return false;
            }

        });

        // задать правило
            $('.edit-questions-form').on('click','#questions .actions-panel .action-add',function () {
            debugger;
            editContent.open({
                getform: '/' + moduleUrl + '/editPopup/',
                getformtype: 'json',
                loadform: function () {
                    editRules.call(this);
                },
                customform: true
            });
        });

       // редактировать правило
        $('.edit-questions-form').on('click','#questions .quest-list .action-edit', function () {
            var id = $(this).closest('.wblock').data('quest_id');
            editContent.open({
                getform: '/' + moduleUrl + '/editPopup/',
                getformtype: 'json',
                getformdata: {
                    rule_id: id
                },
                loadform: function () {
                    editRules.call(this);
                },
                customform: true
            });
            return false;
        });


        // удалить правило
        $('.edit-questions-form').on('click','#questions .quest-list .action-delete', function () {
            var id = $(this).closest('.wblock').data('quest_id');
            message.confirm({
                text: 'Подтвердите удаление правила.',
                type: 'delete',
                ok: function () {
                    $.post('/' + moduleUrl + '/deleteQuestion/', {
                        id: [id]
                    }, function (res) {
                        if (res.content) {
                            $('.edit-questions-form').html(res.content);
                            $(window).resize();
                            ui.initAll();
                        }
                    }, 'json').error(function (err) {
                        message.errors({
                            text: 'Ошибка сервера: ' + err.status,
                            descr: (err.status === 404 || err.status === 200) ? '' : err.responseText
                        });
                    });
                }
            });
            return false;
        });

        // удалить выбранные правила
        $('.edit-questions-form').on('click','#questions .actions-panel .action-delete',function () {
            debugger;
            var ids = [];
            $('#questions .quest-list .check-item:checked').each(function () {
                ids.push($(this).closest('.wblock').data('quest_id'));
            });
            message.confirm({
                text: 'Подтвердите удаление правил.',
                type: 'delete',
                ok: function () {
                    $.post('/' + moduleUrl + '/deleteQuestion/', {
                        id: ids
                    }, function (res) {
                        if (res.content) {
                            $('.edit-questions-form').html(res.content);
                            $(window).resize();
                            ui.initAll();
                        }
                    }, 'json').error(function (err) {
                        message.errors({
                            text: 'Ошибка сервера: ' + err.status,
                            descr: (err.status === 404 || err.status === 200) ? '' : err.responseText
                        });
                    });
                }
            });
            return false;
        });
    })
});