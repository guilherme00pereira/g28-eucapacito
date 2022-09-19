<?php

use G28\Eucapacito\Core\Plugin;

/**
 * @var string $mail
 */

$user = get_user_by( 'email', $mail );
$time = time() + (24 * 3600);
$hash = md5($user->ID . ":" . $time);
set_transient($hash, $user->ID, $time);
$link = "https://www.eucapacito.com.br/redefinir-senha/?c=" . $hash;
$imageSrc = Plugin::getAssetsUrl() . "img/mail-pwd-recovery.png";

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eu Capacito</title>
</head>
<body>
    <a href="<?php echo $link ?>">
        <img src="<?php echo $imageSrc ?>" alt="Recuperar senha" />
    </a>
</body>
</html>