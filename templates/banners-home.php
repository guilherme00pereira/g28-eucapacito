<?php

use G28\Eucapacito\Core\OptionsManager;

/**
 * @var array $banners
 */


?>
<div class="eucap-banner-wrapper">
    <div>
        <button id="eucap_btn_upload" type="button" class="button button-primary button-hero">Adicionar novo banner</button>
    </div>
    <ul id="eucap-banner-list" class="eucap-banners">
        <?php foreach($banners as $banner) { ?>
            <li class="eucap-banner-box" data-id="<?= $banner['id'] ?>">
					<div>
						<button type="button" class="button button-danger exclude-btn">X</button>
					<div>
						<img src="<?= $banner['image'] ?>">
					</div>
					<div>
						<label for="banner-link-<?= $banner['id'] ?>">Link: </label>
						<input id="banner-link-<?= $banner['id'] ?>" type="text" value="<?= $banner['link'] ?>" />
					</div>
					<div>
						<label for="banner-device-<?= $banner['id'] ?>">Exibir: </label>
						<select id="banner-device-<?= $banner['id'] ?>" name="banner-device-<?= $banner['id'] ?>">
							<option value="desktop" <?= $banner['device'] === 'desktop' ? 'selected' : ''  ?>>Desktop</option>
							<option value="mobile" <?= $banner['device'] === 'mobile' ? 'selected' : ''  ?>>Mobile</option>
						</select>
					</div>
				</li>
        <?php } ?>
    </ul>
    <div>
        <button id="btnSaveBanners" type="button" class="button button-primary">Salvar alterações</button>
        <span id="loadingBanners" style="display: none;">
            <img src="<?php echo esc_url( get_admin_url() . 'images/spinner.gif' ); ?>" />
        </span>
    </div>
    <div id="messageBanner"></div>
</div>