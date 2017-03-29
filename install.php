<?php
if (php_sapi_name() !== 'cli') {
    exit('It\'s no cli!');
}

//Configurations - you can change...
$name = 'Router';
$file = 'Router.php';
$configPath = defined('_CONFIG') ? _CONFIG.'Router/' : dirname(dirname(dirname(__DIR__))).'/Config/Router/';

//Checkin
if (is_file($configPath.$file)) {
    return "\n--- $name configuration file already exists!";
}
if (!is_dir($configPath)) {
    @mkdir($configPath, 0777, true);
    @chmod($configPath, 0777);
    if (!is_writable($configPath)) {
        return "\n\n--- Configuration file for $name not instaled!\n\n";
    }
}

//Copiando o arquivo de configuração para o CONFIG da aplicação
copy(__DIR__.'/Config/Router/Router.php', $configPath.$file);

//Return to application installer
return "\n--- $name instaled!";
