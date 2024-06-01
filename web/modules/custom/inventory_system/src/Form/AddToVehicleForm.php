<?php

namespace Drupal\inventory_system\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Messenger\MessengerInterface;


/**
 * Form class for adding inventory items to a vehicle.
 */
class AddToVehicleForm extends FormBase {

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
   * Constructs an AddToVehicleForm object.
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
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'inventory_system_add_to_vehicle_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['vehicle'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Vehicle'),
      '#options' => $this->getVehicleList(),
      '#required' => TRUE,
    ];

    $form['date'] = [
      '#type' => 'date',
      '#title' => $this->t('Date'),
      '#required' => TRUE,
    ];

    $form['item_search'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Search Inventory Items'),
      '#placeholder' => $this->t('Enter a keyword to search for inventory items'),
      '#attributes' => ['id' => 'edit-item-search'],
    ];

    $form['items'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Item'),
        $this->t('Available Quantity'),
        $this->t('Quantity'),
      ],
      '#empty' => $this->t('No inventory items available.'),
    ];

    // Get inventory items and populate the table.
    $inventory_items = $this->getInventoryItems();
    foreach ($inventory_items as $item) {
      $form['items'][$item->item_id]['checkbox'] = [
        '#type' => 'checkbox',
        '#title' => $item->title,
        '#default_value' => FALSE,
      ];

      $form['items'][$item->item_id]['available_quantity'] = [
        '#type' => 'item',
        '#markup' => $this->getAvailableQuantity($item->item_id),
      ];

      $form['items'][$item->item_id]['quantity'] = [
        '#type' => 'number',
        '#default_value' => 0,
        '#min' => 0,
      ];
    }

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add to Vehicle'),
    ];

    $form['#attached']['library'][] = 'inventory_system/inventory_form';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Validation logic...
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Submission logic...
  }

  /**
   * Helper function to retrieve inventory items.
   */
  public function getInventoryItems($search_term = '') {
    $query = $this->database->select('items', 'i')
      ->fields('i', ['item_id', 'title'])
      ->condition('title', '%' . $search_term . '%', 'LIKE')
      ->execute()
      ->fetchAll();

    return $query;
  }

  /**
   * Helper function to retrieve available quantity for an inventory item.
   */
  public function getAvailableQuantity($item_id) {
    // Query to fetch available quantity for an item.
    $query = $this->database->select('items', 'i')
      ->fields('i', ['quantity'])
      ->condition('item_id', $item_id)
      ->execute()
      ->fetchField();

    return $query;
  }

  /**
   * Helper function to retrieve vehicle list.
   */
  public function getVehicleList() {
    $query = $this->database->select('vehicles', 'v')
      ->fields('v', ['vehicle_id', 'name'])
      ->execute();

    $vehicles = [];
    foreach ($query as $row) {
      $vehicles[$row->vehicle_id] = $row->name;
    }

    return $vehicles;
  }
}
