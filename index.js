$(function(){
	$('.actions-panel .action-import A').bind('click', function() {
		$('.popup-import-giftcards').dialog({
			title: 'Импорт карт из xls'
		}).dialog('open');
		return false;
	});

	$('.import-giftcards').submit(function(evt){
		evt.preventDefault();
		$(this).ajaxSubmit({
			type: 'post',
			dataType: 'json',
			success: function(res){
				if(res.errors === null){
					$('.giftcard-items').html(res.content);
				} else {
					console.log(res.errors);
				}
				$('.popup-import-giftcards').dialog('close');
			}
		});
	});
    $('.interview__fields-block:last .next').addClass("hidden");
    $('.interview__fields-block:last .buttons').removeClass("hidden");
	$('.interview__fields-block:first').removeClass("hidden");

	$('.next').on('click',function() {
        $(this).closest('.interview__fields-block').addClass("hidden");
        $(this).closest('.interview__fields-block').next('.interview__fields-block').removeClass("hidden");
    });

    $('.edit-content.m-edit-open .add-btn').click(function () {
        debugger;
    });

});