<?php

use G28\Eucapacito\Core\OptionsManager;

?>

<form method="post" action="options.php">
        <?php
        settings_fields(OptionsManager::OPTIONS_GROUP);
        do_settings_sections(OptionsManager::OPTIONS_NAME);
        submit_button("Salvar alterações");
        ?>
    </form>