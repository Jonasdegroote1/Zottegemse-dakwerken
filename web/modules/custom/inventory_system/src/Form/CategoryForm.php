<?php

namespace Drupal\inventory_system\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

class CategoryForm extends FormBase {
  public function getFormId() {
    return 'category_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['category_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Category Name'),
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add Category'),
    ];
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $category_name = $form_state->getValue('category_name');

    // Save the category to the custom database table.
    $connection = Database::getConnection();
    $query = $connection->insert('categories')
      ->fields(['title'])
      ->values([$category_name])
      ->execute();

    \Drupal::messenger()->addMessage($this->t('The category has been added.'));

    // Redirect to the category list page after saving changes.
    $form_state->setRedirect('inventory_system.category_list');
  }
}
