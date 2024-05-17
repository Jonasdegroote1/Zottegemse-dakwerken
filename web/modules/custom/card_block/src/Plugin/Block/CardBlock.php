<?php

namespace Drupal\card_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Url;

/**
 * Provides a 'Card' Block.
 *
 * @Block(
 *   id = "card_block",
 *   admin_label = @Translation("Card Block"),
 *   category = @Translation("Custom"),
 * )
 */
class CardBlock extends BlockBase {
  
  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $image_url = !empty($config['image']) ? File::load($config['image'])->createFileUrl() : '';

    // If button_url is numeric, assume it's a node ID and get the URL.
    $button_url = is_numeric($config['button_url']) ? Url::fromRoute('entity.node.canonical', ['node' => $config['button_url']])->toString() : $config['button_url'];

    return [
        '#theme' => 'card_block',
        '#title' => $config['title'] ?? '',
        '#description' => $config['description'] ?? '',
        '#image_url' => $image_url,
        '#button_text' => $config['button_text'] ?? '',
        '#button_url' => $button_url,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();

    $form['title'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Title'),
        '#default_value' => $config['title'] ?? '',
    ];

    $form['description'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Description'),
        '#default_value' => $config['description'] ?? '',
    ];

    $form['image'] = [
        '#title' => $this->t('Image'),
        '#type' => 'managed_file',
        '#upload_location' => 'public://card_block_images/',
        '#default_value' => !empty($config['image']) ? [$config['image']] : '',
        '#upload_validators' => [
            'file_validate_extensions' => ['png jpg jpeg gif'],
        ],
    ];

    $form['button_text'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Button Text'),
        '#default_value' => $config['button_text'] ?? '',
    ];

    $form['button_url'] = [
        '#type' => 'entity_autocomplete',
        '#title' => $this->t('Button URL'),
        '#target_type' => 'node',
        '#default_value' => $config['button_url'] ? \Drupal::entityTypeManager()->getStorage('node')->load($config['button_url']) : NULL,
        '#description' => $this->t('Start typing the title of a node to select it.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->configuration['title'] = $values['title'];
    $this->configuration['description'] = $values['description'];
    $this->configuration['button_text'] = $values['button_text'];
    $this->configuration['button_url'] = $values['button_url'];

    $image = $form_state->getValue('image', []);
    if (!empty($image[0])) {
        $file = File::load($image[0]);
        if ($file) {
            $file->setPermanent();
            $file->save();
            $this->configuration['image'] = $file->id();
        }
    }
  }
}
