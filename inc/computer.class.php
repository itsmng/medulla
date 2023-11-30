<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
include_once GLPI_ROOT . '/inc/includes.php';

class PluginMedullaComputer extends CommonDBTM {

    function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
    {
        return "Medulla";
    }

    static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {
        require_once GLPI_ROOT . "/vendor/autoload.php";
        $loader = new FilesystemLoader(GLPI_ROOT . Plugin::getWebDir("medulla") . "/templates");
        $twig = new Environment($loader);
        echo($twig->render('computer.twig'));
    }
}