<?php

namespace Drupal\inventory_system\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

class CategoryForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'inventory_system_category_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Category Name'),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $node = Node::create([
      'type' => 'category',
      'title' => $form_state->getValue('name'),
    ]);
    $node->save();
    \Drupal::messenger()->addMessage($this->t('Category saved.'));
  }
}