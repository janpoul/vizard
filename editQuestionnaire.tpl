{?$pageTitle = $title . ' — Управление сайтом | Сантехкомплект'}
{?$admin_page = 1}
{?$site_link = '/main/salesSupport/'}
<h1>{$title}</h1>
{*{include file="Admin/components/actions_panel.tpl"*}
	{*buttons = array(*}
		{*'save' => '#',*}
		{*'|',*}
		{*'add' => '/questionnaires/editQuestion/'*}
	{*)}*}

<form class="edit-questions-form" action="/questionnaires/saveQuestions/">
	<ul class="question-list">
		{include file="Controllers/Site/Questionnaires/questionList.tpl"}
	</ul>
</form>
        
<div class="popup-window popup-add-question">
    <form action="/questionnaires/addQuestions/">
        <table class="ribbed">
            <tr>
                <td>
                    <input type="text" name="question" />
                </td>
            </tr>
        </table>
        <div class="buttons">
            <div class="submit a-button-blue">Создать</div>
        </div>
    </form>
</div>