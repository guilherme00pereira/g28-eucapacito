<?php

use G28\Eucapacito\Core\Plugin;

$default_tab = null;
$tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <nav class="nav-tab-wrapper">
    <a href="?page=eucapacito-webapp" class="nav-tab <?php if($tab===null):?>nav-tab-active<?php endif; ?>">Mensagens</a>
    <a href="?page=eucapacito-webapp&tab=banners-home" class="nav-tab <?php if($tab==='banners-home'):?>nav-tab-active<?php endif; ?>">Banners da Home</a>
    </nav>

    <div class="tab-content">
    <?php switch($tab) :
    case 'banners-home':
        include sprintf( "%sbanners-home.php", Plugin::getTemplateDir() );
        break;
    default:
        include sprintf( "%smessage-settings.php", Plugin::getTemplateDir() );
        break;
    endswitch; 
        ?>
    </div>
</div>