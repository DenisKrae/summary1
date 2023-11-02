<?php

namespace Drupal\summary\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

class WarningsController extends ControllerBase {

  public function getStatusReportWarnings() {
    $data = [
      'warnings' => [],
      'checked' => [],
    ];

    $status_report = \Drupal::service('system.manager');
    $requirements = $status_report->listRequirements();

    foreach ($requirements as $key => $requirement) {
      if ($requirement['severity'] == REQUIREMENT_WARNING) {
        $data['warnings'][$key] = [
          'title' => $requirement['title'],
          'value' => $requirement['value'],
          'severity' => $requirement['severity'],
          'description' => $requirement['description'] ?? NULL,
        ];
      } elseif ($requirement['severity'] == REQUIREMENT_OK) {
        $data['checked'][$key] = [
          'title' => $requirement['title'],
          'value' => $requirement['value'],
          'severity' => $requirement['severity'],
          'description' => $requirement['description'] ?? NULL,
        ];
      }
    }

    return new JsonResponse($data);
  }
}
