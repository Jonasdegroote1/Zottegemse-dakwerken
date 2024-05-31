<?php

namespace Drupal\inventory_system\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Database;
use Drupal\taxonomy\Entity\Term;

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
    // Select the category names and ids from the custom database table.
    $connection = Database::getConnection();
    $query = $connection->select('categories', 'c')
      ->fields('c', ['title', 'category_id'])
      ->orderBy('title');
    $result = $query->execute()->fetchAll();

    // Prepare table header.
    $header = [
      'category_name' => $this->t('Category Name'),
      'operations' => $this->t('Operations'),
    ];

    // Prepare table rows.
    $rows = [];
    foreach ($result as $record) {
      $edit_url = Url::fromRoute('inventory_system.category_edit_form', ['tid' => $record->category_id]);
      $edit_link = Link::fromTextAndUrl($this->t('Edit'), $edit_url);
      $delete_url = Url::fromRoute('inventory_system.category_delete', ['tid' => $record->category_id]);
      $delete_link = Link::fromTextAndUrl($this->t('Delete'), $delete_url);

      $rows[] = [
        'category_name' => $record->title,
        'operations' => [
          'data' => [
            '#type' => 'operations',
            '#links' => [
              'edit' => [
                'title' => $edit_link->getText(),
                'url' => $edit_link->getUrl(),
              ],
              'delete' => [
                'title' => $delete_link->getText(),
                'url' => $delete_link->getUrl(),
              ],
            ],
          ],
        ],
      ];
    }

    $add_button = [
      '#type' => 'link',
      '#title' => $this->t('Add category'),
      '#url' => Url::fromRoute('inventory_system.category_add_form'),
      '#attributes' => [
        'class' => ['button', 'button--primary'],
      ],
    ];

    return [
    '#type' => 'container',
    '#attributes' => ['class' => ['inventory-list-container']],
    'add_button' => $add_button,
    'table' => [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $rows,
        '#empty' => $this->t('No categories found.'),
    ],
];

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

    // Delete the category from the database table
    $query = \Drupal::database()->delete('categories')
      ->condition('category_id', $tid)
      ->execute();

    if ($query) {
      $this->messenger->addMessage($this->t('Category with ID %tid has been deleted.', ['%tid' => $tid]));
    } else {
      $this->messenger->addMessage($this->t('Unable to delete category. Category with ID %tid not found.', ['%tid' => $tid]), 'error');
    }

    return $this->redirect('inventory_system.category_list');
  }
}
