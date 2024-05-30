<?php

namespace Drupal\inventory_system\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Messenger\MessengerInterface;

class InventoryController extends ControllerBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a new InventoryController object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(Connection $database, MessengerInterface $messenger) {
    $this->database = $database;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('messenger')
    );
  }

  /**
   * Displays a list of inventory items.
   */
  public function listItems() {
    // Fetch items from the database table.
    $query = $this->database->select('items', 'i')
      ->fields('i', ['item_id', 'title', 'description', 'quantity', 'location', 'price'])
      ->execute()
      ->fetchAll();

    // Prepare table header.
    $header = [
      'item_name' => $this->t('Item Name'),
      'description' => $this->t('Description'),
      'quantity' => $this->t('Quantity'),
      'location' => $this->t('Location'),
      'price' => $this->t('Price'),
      'operations' => $this->t('Operations'),
    ];

    // Prepare table rows.
    $rows = [];
    foreach ($query as $item) {
      $edit_url = Url::fromRoute('inventory_system.edit_form', ['item_id' => $item->item_id]);
      $edit_link = Link::fromTextAndUrl($this->t('Edit'), $edit_url);
      $delete_url = Url::fromRoute('inventory_system.delete_item', ['item_id' => $item->item_id]);
      $delete_link = Link::fromTextAndUrl($this->t('Delete'), $delete_url);

      $rows[] = [
        'item_name' => $item->title,
        'description' => $item->description,
        'quantity' => $item->quantity,
        'location' => $item->location,
        'price' => $item->price,
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
      '#title' => $this->t('Add Inventory Item'),
      '#url' => Url::fromRoute('inventory_system.add_form'),
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
        '#empty' => $this->t('No inventory items found.'),
      ],
    ];
  }

  /**
   * Deletes an inventory item.
   *
   * @param int $item_id
   *   The ID of the inventory item to delete.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirect response to the inventory list page.
   */
  public function deleteItem($item_id) {
    // Fetch item from the database to check if it exists.
    $query = $this->database->select('items', 'i')
      ->fields('i', ['item_id'])
      ->condition('item_id', $item_id)
      ->execute()
      ->fetchField();

    // Check if the item exists.
    if ($query) {
      // Delete the item from the database.
      try {
        $this->database->delete('items')
          ->condition('item_id', $item_id)
          ->execute();
        $this->messenger->addMessage($this->t('Inventory item deleted successfully.'));
      } catch (\Exception $e) {
        $this->messenger->addError($this->t('An error occurred while deleting the inventory item: @error', ['@error' => $e->getMessage()]));
      }
    } else {
      $this->messenger->addError($this->t('Invalid item ID.'));
    }

    // Redirect back to the inventory list page.
    return $this->redirect('inventory_system.list');
  }
}
