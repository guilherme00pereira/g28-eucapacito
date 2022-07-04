<?php

use G28\Eucapacito\Options\MessageOptions;

/**
 * @var array $banners
 */


?>
<div class="eucap-banner-wrapper">
    <div>
        <button id="eucap_btn_image_upload" type="button" class="button button-primary">
			<span class="dashicons dashicons-format-image" style="margin: 4px 4px 0 0;"></span>
			Adicionar nova imagem
		</button>
		<button id="eucap_btn_video_upload" type="button" class="button button-primary">
			<span class="dashicons dashicons-format-video" style="margin: 4px 4px 0 0;"></span>
			Adicionar novo vídeo *
		</button>
		<button id="btnSaveBanners" type="button" class="button eucap-button-success">Salvar alterações</button>
        <span id="loadingBanners" style="display: none;">
            <img src="<?php echo esc_url( get_admin_url() . 'images/spinner.gif' ); ?>" />
        </span>
    </div>
	<p style="color: #777;">* No momento exibe apenas vídeos do Youtube</p>
    <ul id="eucap-banner-list" class="eucap-banners">
        <?php foreach($banners as $banner) { ?>
            <li class="eucap-banner-box" data-id="<?= $banner['id'] ?>" data-hash="<?= $banner['hash'] ?>" data-type="<?= $banner['type'] ?>">
					<div>
						<h3><?= $banner['type'] === "image" ? "Imagem" : "Vídeo" ?></h3>
						<button type="button" class="button button-danger exclude-btn">X</button>
					</div>
					<div>
						<img src="<?= $banner['image'] ?>">
					</div>
					<div>
						<label for="banner-link-<?= $banner['hash'] ?>">Link: </label>
						<input id="banner-link-<?= $banner['hash'] ?>" type="text" value="<?= $banner['link'] ?>" />
					</div>
					<div>
						<label for="banner-device-<?= $banner['hash'] ?>">Exibir: </label>
						<select id="banner-device-<?= $banner['hash'] ?>" name="banner-device-<?= $banner['hash'] ?>">
							<option value="desktop" <?= $banner['device'] === 'desktop' ? 'selected' : ''  ?>>Desktop</option>
							<option value="mobile" <?= $banner['device'] === 'mobile' ? 'selected' : ''  ?>>Mobile</option>
						</select>
					</div>
				</li>
        <?php } ?>
    </ul>
    <div id="messageBanner"></div>
</div>