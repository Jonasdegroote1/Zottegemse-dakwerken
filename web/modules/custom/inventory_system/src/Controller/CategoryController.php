<?php

namespace Drupal\inventory_system\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\taxonomy\Entity\Term;

class CategoryController extends ControllerBase {
  public function listItems() {
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', 'category');
    $query->accessCheck(FALSE); // Set access check to FALSE
    $tids = $query->execute();

    $terms = Term::loadMultiple($tids);

    $rows = [];
    foreach ($terms as $term) {
      $rows[] = [
        'data' => [
          $term->getName(),
          $term->getDescription(),
          // Add more fields if needed
        ],
      ];
    }

    $header = [
      $this->t('Category Name'),
      $this->t('Description'),
      // Add more headers if needed
    ];

    $build = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No categories found.'),
    ];

    return $build;
  }
}
