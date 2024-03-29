<?php

use G28\Eucapacito\Core\Plugin;
use G28\Eucapacito\Options\BannerOptions;

$default_tab = null;
$tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <nav class="nav-tab-wrapper">
    <a href="?page=eucapacito-webapp" class="nav-tab <?php if($tab===null):?>nav-tab-active<?php endif; ?>">Mensagens</a>
    <a href="?page=eucapacito-webapp&tab=banners-home" class="nav-tab <?php if($tab==='banners-home'):?>nav-tab-active<?php endif; ?>">Banners da Home</a>
    <a href="?page=eucapacito-webapp&tab=pages" class="nav-tab <?php if($tab==='pages'):?>nav-tab-active<?php endif; ?>">Páginas</a>    
    <a href="?page=eucapacito-webapp&tab=logs" class="nav-tab <?php if($tab==='logs'):?>nav-tab-active<?php endif; ?>">Logs</a>
    </nav>

    <div class="tab-content">
    <?php switch($tab) :
    case 'logs':
        include sprintf( "%slogs.php", Plugin::getTemplateDir() );
        break;
    case 'pages':
        include sprintf( "%spages.php", Plugin::getTemplateDir() );
        break;
    case 'banners-home':
        $banners = BannerOptions::getBanners();
        include sprintf( "%sbanners-home.php", Plugin::getTemplateDir() );
        break;
    default:
        include sprintf( "%smessages.php", Plugin::getTemplateDir() );
        break;
    endswitch; 
        ?>
    </div>
</div>