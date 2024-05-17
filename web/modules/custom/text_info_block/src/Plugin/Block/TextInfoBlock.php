<?php

namespace Drupal\text_info_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Text Info' Block.
 *
 * @Block(
 *   id = "text_info_block",
 *   admin_label = @Translation("Text Info Block"),
 *   category = @Translation("Custom")
 * )
 */
class TextInfoBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#description' => $this->t('Enter the title for the block.'),
      '#default_value' => isset($config['title']) ? $config['title'] : '',
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#description' => $this->t('Enter the description text for the block.'),
      '#default_value' => isset($config['description']) ? $config['description'] : '',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    // Save the configured values.
    $this->configuration['title'] = $form_state->getValue('title');
    $this->configuration['description'] = $form_state->getValue('description');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    
    // Check if 'title' and 'description' are set in the configuration and use them.
    $title = isset($config['title']) ? $config['title'] : 'Default Title';
    $description = isset($config['description']) ? $config['description'] : 'Default Description';

    return [
      '#theme' => 'text_info_block',
      '#title' => $title,
      '#description' => $description,
    ];
  }
}
