(function ($) {

    $(document).on('click', '#eucap_btn_upload', function (e) {
        const imageList = $('#eucap-banner-list');
		custom_uploader = wp.media({
			title: 'Inserir banner',
			library : {
				// uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
				type : 'image'
			},
			button: {
				text: 'Usar esta imagem' // button label text
			},
			multiple: false
		}).on('select', function() { // it also has "open" and "close" events
			const attachment = custom_uploader.state().get('selection').first().toJSON();
			console.log(attachment)
			imageList.append(`
				<div class="eucap-banner-box">
					<div>
						<img src="${attachment.sizes.medium.url}">
					</div>
					<div>
						<label for="">
						<input id="banner-link-1" type="text" />
					</div>
					<div>
						<select>
							<option value="desktop">Desktop</option>
							<option value="mobile">Mobile</option>
						</select>
					</div>
					<div>
						<button type="button" class="button action">Salvar</button>
					</div>
				</div>`
			);
			//button.html('<img src="' + attachment.url + '">').next().show().next().val(attachment.id);

		}).open();

    });

}(jQuery));