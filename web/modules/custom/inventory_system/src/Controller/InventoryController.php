<?php

namespace Drupal\inventory_system\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;

class InventoryController extends ControllerBase {
  public function listItems() {
    // Explicitly set access check.
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'inventory_item')
      ->accessCheck(TRUE)  // Explicitly set the access check.
      ->execute();
    $items = Node::loadMultiple($query);

    $header = [
      'item_name' => $this->t('Item Name'),
      'description' => $this->t('Description'),
      'quantity' => $this->t('Quantity'),
      'location' => $this->t('Location'),
      'price' => $this->t('Price'),
      'operations' => $this->t('Operations'),
    ];
    $rows = [];
    foreach ($items as $item) {
      $delete_url = Url::fromRoute('inventory_system.delete_item', ['node' => $item->id()]);
      $delete_link = Link::fromTextAndUrl($this->t('Delete'), $delete_url)->toRenderable();
      $rows[] = [
        'item_name' => $item->field_item_name->value,
        'description' => $item->field_description->value,
        'quantity' => $item->field_quantity->value,
        'location' => $item->field_location->value,
        'price' => $item->field_price->value,
        'operations' => [
          'data' => $delete_link,
        ],
      ];
    }

    // Add a button to add a new inventory item.
    $add_button = [
      '#type' => 'link',
      '#title' => $this->t('Add Inventory Item'),
      '#url' => Url::fromRoute('inventory_system.add_form'),
      '#attributes' => [
        'class' => ['button', 'button--primary'],
      ],
    ];

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['inventory-list-container']],
      'add_button' => $add_button,
      'table' => [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $rows,
        '#empty' => $this->t('No inventory items found.'),
      ],
    ];
  }

  public function deleteItem($node) {
    $node = Node::load($node);
    if ($node) {
      $node->delete();
      \Drupal::messenger()->addMessage($this->t('Inventory item deleted successfully.'));
    }
    return $this->redirect('inventory_system.list');
  }
}
