<?php

namespace Drupal\inventory_system\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class InventoryForm extends FormBase {
  public function getFormId() {
    return 'inventory_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['item_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Item Name'),
      '#required' => TRUE,
    ];
    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#required' => TRUE,
    ];
    $form['quantity'] = [
      '#type' => 'number',
      '#title' => $this->t('Quantity'),
      '#required' => TRUE,
    ];
    $form['location'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Location'),
      '#required' => TRUE,
    ];
    $form['price'] = [
      '#type' => 'number',
      '#title' => $this->t('Price'),
      '#step' => '0.01',
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add Item'),
    ];
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get form values
    $item_name = $form_state->getValue('item_name');
    $description = $form_state->getValue('description');
    $quantity = $form_state->getValue('quantity');
    $location = $form_state->getValue('location');
    $price = $form_state->getValue('price');

    // Insert data into custom database table
    $connection = \Drupal::database();
    $connection->insert('items')
      ->fields([
        'title' => $item_name,
        'description' => $description,
        'quantity' => $quantity,
        'location' => $location,
        'price' => $price,
      ])
      ->execute();

    \Drupal::messenger()->addMessage($this->t('Inventory item added successfully.'));

    // Redirect to the inventory list page.
    $form_state->setRedirect('inventory_system.list');
  }
}
