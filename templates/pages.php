<?php

$reactPages = [
    'home'      => 'Home',
    'cursos'    => 'Cursos'
];

$wpPages = get_pages();

?>

<div class="table-container">
    <div id="actionReturn"></div>
    <div id="fieldsMap">
        <button class="button addRow" data-type="field"> + adicionar relacionamento</button>
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
                    <?php foreach ($reactPages as $index => $page) { ?>
                        <tr>
                            <td data-react-page="<?php echo $index ?>">
                                <?php echo $page ?>
                            </td>
                            <td>
                                =
                            </td>
                            <td>
                                <select id="wpPages">
                                    <?php foreach ($wpPages as $page) { ?>
                                        <option value="<?php echo $page->ID ?>"><?php echo $page->post_title ?></option>
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