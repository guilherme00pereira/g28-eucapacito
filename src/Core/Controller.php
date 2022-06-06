<?php

namespace G28\Eucapacito\Core;


class Controller {

    public function __construct()
	{
		add_action('admin_menu', array($this, 'addMenuPage' ));
	}

    public function addMenuPage()
	{
		add_menu_page(
			'Eu Capacito WebApp',
			'Eu Capacito WebApp',
			'manage_options',
			'eucapacito-webapp',
			array( $this, 'renderMenuPage' ),
            plugins_url( 'g28-eucapacito/assets/img/admin-menu-icon.jpg' ),//'dashicons-dashboard',
            58
		);
	}

	public function renderMenuPage()
	{
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $default_tab = null;
        $tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
		?>
		<div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <nav class="nav-tab-wrapper">
            <a href="?page=my-plugin" class="nav-tab <?php if($tab===null):?>nav-tab-active<?php endif; ?>">Default Tab</a>
            <a href="?page=my-plugin&tab=settings" class="nav-tab <?php if($tab==='settings'):?>nav-tab-active<?php endif; ?>">Settings</a>
            <a href="?page=my-plugin&tab=tools" class="nav-tab <?php if($tab==='tools'):?>nav-tab-active<?php endif; ?>">Tools</a>
            </nav>

            <div class="tab-content">
            <?php switch($tab) :
            case 'settings':
                echo 'Settings';
                break;
            case 'tools':
                echo 'Tools';
                break;
            default:
                echo 'Default tab';
                break;
            endswitch; 
                ?>
            </div>
        </div>
		<?php
	}

}