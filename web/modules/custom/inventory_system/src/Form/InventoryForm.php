<?php

namespace Drupal\inventory_system\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InventoryForm extends FormBase {
  
  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a new InventoryForm object.
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
    return 'inventory_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Query categories from the database
    $query = $this->database->select('categories', 'c')
      ->fields('c', ['category_id', 'title'])
      ->execute();

    $categories = $query->fetchAllKeyed();

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
    $form['category'] = [
      '#type' => 'select',
      '#title' => $this->t('Category'),
      '#options' => $categories,
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add Item'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get form values
      $item_name = $form_state->getValue('item_name');
      $description = $form_state->getValue('description');
      $quantity = $form_state->getValue('quantity');
      $location = $form_state->getValue('location');
      $price = $form_state->getValue('price');
      $category = $form_state->getValue('category');

      // Insert data into 'items' table
      $connection = \Drupal::database();
      $item_id = $connection->insert('items')
        ->fields([
          'title' => $item_name,
          'description' => $description,
          'quantity' => $quantity,
          'location' => $location,
          'price' => $price,
          'category_id' => $category,
        ])
        ->execute();

    \Drupal::messenger()->addMessage($this->t('Inventory item added successfully.'));
    // Redirect to the inventory list page.
    $form_state->setRedirect('inventory_system.list');
  }

}
