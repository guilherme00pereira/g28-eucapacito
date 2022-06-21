(function ($) {

	$(document).ready(function(){
		$('#eucap-banner-list').sortable({
			placeholder: "ui-state-highlight",
			cursor: 'move',
		  });
		//imageList.disableSelection();
	})	

    $(document).on('click', '#eucap_btn_upload', function (e) {
        const imageList = $('#eucap-banner-list');
		custom_uploader = wp.media({
			title: 'Inserir banner',
			library : {
				type : 'image'
			},
			button: {
				text: 'Usar esta imagem'
			},
			multiple: false
		}).on('select', function() {
			const attachment = custom_uploader.state().get('selection').first().toJSON();
			console.log(typeof attachment.sizes.medium == 'undefined');
			const imagem = typeof attachment.sizes.medium == 'undefined' ? attachment.sizes.thumbnail.url : attachment.sizes.medium.url;
			imageList.append(`
				<li class="eucap-banner-box" data-id="${attachment.id}">
					<div>
						<button type="button" class="button button-danger exclude-btn">X</button>
					<div>
						<img src="${imagem}" />
					</div>
					<div>
						<label for="banner-link-${attachment.id}">Link: </label>
						<input id="banner-link-${attachment.id}" type="text" />
					</div>
					<div>
						<label for="banner-device-${attachment.id}">Exibir: </label>
						<select id="banner-device-${attachment.id}" name="banner-device-${attachment.id}">
							<option value="desktop">Desktop</option>
							<option value="mobile">Mobile</option>
						</select>
					</div>
				</li>
			`);

		}).open();
    });

	$(document).on('click', '.exclude-btn', function (e) {
		$(this).parents('.eucap-banner-box').remove();
	});

	$(document).on('click', '#btnSaveBanners', function (e) {
		$('#loadingBanners').show();
		$('#messageBanner').hide();
		let banners = [];
		$('#eucap-banner-list').children().each(function() {
			const id = $(this).data('id');
			banners.push({
				id: id,
				link: $('#banner-link-' + id).val(),
				device: $('#banner-device-' + id).val(),
			});
		})
		let params = {
			action: ajaxobj.action_saveBanner,
			nonce: ajaxobj.eucap_nonce,
			banners: JSON.stringify(banners)
		}
		$.post(ajaxobj.ajax_url, params, function(res){
			console.log(res)
			const div = $('#messageBanner');
			div.show().removeClass();
			$('#loadingBanners').hide();
			if(res.success) {
				div.addClass('notice notice-success notice-alt')
			} else {
				div.addClass('notice notice-error notice-alt')
			}
			div.html(res.message)
		}, 'json');
	});

}(jQuery));