<?php

namespace Drupal\summary\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\update\UpdateFetcherInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ModulesThemesController extends ControllerBase {

  protected $updateFetcher;

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('update.fetcher')
    );
  }

  public function __construct(UpdateFetcherInterface $update_fetcher) {
    $this->updateFetcher = $update_fetcher;
  }

  public function getModulesThemes() {
    $module_handler = \Drupal::service('module_handler');
    $installed_modules = $module_handler->getModuleList();
    $data = [];

    // Für Drupal Core
    $core = [
      'name' => 'drupal',
      'info' => [
        'version' => \Drupal::VERSION,
      ],
    ];
    $site_key = '';
    $core_data = $this->updateFetcher->fetchProjectData($core, $site_key);
    $xml = simplexml_load_string($core_data);
    $latest_core_version = isset($xml->releases->release[0]) ? (string)$xml->releases->release[0]->version : 'N/A';
    $core_severity = isset($xml->releases->release[0]->terms->term[0]->value) ? (int)$xml->releases->release[0]->terms->term[0]->value : null;

    $data['core'] = [
      'installed_version' => \Drupal::VERSION,
      'latest_version' => $latest_core_version,
      'severity' => $core_severity,
    ];

    // Für Module
    foreach ($installed_modules as $module_name => $module) {
      $module_info = \Drupal::service('extension.list.module')->getExtensionInfo($module_name);
      $project_data = $this->updateFetcher->fetchProjectData(['name' => $module_name, 'info' => $module_info], $site_key);
      $xml = simplexml_load_string($project_data);
      $latest_version = isset($xml->releases->release[0]) ? (string)$xml->releases->release[0]->version : 'N/A';
      $module_severity = isset($xml->releases->release[0]->terms->term[0]->value) ? (int)$xml->releases->release[0]->terms->term[0]->value : null;

      $data['modules'][$module_name] = [
        'installed_version' => $module_info['version'],
        'latest_version' => $latest_version,
        'severity' => $module_severity,
      ];
    }

    return new JsonResponse($data);
  }
}
