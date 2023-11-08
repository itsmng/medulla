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

 include_once GLPI_ROOT . '/inc/includes.php';
 include_once Plugin::getPhpDir('medulla') . '/inc/form.utils.php';

 class PluginMedullaMedulla extends CommonDBTM {
    function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
    {
        return "Medulla";
    }

    static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0)
    {
        $form = [
            'action' => Plugin::getWebDir('looztick') . '/front/looztick.form.php',
            'submit' => 'Link',
            'content' => [
                '' => [
                    'visible' => true,
                    'inputs' => [
                        "QR code" => [
                            'name' => 'qrcode',
                            'id' => 'looztick_qrcode_dropdown',
                            'type' => 'select',
                        ],
                    ]
                ]
            ]
        ];
        echo renderForm($form);
        return true;
    }
 }