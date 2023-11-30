<?php
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

 define('MEDULLAPLUGIN_VERSION', '0.1.0');
 define('MEDULLAPLUGIN_AUTHOR', 'ITSM Dev Team, AntoineLemarchand, Minzord');
 define('MEDULLAPLUGIN_HOMEPAGE', 'https://github.com/AntoineLemarchand/medulla');

function plugin_version_medulla() : array {
    return array(
        'name'           => "Medulla",
        'version'        => MEDULLAPLUGIN_VERSION,
        'author'         => MEDULLAPLUGIN_AUTHOR,
        'license'        => 'GPLv3+',
        'homepage'       => MEDULLAPLUGIN_HOMEPAGE,
        'requirements'   => [
            'glpi'   => [
               'min' => '9.5'
            ],
            'php'    => [
                'min' => '8.0'
            ]
        ]
    );
}

function plugin_medulla_check_prerequisites() : bool {
    if (version_compare(ITSM_VERSION, '2.0.0', 'lt')) {
        echo "This plugin requires ITSM >= 2.0.0";
        return false;
    }
    return true;
}

function plugin_medulla_check_config() : bool {
    return true;
}


function plugin_init_medulla() : void {
    global $PLUGIN_HOOKS;

    // Plugin::registerClass('PluginMedullaProfile', ['addtabon' => ['Profile']]);
    
    $PLUGIN_HOOKS['csrf_compliant']['medulla'] = true;

    if (Session::haveRight("profile", UPDATE)) {
        $PLUGIN_HOOKS['config_page']['medulla'] = 'front/config.form.php';
        $PLUGIN_HOOKS['menu_toadd']['medulla'] = ['tools' => array(PluginMedullaMenu::class)];
    }

    // if (Session::haveRight("computer", READ)) {
        Plugin::registerClass('PluginMedullaComputer', [
            'addtabon' => [
                'Computer',
            ]
        ]);
    // }
}
