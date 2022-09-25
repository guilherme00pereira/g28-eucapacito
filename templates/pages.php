<?php

use G28\Eucapacito\Options\PageOptions;

$pages = new PageOptions();
$reactPages = $pages->getPagesRelationship();
$wpPages = get_pages();

?>

<div class="table-container">
    <div id="actionReturn"></div>
    <div id="fieldsMap">
        <button class="button button-secondary" id="resetPages">Reset</button>
        <button class="button button-primary" id="saveFields">Salvar</button>
        <span id="loadingSaveFields" style="display: none; padding-left: 15px;">
            <img src="<?php echo esc_url(get_admin_url() . 'images/spinner.gif'); ?>" alt="loading" />
        </span>
        <table id="fieldsMapList" class="table-fields" role="presentation">
            <thead>
                <tr>
                    <th>Páginas React</th>
                    <th style="width: 40px;">=</th>
                    <th>Páginas WordPress</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($reactPages) > 0) { ?>
                    <?php foreach ($reactPages as $page) { ?>
                        <tr data-react-key="<?php echo $page['key'] ?>" data-wp-id="<?php echo $page['wp_id'] ?>">
                            <td>
                                <?php echo $page['title'] ?>
                            </td>
                            <td>
                                =
                            </td>
                            <td>
                                <select name="wpPages">
                                    <?php foreach ($wpPages as $wp) { ?>
                                        <option value="<?php echo $wp->ID ?>" <?php if( $wp->ID == $page['wp_id']) { ?>selected="selected"<?php } ?>>
                                            <?php echo $wp->post_title ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr id="noFieldsRow">
                        <td colspan="4">Nenhum campo cadastrado</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>