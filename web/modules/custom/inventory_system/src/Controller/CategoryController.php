<?php

namespace Drupal\inventory_system\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CategoryController extends ControllerBase {

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a new CategoryController object.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(MessengerInterface $messenger) {
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger')
    );
  }

  /**
   * List all categories.
   */
  public function listItems() {
    $query = \Drupal::entityQuery('taxonomy_term')
      ->condition('vid', 'category')
      ->accessCheck(FALSE); // Set access check to FALSE
    $tids = $query->execute();

    $terms = Term::loadMultiple($tids);

    $rows = [];
    foreach ($terms as $term) {
      $deleteUrl = Url::fromRoute('inventory_system.category_delete', ['tid' => $term->id()]);
      $deleteLink = Link::fromTextAndUrl($this->t('Delete'), $deleteUrl);

      $rows[] = [
        'data' => [
          $term->getName(),
          $term->getDescription(),
          $deleteLink->toString(),
        ],
      ];
    }

    $header = [
      $this->t('Category Name'),
      $this->t('Description'),
      $this->t('Actions'),
    ];

    $build = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No categories found.'),
    ];

    return $build;
  }

  /**
   * Delete a category.
   *
   * @param int $tid
   *   The term ID of the category to delete.
   */
  public function deleteCategory($tid) {
    // Convert the $tid parameter to an integer if necessary
    $tid = (int) $tid;

    // Load the term entity by its ID
    $term = Term::load($tid);
    if ($term) {
      $term_name = $term->getName();
      $term->delete();
      $this->messenger->addMessage($this->t('Category %category has been deleted.', ['%category' => $term_name]));
    } else {
      $this->messenger->addMessage($this->t('Unable to delete category. Category not found.'), 'error');
    }
    return $this->redirect('inventory_system.category_list');
  }
}
