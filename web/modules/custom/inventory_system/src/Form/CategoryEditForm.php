<?php

namespace Drupal\inventory_system\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryEditForm extends FormBase {
  protected $term;

  public function getFormId() {
    return 'category_edit_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state, $tid = NULL) {
    // Load the term entity.
    $this->term = Term::load($tid);
    if (!$this->term) {
      throw new NotFoundHttpException();
    }

    $form['category_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Category Name'),
      '#default_value' => $this->term->getName(),
      '#required' => TRUE,
    ];
    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $this->term->getDescription(),
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save Changes'),
    ];
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->term->setName($form_state->getValue('category_name'));
    $this->term->setDescription($form_state->getValue('description'));
    $this->term->save();

    \Drupal::messenger()->addMessage($this->t('The category has been updated.'));

    // Redirect to the category list page after saving changes.
    $form_state->setRedirect('inventory_system.category_list');
  }
}
