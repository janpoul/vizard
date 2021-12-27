{?$pageTitle = 'Тестирование — Управление сайтом | Сантехкомплект'}
{?$pageDescription = 'Компания Сантехкомплект предлагает сантехнику и инженерное оборудование от разных производителей оптом. Вы можете оформить заказ и купить необходимый вам товар в нашем интернет магазине. Мы занимаемся продажей сантехники в Москве, Санкт Петербурге, Краснодаре и других городах.'}
{*{?$includeOuterJS.yt = '//www.youtube.com/iframe_api'}*}

<h1>Тестирование</h1>

<div>

    <h3 class="service-page__title">Тестирование №1 </h3>
    <form class="fields-cont support-form" action="/questionnaires/sendAnswers/"">
    <input type="hidden" name="feedbackType" value="testing">
    <div class="interview">
        {if !empty($questions)}
            <div class="interview__fields poll-fields js-interview-content">
                {foreach from=$questions item=quest}
                    {?$answers = $quest.answers}
                    <div class="interview__fields-block hidden m-big">
                        <div class="interview__fields-title"><span>{$quest.question}</span></div>
                        <div class="interview__fields-input">
                            {if !empty($quest.note)}
                                <p class="descr">{$quest.note}</p>
                            {/if}
                            {foreach from=$answers key=id item=ans}
                                {if $quest.multi_answer}
                                    <label class="checkbox-circle a-nowrap">
                                        <input type="checkbox" name="question[{$quest.id}][]" value="{$id}"/>
                                        <span class="checkbox-circle__checkmark"></span>
                                        <span class="checkbox-circle__text js-field-text">{$ans}</span>
                                    </label>
                                {else}
                                    <label class="checkbox-circle a-nowrap">
                                        <input type="radio" name="question[{$quest.id}]" value="{$id}"/>
                                        <span class="checkbox-circle__checkmark"></span>
                                        <span class="checkbox-circle__text js-field-text">{$ans}</span>
                                    </label>
                                {/if}
                            {/foreach}
                        </div>
                        <div class="next">
                            <span>следующий</span>
                        </div>
                        <div class="buttons hidden">
                            <button class="btn btn-blue btn-blue checkout-button">Отправить</button>
                        </div>
                    </div>
                {/foreach}
            </div>
        {/if}
    </div>

    </form>

</div>

