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
      ->fields('i', ['item_id', 'title', 'description', 'quantity', 'location', 'category_id'])
      ->orderBy('category_id');

    // Execute the query and fetch results.
    $results = $query->execute()->fetchAll();

    // Initialize an array to hold items grouped by category.
    $items_by_category = [];

    foreach ($results as $item) {
      // Fetch category name based on category_id.
      $category_name = $this->getCategoryName($item->category_id);

      // Add the item to the respective category.
      $items_by_category[$category_name][] = [
        'item_id' => $item->item_id,
        'item_name' => $item->title,
        'description' => $item->description,
        'quantity' => $item->quantity,
        'location' => $item->location,
        // Include other fields as needed.
      ];
    }

    // Render items grouped by category.
    $rows = [];

    foreach ($items_by_category as $category_name => $items) {
      // Render category title.
      $rows[] = [
        'data' => [
          'item_name' => [
            'data' => $category_name,
            'colspan' => 5, // Adjust colspan based on the number of columns.
          ],
        ],
        'class' => ['category-row', 'category-name'],
      ];

      // Render items within the category.
      foreach ($items as $item) {
        $edit_url = Url::fromRoute('inventory_system.edit_form', ['item_id' => $item['item_id']]);
        $edit_link = Link::fromTextAndUrl($this->t('Edit'), $edit_url);
        $delete_url = Url::fromRoute('inventory_system.delete_item', ['item_id' => $item['item_id']]);
        $delete_link = Link::fromTextAndUrl($this->t('Delete'), $delete_url);

        $quantity_class = $this->getQuantityClass($item['quantity']);

        $rows[] = [
          'data' => [
            'item_name' => $item['item_name'],
            'description' => $item['description'],
            'quantity' => $item['quantity'],
            'location' => $item['location'],
            // Include other fields as needed.
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
          ],
          'class' => [$quantity_class],
        ];
      }
    }

    $add_button = [
      '#type' => 'link',
      '#title' => $this->t('Add Inventory Item'),
      '#url' => Url::fromRoute('inventory_system.add_form'),
      '#attributes' => [
        'class' => ['button', 'button--primary'],
      ],
    ];

    $add_to_vehicle_button = [
      '#type' => 'link',
      '#title' => $this->t('Add Inventory Item to Vehicle'),
      '#url' => Url::fromRoute('inventory_system.add_to_vehicle'),
      '#attributes' => [
        'class' => ['button', 'button--primary'],
      ],
    ];

    $view_vehicles_button = [
      '#type' => 'link',
      '#title' => $this->t('View Vehicles'),
      '#url' => Url::fromRoute('inventory_system.vehicle_overview'),
      '#attributes' => [
        'class' => ['button', 'button--primary'],
      ],
    ];

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['inventory-list-container']],
      'add_button' => $add_button,
      'add_to_vehicle_button' => $add_to_vehicle_button,
      'view_vehicles_button' => $view_vehicles_button,
      'table' => [
        '#type' => 'table',
        '#header' => $this->getTableHeader(),
        '#rows' => $rows,
        '#empty' => $this->t('No inventory items found.'),
      ],
      '#attached' => [
        'library' => [
          'inventory_system/css',
        ],
      ],
    ];
  }

  /**
   * Helper function to get table header.
   */
  private function getTableHeader() {
    return [
      'item_name' => $this->t('Item Name'),
      'description' => $this->t('Description'),
      'quantity' => $this->t('Quantity'),
      'location' => $this->t('Location'),
      'operations' => $this->t('Operations'),
      // Include other fields as needed.
    ];
  }

  /**
   * Helper function to fetch category name based on category ID.
   */
  private function getCategoryName($category_id) {
    // Fetch category name from the 'categories' table based on category_id.
    // Adjust this query based on your actual database schema.
    $category_name = $this->database->select('categories', 'c')
      ->fields('c', ['title'])
      ->condition('c.category_id', $category_id)
      ->execute()
      ->fetchField();

    return $category_name ? $category_name : 'Uncategorized';
  }

  /**
   * Helper function to get the CSS class based on item quantity.
   */
  private function getQuantityClass($quantity) {
    if ($quantity < 50) {
      return 'low-quantity';
    }
    return '';
  }

  /**
   * Deletes an inventory item.
   *
   * @param int $item_id
   *   The ID of the item to delete.
   */
  public function deleteItem($item_id) {
    // Check if the item exists.
    $item_exists = $this->database->select('items', 'i')
      ->fields('i', ['item_id'])
      ->condition('item_id', $item_id)
      ->countQuery()
      ->execute()
      ->fetchField();

    if ($item_exists) {
        try {
            // Delete the item from the items table.
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
    
    // Redirect to the listing page after deletion.
    return $this->redirect('inventory_system.list');
  }
}
