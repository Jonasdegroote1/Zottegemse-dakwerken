<?php

namespace Drupal\inventory_system\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\Entity\Node;

class InventoryController extends ControllerBase {
  use StringTranslationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a new InventoryController object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, MessengerInterface $messenger) {
    $this->entityTypeManager = $entityTypeManager;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('messenger')
    );
  }

  /**
   * Displays a list of inventory items.
   */
  public function listItems() {
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'inventory_item')
      ->accessCheck(TRUE)
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
      $edit_url = Url::fromRoute('inventory_system.edit_form', ['node' => $item->id()]);
      $edit_link = Link::fromTextAndUrl($this->t('Edit'), $edit_url);
      $delete_url = Url::fromRoute('inventory_system.delete_item', ['node' => $item->id()]);
      $delete_link = Link::fromTextAndUrl($this->t('Delete'), $delete_url);
      $rows[] = [
        'item_name' => $item->field_item_name->value,
        'description' => $item->field_description->value,
        'quantity' => $item->field_quantity->value,
        'location' => $item->field_location->value,
        'price' => $item->field_price->value,
        'operations' => [
          'data' => [
            '#type' => 'operations',
            '#links' => [
              'edit' => [
                'title' => $edit_link->getText(),
                'url' => $edit_link->getUrl(),
              ],
              'delete' => [
                'title' => $delete_link->getText(),
                'url' => $delete_link->getUrl(),
              ],
            ],
          ],
        ],
      ];
    }

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

  /**
   * Deletes an inventory item.
   *
   * @param int $node
   *   The node ID of the inventory item to delete.
   */
  public function deleteItem($node) {
    $node = Node::load($node);
    if ($node) {
      $node->delete();
      $this->messenger->addMessage($this->t('Inventory item deleted successfully.'));
    }
    return $this->redirect('inventory_system.list');
  }
}
