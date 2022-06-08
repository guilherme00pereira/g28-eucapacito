<?php

use G28\Eucapacito\Core\Plugin;

$default_tab = null;
$tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <nav class="nav-tab-wrapper">
    <a href="?page=my-plugin" class="nav-tab <?php if($tab===null):?>nav-tab-active<?php endif; ?>">Mensagens</a>
    <a href="?page=my-plugin&tab=settings" class="nav-tab <?php if($tab==='settings'):?>nav-tab-active<?php endif; ?>">Configurações</a>
    </nav>

    <div class="tab-content">
    <?php switch($tab) :
    case 'settings':
        echo "Em breve";
        break;
    default:
        include sprintf( "%smessage-settings.php", Plugin::getTemplateDir() );
        break;
    endswitch; 
        ?>
    </div>
</div>