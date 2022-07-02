<?php

use G28\Eucapacito\Options\MessageOptions;

?>

<form method="post" action="options.php">
        <?php
        settings_fields(MessageOptions::OPTIONS_GROUP);
        do_settings_sections(MessageOptions::OPTIONS_NAME);
        submit_button("Salvar alterações");
        ?>
    </form>