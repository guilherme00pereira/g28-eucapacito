(function ($) {

	$(document).ready(function(){
		$('#eucap-banner-list').sortable({
			placeholder: "ui-state-highlight",
			cursor: 'move',
		  });
		$('#logFiles').change( function (e) {
			$('#loadingLogs').show();
			let params = {
				action: ajaxobj.action_getLog,
				nonce: ajaxobj.eucap_nonce,
				filename: JSON.stringify(e.target.value)
			}
			$.get(ajaxobj.ajax_url, params, function(res){
				const div = $('#logFileContent');
				div.html(res.message[1]);
				$('#loadingLogs').hide();
			}, 'json');
		});
	})	

    $(document).on('click', '#eucap_btn_image_upload', function (e) {
        const mediaList = $('#eucap-banner-list');
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
			const imagem = typeof attachment.sizes.medium == 'undefined' ? attachment.sizes.thumbnail.url : attachment.sizes.medium.url;
			mediaList.append(`
				<li class="eucap-banner-box" data-id="${attachment.id}" data-hash="${attachment.id}" data-type="image">
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

	$(document).on('click', '#eucap_btn_video_upload', function (e) {
        const mediaList = $('#eucap-banner-list');
		const id = mediaList.size() + 1;
		mediaList.append(`
			<li class="eucap-banner-box" data-id="${id}" data-hash="${id}" data-type="video">
				<div>
					<button type="button" class="button button-danger exclude-btn">X</button>
				</div>
				<div>
					<label for="banner-link-${id}">Vídeo URL: </label>
					<input id="banner-link-${id}" type="text" />
				</div>
				<div>
					<label for="banner-device-${id}">Exibir: </label>
					<select id="banner-device-${id}" name="banner-device-${id}">
						<option value="desktop">Desktop</option>
						<option value="mobile">Mobile</option>
					</select>
				</div>
			</li>
		`);
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
			const hash = $(this).data('hash');
			const type = $(this).data('type');
			banners.push({
				id: id,
				link: $('#banner-link-' + hash).val(),
				device: $('#banner-device-' + hash).val(),
				type: type
			});
		})
		let params = {
			action: ajaxobj.action_saveBanner,
			nonce: ajaxobj.eucap_nonce,
			banners: JSON.stringify(banners)
		}
		$.post(ajaxobj.ajax_url, params, function(res){
			const div = $('#messageBanner');
			div.show().removeClass();
			$('#loadingBanners').hide();
			if(res.success) {
				div.addClass('notice notice-success notice-alt')
			} else {
				div.addClass('notice notice-error notice-alt')
			}
			div.html(res.message);

		}, 'json');
	});

	$(document).on('click', '#runAvatar', function (e) {
		$('#loadingLogs').show();
		let params = {
			action: ajaxobj.action_runAvatar,
			nonce: ajaxobj.eucap_nonce,
		}
		$.get(ajaxobj.ajax_url, params, function(res){
			const div = $('#logFileContent');
			div.html(res.message[1]);
			$('#loadingLogs').hide();
		}, 'json');
	});


// region Pages Relationship
	$(document).on('click', '#saveFields', function (e) {
		const div = $('#actionReturn');
		const loading = $('#loadingSaveFields');
		
		let map = [];
		const list = $('#fieldsMapList tbody').children();
		list.each( function (idx) {
			const wpInput = $(this).find('input[name="wp[' + idx + ']"]')
			const reactInput = $(this).find('input[name="react[' + idx + ']"]')
			const flag = $(this).data('required');
			map.push({
				wp: wpInput.val(),
				react: reactInput.val(),
				required: flag ?? false
			})
		})
		let params = {
			action: ajaxobj.action_saveFields,
			nonce: ajaxobj.eucap_nonce,
			fields: map
		}
		
		$.post(ajaxobj.ajax_url, params, function(res){
			loading.show()
			if(res.success) {
				div.addClass('notice notice-success notice-alt')
			} else {
				div.addClass('notice notice-error notice-alt')
			}
			div.html(res.message);
			loading.hide()
		}, 'json');
	});

	$(document).on('click', '#resetPages', function (e) {
		const div = $('#actionReturn');
		const loading = $('#loadingSaveFields');
		let params = {
			action: ajaxobj.action_resetPages,
			nonce: ajaxobj.eucap_nonce,
		}
		$.post(ajaxobj.ajax_url, params, function(res){
			loading.show()
			if(res.success) {
				div.addClass('notice notice-success notice-alt')
			} else {
				div.addClass('notice notice-error notice-alt')
			}
			div.html(res.message);
			loading.hide()
		}, 'json');
	});

}(jQuery));