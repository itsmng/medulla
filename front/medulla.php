<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

include("../../../inc/includes.php");
Html::header("Medulla", $_SERVER["PHP_SELF"], "tools", "Medulla");

if (isset($_POST['log_audit'])){
    $logs = new PluginMedullaGetdata();
    $logs = $logs->getauditlog($_POST['log_audit']);
    $logs = $logs['log'];

    require_once GLPI_ROOT . "/vendor/autoload.php";
    $loader = new FilesystemLoader(GLPI_ROOT . Plugin::getWebDir("medulla") . "/templates");
    $twig = new Environment($loader);
    echo($twig->render('viewlog.twig', [
        'logs' => $logs,
    ]));
} else {
    $computer = new PluginMedullaGetdata();
    echo $computer->pushtoview();
}