(function ($) {

	$(document).ready(function(){
		$('#eucap-banner-list').sortable({
			placeholder: "ui-state-highlight",
			cursor: 'move',
			change: function(e, ui) {
				$('#btnSaveBanners').show();
			}
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
			$('#btnSaveBanners').show();
			const attachment = custom_uploader.state().get('selection').first().toJSON();
			imageList.append(`
				<li class="eucap-banner-box" data-id="${attachment.id}">
					<div>
						<button type="button" class="button button-danger exclude-btn">X</button>
					<div>
						<img src="${attachment.sizes.medium.url}">
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

	});

	$(document).on('click', '#btnSaveBanners', function (e) {
		$('#loadingBanners').show();
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
			$('#loadingBanners').hide();
		}, 'json');
	});

}(jQuery));