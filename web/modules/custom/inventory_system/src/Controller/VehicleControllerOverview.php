<?php

namespace Drupal\inventory_system\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Messenger\MessengerInterface;

class VehicleControllerOverview extends ControllerBase {
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
  * Constructs a new VehicleControllerOverview object.
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
  * Displays a list of vehicles grouped by vehicle_id and date.
  */
/**
  * Displays a list of vehicles grouped by vehicle_id and date.
  */
  public function overviewPage() {
    // Retrieve the list of vehicles.
    $grouped_vehicles = $this->vehicleList();

    // Build the table.
    $header = [
      'name' => $this->t('Vehicle name'),
      'quantity' => $this->t('Quantity'),
      'date' => $this->t('Date'),
      'details' => $this->t('Details'), // Added column for details
    ];

    $rows = [];
    foreach ($grouped_vehicles as $vehicle_id => $vehicles) {
      foreach ($vehicles as $date => $items) {
        // Construct row for each group.

        $vehicle_name = reset($items)->vehicle_name;

        $row = [
          'name' => $vehicle_name, // Using $vehicle_id for the ID
          'quantity' => 0,
          'date' => $date,
          'details' => [
            'data' => [
              '#type' => 'link',
              '#title' => $this->t('View Details'),
              '#url' => Url::fromRoute('inventory_system.vehicle_detail_page', ['vehicle_id' => $vehicle_id, 'date' =>$date]),
            ],
          ],
        ];
        // Accumulate quantities for each group.
        foreach ($items as $item) {
          $row['quantity'] += $item->quantity;
        }
        $rows[] = $row;
      }

    $add_button = [
      '#type' => 'link',
      '#title' => $this->t('Add items to vehicle'),
      '#url' => Url::fromRoute('inventory_system.add_to_vehicle'),
      '#attributes' => [
        'class' => ['button', 'button--primary'],
      ],
      ];
    }

    $inventory_button = [
      '#type' => 'link',
      '#title' => $this->t('View Inventory'),
      '#url' => Url::fromRoute('inventory_system.list'),
      '#attributes' => [
        'class' => ['button', 'button--primary'],
      ],
    ];

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['vehicle-list-container']],
      'add_button' => $add_button,
      'inventory_button' => $inventory_button,
      'table' => [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $rows,
        '#empty' => $this->t('No vehicles found.'),
      ],
    ];

  }


/**
 * Displays detailed information about a specific vehicle and date.
 */
  public function detailPage($vehicle_id, $date) {
    // Retrieve items for the specified vehicle_id and date.
    $items = $this->getItemsForVehicleAndDate($vehicle_id, $date);

    $vehicle_name = $this->getVehicleName($vehicle_id);

    // Build the table.
    $header = [
      'name' => $this->t('Item name'),
      'quantity' => $this->t('Quantity'),
    ];

    $rows = [];
    foreach ($items as $item) {
      $rows[] = [
        'name' => $item->title,
        'quantity' => $item->quantity,
      ];
    }

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['vehicle-detail-container']],
      'vehicle_name' => [
          '#markup' => '<h2>' . $this->t('Vehicle: @name', ['@name' => $vehicle_name]) . '</h2>',
        ],
      'back' => [
        '#type' => 'link',
        '#title' => $this->t('Back to list'),
        '#url' => Url::fromRoute('inventory_system.vehicle_overview'),
        '#attributes' => [
          'class' => ['button', 'button--primary'],
        ],
      ],
      'table' => [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $rows,
        '#empty' => $this->t('No items found.'),
      ],
    ];
  }

/**
 * Retrieves items for a specific vehicle_id and date.
 */

  protected function getVehicleName($vehicle_id) {
      // Query the database to fetch the vehicle name.
      $query = $this->database->select('vehicles', 'v')
          ->fields('v', ['name'])
          ->condition('v.vehicle_id', $vehicle_id);
      $vehicle_name = $query->execute()->fetchField();
      return $vehicle_name;
  }

  protected function getItemsForVehicleAndDate($vehicle_id, $date) {
    // Build the query to fetch items for the specified vehicle_id and date.
    $query = $this->database->select('item_vehicle', 'iv')
      ->fields('iv', ['item_id', 'quantity'])
      ->condition('iv.vehicle_id', $vehicle_id)
      ->condition('iv.date', $date)
      ->orderBy('iv.item_id', 'ASC');

    $query->join('items', 'i', 'iv.item_id = i.item_id');
    $query->fields('i', ['title']);

    // Execute the query and fetch results.
    $items = $query->execute()->fetchAll();

    return $items;
  }

  /**
  * Retrieves a list of vehicles grouped by vehicle_id and date.
  */
    public function vehicleList() {
      // Build the query to fetch items from the item_vehicle table joined with the vehicles table, sorted by date.
      $query = $this->database->select('item_vehicle', 'iv')
        ->fields('iv', ['item_vehicle_id', 'item_id', 'quantity', 'date', 'vehicle_id'])
        ->orderBy('iv.date', 'DESC');

      $query->join('vehicles', 'v', 'iv.vehicle_id = v.vehicle_id');
      $query->fields('v', ['name']);

      // Execute the query and fetch results.
      $results = $query->execute()->fetchAll();

      // Group results by vehicle_id and date.
      $grouped_results = [];
      foreach ($results as $result) {
        $vehicle_id = $result->vehicle_id;
        $date = $result->date;
        $result->vehicle_name = $result->name;
        if (!isset($grouped_results[$vehicle_id][$date])) {
          $grouped_results[$vehicle_id][$date] = [];
        }
        $grouped_results[$vehicle_id][$date][] = $result;
      }

    return $grouped_results;
  }
}
