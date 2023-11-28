
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

class PluginMedullaProfile extends CommonDBTM {
      	
	/**
	 * canCreate
	 *
	 * @return boolean
	 */
	static function canCreate() : bool {
		if (isset($_SESSION["profile"])) return ($_SESSION["profile"]['medulla'] == 'w');
		return false;
	}
	
	/**
	 * canView
	 *
	 * @return boolean
	 */
	static function canView() : bool {
		if (isset($_SESSION["profile"])) return ($_SESSION["profile"]['medulla'] == 'w' || $_SESSION["profile"]['medulla'] == 'r');
		return false;
	}
	
	/**
	 * createAdminAccess
	 *
	 * @param  int $ID
	 * @return void
	 */
	static function createAdminAccess($ID) : void {
		$myProf = new self();
		if (!$myProf->getFromDB($ID)) $myProf->add(array('id' => $ID, 'right' => 'w'));
	}
	
	/**
	 * addDefaultProfileInfos
	 *
	 * @param  int $profiles_id
	 * @param  array $rights
	 * @return void
	 */
	static function addDefaultProfileInfos($profiles_id, $rights) : void {
		$profileRight = new ProfileRight();

		foreach ($rights as $right => $value) {
			if (!countElementsInTable('glpi_profilerights', ['profiles_id' => $profiles_id, 'name' => $right])) {
				$myright['profiles_id'] = $profiles_id;
				$myright['name']        = $right;
				$myright['rights']      = $value;

				$profileRight->add($myright);

				$_SESSION['glpiactiveprofile'][$right] = $value;
			}
		}
	}
	
	/**
	 * changeProfile
	 *
	 * @return void
	 */
	static function changeProfile() : void {
		$prof = new self();

		if ($prof->getFromDB($_SESSION['glpiactiveprofile']['id'])) {
			$_SESSION["glpi_plugin_medulla_profile"] = $prof->fields;
		} else {
			unset($_SESSION["glpi_plugin_medulla_profile"]);
		}
	}
	
	/**
	 * getTabNameForItem
	 *
	 * @param  object $item
	 * @param  int $withtemplate
	 * @return string
	 */
	function getTabNameForItem(CommonGLPI $item, $withtemplate=0) : string {
		if (Session::haveRight("profile", UPDATE) && $item->getType() == 'Profile') {
			return __('Medulla Plugin', 'medulla');
		}

		return '';
	}
	
	/**
	 * displayTabContentForItem
	 *
	 * @param  object $item
	 * @param  int $tabnum
	 * @param  int $withtemplate
	 * @return boolean
	 */
	static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {
		if ($item->getType() == 'Profile') {
			
			$ID = $item->getID();
			$prof = new self();

			foreach (self::getRightsGeneral() as $right) {
				self::addDefaultProfileInfos($ID, [$right['field'] => 0]);
			}

			$prof->showForm($ID);
		}

		return true;
	}
	
	/**
	 * getRightsGeneral
	 *
	 * @return array
	 */
	static function getRightsGeneral() : array {
		$rights = [
			[
				'itemtype'  => 'PluginMedullaProfile',
				'label'     => __('Medulla', 'medulla'),
				'field'     => 'plugin_medulla_medulla',
				'rights'    =>  [READ => __('Allow Reading', 'medulla'), UPDATE => __('Allow editing', 'medulla')],
				'default'   => 23
			]
		];

		return $rights;
	}
	
	/**
	 * showForm
	 *
	 * @param  int $profiles_id
	 * @param  boolean $openform
	 * @param  boolean $closeform
	 * @return void
	 */
	function showForm($profiles_id = 0, $openform = true, $closeform = true) {

		if (!Session::haveRight("profile",READ)) return false;
		
		echo "<div class='firstbloc'>";

		if (($canedit = Session::haveRight('profile', UPDATE)) && $openform) {
			$profile = new Profile();
			echo "<form method='post' action='".$profile->getFormURL()."'>";
		}
		
		$profile = new Profile();
		$profile->getFromDB($profiles_id);
		$rights = $this->getRightsGeneral();
		$profile->displayRightsChoiceMatrix($rights, ['default_class' => 'tab_bg_2', 'title' => __('General')]);

		if ($canedit && $closeform) {
			echo "<div class='center'>";
			echo Html::hidden('id', ['value' => $profiles_id]);
			echo Html::submit(_sx('button', 'Save'), ['name' => 'update']);
			echo "</div>\n";
			Html::closeForm();
		}
		
		echo "</div>";
	}
}