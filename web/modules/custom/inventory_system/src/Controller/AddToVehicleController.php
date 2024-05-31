<?php

namespace Drupal\inventory_system\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormBuilder;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class AddToVehicleController extends ControllerBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * Constructs a new AddToVehicleController object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The form builder.
   */
  public function __construct(Connection $database, FormBuilderInterface $form_builder) {
    $this->database = $database;
    $this->formBuilder = $form_builder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('form_builder')
    );
  }

  /**
   * Displays the form to add inventory items to a vehicle.
   */
  public function addToVehicle(Request $request) {
    $form = $this->formBuilder->getForm('Drupal\inventory_system\Form\AddToVehicleForm');

    // Fetch list of vehicles from the database.
    $vehicles = $this->getVehicleList();

    return [
      '#theme' => 'inventory_system_add_to_vehicle_page',
      '#form' => $form,
      '#vehicles' => $vehicles,
    ];
  }

  /**
   * Helper function to fetch list of vehicles.
   */
  private function getVehicleList() {
    // Query to fetch list of vehicles from the database.
    $query = $this->database->select('vehicles', 'v')
      ->fields('v', ['vid', 'name'])
      ->orderBy('name');
    $results = $query->execute()->fetchAll();

    $vehicles = [];
    foreach ($results as $vehicle) {
      $vehicles[$vehicle->vid] = $vehicle->name;
    }

    return $vehicles;
  }

}
