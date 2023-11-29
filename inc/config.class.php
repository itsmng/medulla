<?php

use DoctrineModule\Options\Authentication;

/**
 * ---------------------------------------------------------------------
 * ITSM-NG
 * Copyright (C) 2022 ITSM-NG and contributors.
 *
 * https://www.itsm-ng.org
 *
 * based on GLPI - Gestionnaire Libre de Parc Informatique
 * Copyright (C) 2003-2014 by the INDEPNET Development Team.
 *
 * ---------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of ITSM-NG.
 *
 * ITSM-NG is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * ITSM-NG is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ITSM-NG. If not, see <http://www.gnu.org/licenses/>.
 * ---------------------------------------------------------------------
 */
class PluginMedullaConfig extends CommonDBTM {

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
        $menu['page'] = "/plugins/medulla/front/config.form.php";
        $menu['icon']  = "fas fa-laptop";
        return $menu;
    }

    /**
     * Displays the configuration page for the plugin
     * 
     * @return void
     */
    function showConfigForm() : void {
        global $DB;
        $image = Plugin::getWebDir('medulla').'/img/medulla.png';
        $action = Plugin::getWebDir('medulla').'/front/config.form.php';
        $config = iterator_to_array($DB->query("SELECT * FROM `glpi_plugin_medulla_config` WHERE `id` = 1"));
        $form = [
            'action' => $action,
            'buttons' => [
                [
                    'type' => 'submit',
                    'name' => 'update_config',
                    'value' => __('Update'),
                    'class' => 'submit-button btn btn-warning',
                ]
            ],
            'content' => [
                '' => [
                    'visible' => true,
                    'inputs' => [
                        '' => [
                            'content' => <<<HTML
                            <img src="{$image}" alt="Medulla logo" class="mx-auto" style="max-height: 12rem;width: auto"/>
                            HTML,
                        ],
                        'action' => [
                            'type' => 'hidden',
                            'name' => 'update_config',
                            'value' => '',
                        ],
                    ]
                ],
                __('Configuration') => [
                    'visible' => true,
                    'inputs' => [
                        __('Server host') => [
                            'type' => 'text',
                            'name' => 'host',
                            'placeholder' => 'https://medulla.example.com',
                            'value' => $config[0]['host'],
                        ],
                        __('Server port') => [
                            'type' => 'number',
                            'name' => 'port',
                            'min' => 0,
                            'max' => 65535,
                            'placeholder' => '443',
                            'value' => $config[0]['port'],
                        ],
                        __('Username') => [
                            'type' => 'text',
                            'name' => 'username',
                            'value' => $config[0]['username'],
                        ],
                        __('Password') => [
                            'type' => 'password',
                            'name' => 'password',
                            'value' => Toolbox::sodiumDecrypt($config[0]['password']),
                        ],
                        __('LDAP Login') => [
                            'type' => 'text',
                            'name' => 'ldap_user',
                            'value' => $config[0]['ldap_user'],
                        ],
                        __('LDAP Password') => [
                            'type' => 'password',
                            'name' => 'ldap_password',
                            'value' => Toolbox::sodiumDecrypt($config[0]['ldap_password']),
                        ],
                    ]
                ]
            ]
        ];
        require_once GLPI_ROOT . "/ng/form.utils.php";
        echo renderTwigForm($form);
    }

    function updateConfig() : void {
        global $DB;
        $host = $_POST['host'];
        $port = $_POST['port'];
        $username = $_POST['username'];
        $password = Toolbox::sodiumEncrypt($_POST['password']);
        $ldap_user = $_POST['ldap_user'];
        $ldap_password = Toolbox::sodiumEncrypt($_POST['ldap_password']);
        $query = "UPDATE `glpi_plugin_medulla_config` SET
        `host` = '{$host}', `port` = '{$port}', `username` = '{$username}', `password` = '{$password}', `ldap_user` = '{$ldap_user}', `ldap_password` = '{$ldap_password}' WHERE `id` = 1";
        $DB->queryOrDie($query, $DB->error());
    }

    function getConfig() : array {
        global $DB;
        $query = "SELECT * FROM `glpi_plugin_medulla_config` WHERE `id` = 1";
        $result = $DB->query($query);
        return iterator_to_array($result)[0];
    }
}
