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

class PluginMedullaMedulla extends CommonDBTM
{

    function getAgentInfo() : array {
        global $DB;
        $query = "SELECT * FROM `glpi_plugin_medulla_config` WHERE `id` = 1";
        $result = $DB->query($query);
        $config = iterator_to_array($result)[0];
        return [
            'scheme' => 'https',
            "host" => $config['host'],
            "port" => $config['port'],
            'login' => $config['username'],
            'password' => $config['password']
        ];
    }

    /**
     * Exécute une requête XML-RPC.
     *
     * @param string $method Le nom de la méthode XML-RPC à appeler.
     * @param array $params Les paramètres à passer à la méthode.
     * @param bool $includeCookie Indique si le cookie de session doit être inclus dans la requête.
     * @return array Un tableau contenant les en-têtes HTTP et le corps de la réponse.
     * @throws Exception Si une erreur se produit lors de la connexion ou de l'envoi de la requête.
     */
    function executeRequest($method, $params, $includeCookie = false) : array {
        $agentInfo = $this->getAgentInfo();
        $requestXml = xmlrpc_encode_request($method, $params, ['output_type' => 'php', 'verbosity' => 'pretty', 'encoding' => 'UTF-8']);

        // On définit les en-têtes HTTP
        $url = "/";
        $httpQuery  = "POST " . $url . " HTTP/1.0\r\n";
        $httpQuery .= "User-Agent: MMC web interface\r\n";
        $httpQuery .= "Host: " . $agentInfo["host"] . ":" . $agentInfo["port"] . "\r\n";
        $httpQuery .= "Content-Type: text/xml\r\n";
        $httpQuery .= "Content-Length: " . strlen($requestXml) . "\r\n";
        // On ajoute le cookie si nécessaire
        if ($includeCookie) {
            $httpQuery .= "Cookie: " . $_COOKIE['medulla'] . "\r\n";
        }
        $httpQuery .= "Authorization: Basic " . base64_encode($agentInfo["login"] . ":" . $agentInfo["password"]) . "\r\n\r\n";
        $httpQuery .= $requestXml;

        // Configurer le contexte SSL
        // 'allow_self_signed' est défini sur false pour n'accepter que les certificats signés par une autorité de certification reconnue
        // 'verify_peer' est défini sur true pour vérifier le certificat SSL du serveur
        $context = stream_context_create();
        $proto = $agentInfo["scheme"] == "https" ? "ssl://" : "";
        if ($proto) {
            stream_context_set_option($context, "ssl", "allow_self_signed", false);
            stream_context_set_option($context, "ssl", "verify_peer", true);
        }

        // On ouvre la connexion au serveur
        $socket = stream_socket_client($proto . $agentInfo["host"] . ":" . $agentInfo["port"], $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
        if (!$socket) {
            throw new Exception("Unable to connect to XML-RPC server: $errstr ($errno)");
        }

        // On ajoute un délai de 60 secondes pour la lecture
        stream_set_timeout($socket, 60);

        if (!fwrite($socket, $httpQuery)) {
            throw new Exception("Unable to send data to XML-RPC server");
        }

        $responseXml = '';
        while (!feof($socket)) {
            $ret = fgets($socket, 128);
            $responseXml .= $ret;
        }
        fclose($socket);

        // Séparez les en-têtes HTTP du corps de la réponse
        list($headers, $body) = explode("\r\n\r\n", $responseXml, 2);
        return [$headers, $body];
    }

    function authenticateAndGetCookie($method, $params) : string {
        list($header, $body) = $this->executeRequest($method, $params);

        $headers_array = [];
        $header_lines = explode("\r\n", $header);
        foreach ($header_lines as $header) {
            $parts = explode(': ', $header, 2);
            if (count($parts) == 2) {
                $headers_array[$parts[0]] = $parts[1];
            }
        }

        if (isset($headers_array['Set-Cookie'])) {
            $cookie = $headers_array['Set-Cookie'];
            $cookie = $cookie;
        } else {
            throw new Exception("Unable to authenticate to XML-RPC server.");
        }
        return $cookie;
    }

    function sendXmlRpcRequest($method, $params) {
        list($header, $body) = $this->executeRequest($method, $params, true);
        $responseXml = substr($body, strpos($body, '<?xml'));
        $response = xmlrpc_decode($responseXml);
        if (is_array($response) && xmlrpc_is_fault($response)) {
            throw new Exception("XML-RPC fault: " . $response['faultString'] . " (" . $response['faultCode'] . ")");
        }
        return $response;
    }

    function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {
        return "Medulla";
    }

    static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) : bool {
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
