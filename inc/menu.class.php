<?php
class PluginMedullaMenu extends CommonDBTM {
    static $rightname = 'config';

    /**
     * getTypeName
     *
     * @param  int $nb
     * @return string
     */
    static function getTypeName($nb = 0)
    {
        return __("Medulla", 'medulla');
    }

    /**
     * get menu content
     *
     * @return array
     */
    static function getMenuContent()
    {
        $menu = array();

        $menu['title'] = "Medulla";
        $menu['page'] = "/plugins/medulla/front/medulla.php";
        $menu['icon']  = "fas fa-laptop";
        return $menu;
    }
}