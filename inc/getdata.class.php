<?php
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class PluginMedullaGetdata extends PluginMedullaMedulla {

    private function getaudit() : array {
        $method = "base.ldapAuth";
        $config = new PluginMedullaConfig();
        $config = $config->getConfig();
        $params = [$config['ldap_user'], "", 86400];

        $_COOKIE['medulla'] = $this->authenticateAndGetCookie($method, $params);
        $datas = $this->sendXmlRpcRequest("xmppmaster.get_deploy_by_user_with_interval", $params);

        return $datas['tabdeploy'];
    }

    function getauditlog($commandID) : array {
        $method = "base.ldapAuth";
        $config = new PluginMedullaConfig();
        $config = $config->getConfig();
        $params = [$commandID];

        $_COOKIE['medulla'] = $this->authenticateAndGetCookie($method, $params);
        $datas = $this->sendXmlRpcRequest("xmppmaster.getlinelogssession", $params);

        return $datas;
    }

    private function getcomputer() : array {

        $method = "base.ldapAuth";
        $config = new PluginMedullaConfig();
        $config = $config->getConfig();
        $params = [$config['ldap_user']];

        $_COOKIE['medulla'] = $this->authenticateAndGetCookie($method, $params);
        $packages = $this->sendXmlRpcRequest("pkgs.get_all_packages", $params);
        $packages = $packages['datas'];

        return $packages;
    }

    private function listcomputer() {

        // Get PC form bdd
        global $DB;
        $query = "SELECT * FROM `glpi_computers` WHERE is_deleted = 0";
        $packages = $DB->queryOrDie($query, $DB->error());
        $packages = iterator_to_array($packages);
        return $packages;
    }

    function pushtoview() : string {

        $packages = $this->getcomputer();
        $audits = $this->getaudit();
        $computers = $this->listcomputer();

        require_once GLPI_ROOT . "/vendor/autoload.php";
        $loader = new FilesystemLoader(GLPI_ROOT . Plugin::getWebDir("medulla") . "/templates");
        $twig = new Environment($loader);
        return($twig->render('getdata.twig', [
            'packages' => $packages,
            'audits' => $audits,
            'computers' => $computers
        ]));
    }
}