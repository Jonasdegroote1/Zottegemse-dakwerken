<?php

namespace Drupal\inventory_system\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

class vehicleForm extends FormBase {
  public function getFormId() {
    return 'vehicle_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['vehicle_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Vehicle Name'),
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add Vehicle'),
    ];
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $vehicle_name = $form_state->getValue('vehicle_name');

    // Save the vehicle to the custom database table.
    $connection = Database::getConnection();
    $query = $connection->insert('vehicles')
      ->fields(['name'])
      ->values([$vehicle_name])
      ->execute();

    \Drupal::messenger()->addMessage($this->t('The vehicle has been added.'));

    // Redirect to the vehicle list page after saving changes.
    $form_state->setRedirect('inventory_system.vehicles_list');
  }
}