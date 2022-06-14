(function ($) {

    $(document).on('click', '#eucap_btn_upload', function (e) {
        
        var button = $(this),
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
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			button.html('<img src="' + attachment.url + '">').next().show().next().val(attachment.id);
		}).open();

    });

}(jQuery));