<?php

namespace Drupal\inventory_system\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

class InventoryEditForm extends FormBase {
  protected $node;

  public function getFormId() {
    return 'inventory_edit_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state, Node $node = NULL) {
    $this->node = $node;

    $form['item_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Item Name'),
      '#default_value' => $node->field_item_name->value,
      '#required' => TRUE,
    ];
    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $node->field_description->value,
      '#required' => TRUE,
    ];
    $form['quantity'] = [
      '#type' => 'number',
      '#title' => $this->t('Quantity'),
      '#default_value' => $node->field_quantity->value,
      '#required' => TRUE,
    ];
    $form['location'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Location'),
      '#default_value' => $node->field_location->value,
      '#required' => TRUE,
    ];
    $form['price'] = [
      '#type' => 'number',
      '#title' => $this->t('Price'),
      '#default_value' => $node->field_price->value,
      '#step' => '0.01',
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save Changes'),
    ];
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->node->set('field_item_name', $form_state->getValue('item_name'));
    $this->node->set('field_description', $form_state->getValue('description'));
    $this->node->set('field_quantity', $form_state->getValue('quantity'));
    $this->node->set('field_location', $form_state->getValue('location'));
    $this->node->set('field_price', $form_state->getValue('price'));
    $this->node->save();

    \Drupal::messenger()->addMessage($this->t('Inventory item updated successfully.'));

    // Redirect to the inventory list page.
    $form_state->setRedirect('inventory_system.list');
  }
}
