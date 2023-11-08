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
class PluginMedullaConfig extends CommonDBTM {
    /**
     * Displays the configuration page for the plugin
     * 
     * @return void
     */
    public function showConfigForm() {

        $image = Plugin::getWebDir('medulla').'/img/medulla.png';
        $form = [
            'action' => $_SERVER['PHP_SELF'],
            'buttons' => [
                [
                    'type' => 'submit',
                    'name' => 'update_config',
                    'value' => __('Update'),
                    'class' => 'submit-button btn btn-warning',
                ],
                [
                    'type' => 'button',
                    'name' => 'sync',
                    'value' => __('Sync'),
                    'class' => 'submit-button btn btn-primary',
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
                    ]
                ],
                __('Configuration') => [
                    'visible' => true,
                    'inputs' => [
                        __('Medulla server endpoint') => [
                            'type' => 'text',
                            'name' => 'endpoint',
                            'placeholder' => 'https://medulla.example.com',
                        ],
                    ]
                ]
            ]
        ];
        require Plugin::getPhpDir('medulla') . "/inc/form.utils.php";
        echo renderForm($form);
    }

    public function updateConfig() {
    }
}
