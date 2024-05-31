<?php

namespace Drupal\inventory_system\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CategoryEditForm extends FormBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a new CategoryEditForm object.
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
    return 'category_edit_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $tid = NULL) {
    if ($tid) {
      // Fetch category data from the database.
      $category = $this->database->select('categories', 'c')
        ->fields('c', ['category_id', 'title'])
        ->condition('category_id', $tid)
        ->execute()
        ->fetchAssoc();

      // Build the form with category data.
      if ($category) {
        $form['category_id'] = [
          '#type' => 'hidden',
          '#value' => $category['category_id'],
        ];
        $form['category_name'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Category Name'),
          '#required' => TRUE,
          '#default_value' => $category['title'],
        ];
        $form['submit'] = [
          '#type' => 'submit',
          '#value' => $this->t('Update Category'),
        ];
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $category_id = $form_state->getValue('category_id');
    $category_name = $form_state->getValue('category_name');

    // Update the category in the custom database table.
    $this->database->update('categories')
      ->fields([
        'title' => $category_name,
      ])
      ->condition('category_id', $category_id)
      ->execute();

    \Drupal::messenger()->addMessage($this->t('The category has been updated.'));
    $form_state->setRedirect('inventory_system.category_list');
  }
}
