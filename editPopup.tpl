{*{?$currentCatalog = $current_type->getCatalog()}*}
<form class="edit-quest" action="/questionnaires/editQuestionnaire/">
    <input type="hidden" name="id" value="{!empty($question.id)? $question.id:'new'}">
    <div class="content-top">
        <h1>{if empty($question.id)}Добавление{else}Редактирование{/if} вопроса</h1>
        <div class="content-options">
            {?$buttons = array(
            'back' => array('text' => 'Отмена'),
            'save' => array(
            'url' => '/questionnaires/saveQuestions/',
            'class' => 'submit'
            )
            )}
            {include file="Admin/components/actions_panel.tpl"
            assign = addFormButtons
            buttons = $buttons}
            {$addFormButtons|html}
        </div>
    </div>

    <div class="content-scroll">
        <div class="white-blocks viewport">

            <div class="wblock white-block-row">
                <div class="w3">
                    <strong>
                        Вопрос
                    </strong>
                </div>
                <div class="w9">
                    <input type="text" name="question"
                           value="{!empty($question.id)? $question.question:''}">
                </div>
            </div>

            <div class="wblock white-block-row">
                <div class="w3">
                    <span>
                        Примечание
                    </span>
                </div>
                <div class="w9">
                    <textarea name="note" rows="4">
                        {!empty($question.id)? $question.note:""}
                        </textarea>
                </div>
            </div>

            <div class="wblock white-block-row">
                <div class="w3">
                    <span>
                        Правильные ответы
                    </span>
                </div>
                <div class="w9">
                    <textarea name="check" rows="4">
                        {!empty($question.id)? $question.check:''}
                        </textarea>
                </div>
            </div>
            <div class="wblock">
                <div class="white-block-row">

                </div>
                <div class="white-inner-cont answers">
                    {if !empty($question.id)}
                        {foreach from=$question.answers key=a_id item=answer name=answers}
                            <div class="white-block-row" data-ans="{$a_id}">
                                <div class="w3">
                                    <span>Ответ {iteration}</span>
                                </div>
                                <div class="w7">
                                    <input type="text"
                                           name="answer[{$a_id}]"
                                           value="{$answer}">
                                </div>
                                <div class="w2">
                                    <div class="delete-object action-button w05">
                                        <i class="icon-prop-delete"></i>
                                    </div>
                                </div>
                            </div>
                        {/foreach}
                    {/if}

                    <div class="white-block-row new-answer row">
                        <div class="w3">
                        </div>
                        <div class="w7">
                            <input type="text" placeholder="Добавить ответ..."
                            data-name="answer[]" name="new-answer" value="">
                        </div>
                        <div class="w2">

                            <div class="add-object add-btn w3">
                                <i class="icon-add"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="wblock white-block-row">
                <label class="w12">

                    <input type="checkbox"
                           name="multi_answer"{if !empty($question.id) && $question.multi_answer } checked="checked"{/if}>
                    <span>Разрешить несколько вариантов ответа</span>
                </label>
            </div>
        </div>
    </div>

</form>