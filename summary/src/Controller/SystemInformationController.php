<?php

namespace Drupal\summary\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

class SystemInformationController extends ControllerBase {

  public function getInfo() {
    $info = [];

    // Drupal-Version
    $info['drupal_version'] = \Drupal::VERSION;

    // PHP-Version
    $info['php_version'] = phpversion();

    // Datenbank-Informationen
    $database = \Drupal::database();
    $info['database'] = [
        'type' => $database->driver(),
        'version' => $database->version(),
    ];

    // Webserver
    $info['server_software'] = $_SERVER['SERVER_SOFTWARE'];

    // Betriebssystem
    $info['os'] = php_uname();

    //Geladene PHP-Erweiterungen
    $info['php_extensions'] = get_loaded_extensions();

    //Drupal-Konfigurationsdaten
    $site_config = \Drupal::config('system.site');
    $info['site_name'] = $site_config->get('name');
    $info['site_mail'] = $site_config->get('mail');

    //Benutzerinformationen
    $user_count = \Drupal::entityQuery('user')->accessCheck(FALSE)->count()->execute();
    $info['user_count'] = $user_count;

    //Server-Anforderungsinformationen
    $info['request_method'] = \Drupal::request()->getMethod();
    $info['request_uri'] = \Drupal::request()->getRequestUri();





    return new JsonResponse($info);
  }
}
