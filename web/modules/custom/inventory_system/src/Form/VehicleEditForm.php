<?php

namespace Drupal\inventory_system\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

class VehicleEditForm extends FormBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a new VehicleEditForm object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'vehicle_edit_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $vid = NULL) {
    if ($vid) {
      // Fetch vehicle data from the database.
      $vehicle = $this->database->select('vehicles', 'v')
        ->fields('v', ['vehicle_id', 'name'])
        ->condition('vehicle_id', $vid)
        ->execute()
        ->fetchAssoc();

      // Build the form with vehicle data.
      if ($vehicle) {
        $form['vehicle_id'] = [
          '#type' => 'hidden',
          '#value' => $vehicle['vehicle_id'],
        ];
        $form['vehicle_name'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Vehicle Name'),
          '#required' => TRUE,
          '#default_value' => $vehicle['name'],
        ];
        $form['submit'] = [
          '#type' => 'submit',
          '#value' => $this->t('Update Vehicle'),
        ];
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $vehicle_id = $form_state->getValue('vehicle_id');
    $vehicle_name = $form_state->getValue('vehicle_name');

    // Update the vehicle in the custom database table.
    $query = $this->database->update('vehicles')
      ->fields(['name' => $vehicle_name])
      ->condition('vehicle_id', $vehicle_id)
      ->execute();

    \Drupal::messenger()->addMessage($this->t('The vehicle has been updated.'));
    $form_state->setRedirect('inventory_system.vehicles_list');
  }
}