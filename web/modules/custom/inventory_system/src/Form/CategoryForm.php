<?php

namespace Drupal\inventory_system\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
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
    $description = $form_state->getValue('description');

    // Save the category as a taxonomy term
    $term = Term::create([
      'name' => $category_name,
      'vid' => 'category',
      'description' => $description,
    ]);
    $term->save();

    \Drupal::messenger()->addMessage($this->t('The category has been added.'));

    // Redirect to the category list page after saving changes.
    $form_state->setRedirect('inventory_system.category_list');
  }
}
