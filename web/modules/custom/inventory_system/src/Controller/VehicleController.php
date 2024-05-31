<?php

namespace Drupal\inventory_system\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Database;

class VehicleController extends ControllerBase {

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a new VehicleController object.
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
   * List all vehicles.
   */
  public function listItems() {
    // Select the vehicle names and ids from the custom database table.
    $connection = Database::getConnection();
    $query = $connection->select('vehicles', 'v')
      ->fields('v', ['name', 'vehicle_id'])
      ->orderBy('name');
    $result = $query->execute()->fetchAll();

    // Prepare table header.
    $header = [
      'vehicle_name' => $this->t('Vehicle Name'),
      'operations' => $this->t('Operations'),
    ];

    // Prepare table rows.
     $rows = [];
    foreach ($result as $record) {
      $edit_url = Url::fromRoute('inventory_system.vehicle_edit_form', ['vid' => $record->vehicle_id]);
      $edit_link = Link::fromTextAndUrl($this->t('Edit'), $edit_url);
      $delete_url = Url::fromRoute('inventory_system.vehicle_delete', ['vid' => $record->vehicle_id]);
      $delete_link = Link::fromTextAndUrl($this->t('Delete'), $delete_url);

      $rows[] = [
        'vehicle_name' => $record->name,
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

    // Build the table.
    $build = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    ];

    return $build;
  }

  /**
   * Delete a vehicle.
   * 
   * @param int $vid
   * 
   */

  public function deleteVehicle($vid) {
    // Delete the vehicle from the custom database table.
    $connection = Database::getConnection();
    $query = $connection->delete('vehicles')
      ->condition('vehicle_id', $vid)
      ->execute();

    $this->messenger->addMessage($this->t('The vehicle has been deleted.'));

    // Redirect to the vehicle list page after deleting the vehicle.
    return $this->redirect('inventory_system.vehicles_list');
  }

}