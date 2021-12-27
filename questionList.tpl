<div id="questions" class="tab-page actions-cont m-current">
    <div class="content-scroll">
        <div class="aside-panel">
            {include file="Admin/components/actions_panel.tpl"
            multiple = false
            buttons = array(
            'add' => array(
            'text' => 'Добавить'
            ),
            'delete' =>array(
            'text' => 'Удалить'
            )
            )}
        </div>
        <div class="viewport">
            <div class="quest-list white-blocks">
                {foreach from=$questions item=question}
                    <div class="wblock white-block-row shifty" data-position="{$question.position}"
                         data-quest_id="{$question.id}">
                        <div class="w02 drag-drop">
                            <input type="hidden" name="type_id" value="{$question.id}"/>
                            <input type="hidden" name="type_position" value="{$question.position}"/>
                        </div>
                        <div class="w05">
                            <input type="checkbox" name="check[]" value="{$question.id}" class="check-item" />
                        </div>
                        <div class="w9">{$question.question}</div>
                        <a href = "/questionnaires/editQuestion/?rule_id={$question.id}" class="action-button action-edit edit-type w1 m-border m-active reload" title="Редактировать">
                            <i class="icon-edit"></i>
                        </a>
                        <div class="action-button action-delete w1 m-border m-active delete" title="Удалить">
                            <i class="icon-delete"></i>
                        </div>
                    </div>
                {/foreach}
            </div>
        </div>
    </div>
</div>

