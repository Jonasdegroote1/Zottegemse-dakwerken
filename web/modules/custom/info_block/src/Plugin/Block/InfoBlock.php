<?php

namespace Drupal\info_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * Provides an 'Information Block' Block.
 *
 * @Block(
 *   id = "info_block",
 *   admin_label = @Translation("Information Block"),
 *   category = @Translation("Custom")
 * )
 */
class InfoBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $image = $config['image'] ? File::load($config['image'][0]) : '';

    // Prepare the build array for rendering.
    $build = [
      '#theme' => 'info_block',
      '#title' => $config['title'] ?? '',
      '#description' => $config['description'] ?? '',
      '#image_url' => $image ? $image->createFileUrl() : '',
      '#subtitle1' => $config['subtitle1'] ?? '',
      '#subtitle2' => $config['subtitle2'] ?? '',
      '#subdescription1' => $config['subdescription1'] ?? '',
      '#subdescription2' => $config['subdescription2'] ?? '',
      '#attached' => [
        'library' => [
          'info_block/info_block_styles',
        ],
      ],
    ];

    return $build;
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
      '#description' => $this->t('Enter the title for this block.'),
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $config['description'] ?? '',
    ];

    $form['image'] = [
      '#title' => $this->t('Upload Image'),
      '#type' => 'managed_file',
      '#upload_location' => 'public://upload/info_block_images/',
      '#default_value' => $config['image'] ?? '',
      '#upload_validators' => [
        'file_validate_extensions' => ['png jpg jpeg gif'],
      ],
    ];

    $form['subtitle1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Subtitle'),
      '#default_value' => $config['subtitle1'] ?? '',
    ];

    $form['subtitle2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Second Subtitle'),
      '#default_value' => $config['subtitle2'] ?? '',
    ];

    $form['subdescription1'] = [
      '#type' => 'textarea',
      '#title' => $this->t('First Subdescription'),
      '#default_value' => $config['subdescription1'] ?? '',
    ];

    $form['subdescription2'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Second Subdescription'),
      '#default_value' => $config['subdescription2'] ?? '',
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
    $this->configuration['image'] = $values['image'];
    $this->configuration['subtitle1'] = $values['subtitle1'];
    $this->configuration['subtitle2'] = $values['subtitle2'];
    $this->configuration['subdescription1'] = $values['subdescription1'];
    $this->configuration['subdescription2'] = $values['subdescription2'];
  }
}
