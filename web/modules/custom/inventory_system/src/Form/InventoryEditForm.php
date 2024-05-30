<?php

namespace Drupal\inventory_system\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InventoryEditForm extends FormBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a new InventoryEditForm object.
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
    return 'inventory_edit_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $item_id = NULL) {
    if ($item_id) {
      // Fetch item data from the database.
      $item = $this->database->select('items', 'i')
        ->fields('i', ['item_id', 'title', 'description', 'quantity', 'location', 'price', 'category_id'])
        ->condition('item_id', $item_id)
        ->execute()
        ->fetchAssoc();

      // Build the form with item data.
      if ($item) {
        $form['item_id'] = [
          '#type' => 'hidden',
          '#value' => $item_id,
        ];

        $form['item_name'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Item Name'),
          '#default_value' => $item['title'],
          '#required' => TRUE,
        ];

        $form['description'] = [
          '#type' => 'textarea',
          '#title' => $this->t('Description'),
          '#default_value' => $item['description'],
          '#required' => TRUE,
        ];

        $form['quantity'] = [
          '#type' => 'number',
          '#title' => $this->t('Quantity'),
          '#default_value' => $item['quantity'],
          '#required' => TRUE,
        ];

        $form['location'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Location'),
          '#default_value' => $item['location'],
          '#required' => TRUE,
        ];

        $form['price'] = [
          '#type' => 'number',
          '#title' => $this->t('Price'),
          '#default_value' => $item['price'],
          '#step' => '0.01',
          '#required' => TRUE,
        ];

        // Query categories from the database
        $query = $this->database->select('categories', 'c')
          ->fields('c', ['category_id', 'title'])
          ->execute();
        $categories = $query->fetchAllKeyed();

        $form['category'] = [
          '#type' => 'select',
          '#title' => $this->t('Category'),
          '#options' => $categories,
          '#default_value' => $item['category_id'],
          '#required' => TRUE,
        ];

        $form['submit'] = [
          '#type' => 'submit',
          '#value' => $this->t('Save Changes'),
        ];
      }
      else {
        // Item not found, display error message.
        $form['error'] = [
          '#markup' => $this->t('Item not found.'),
        ];
      }
    }
    else {
      // No item ID provided, display error message.
      $form['error'] = [
        '#markup' => $this->t('No item ID provided.'),
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get the item ID from the form state.
    $item_id = $form_state->getValue('item_id');

    // Update the item data in the database.
    $this->database->update('items')
      ->fields([
        'title' => $form_state->getValue('item_name'),
        'description' => $form_state->getValue('description'),
        'quantity' => $form_state->getValue('quantity'),
        'location' => $form_state->getValue('location'),
        'price' => $form_state->getValue('price'),
        'category_id' => $form_state->getValue('category'),
      ])
      ->condition('item_id', $item_id)
      ->execute();

    // Optionally, you can add a message to indicate successful update.
    \Drupal::messenger()->addMessage($this->t('Item has been updated successfully.'));

    // Redirect the user to a page or URL after saving the changes.
    $form_state->setRedirect('inventory_system.list');
  }

}

