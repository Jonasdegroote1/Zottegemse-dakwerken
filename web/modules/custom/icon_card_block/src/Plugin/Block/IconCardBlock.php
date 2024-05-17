<?php

namespace Drupal\icon_card_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * Provides an Icon Card Block.
 *
 * @Block(
 *   id = "icon_card_block",
 *   admin_label = @Translation("Icon Card Block"),
 *   category = @Translation("Custom")
 * )
 */
class IconCardBlock extends BlockBase {
    /**
     * {@inheritdoc}
     */
/**
 * {@inheritdoc}
 */ 
  public function build() {
    $config = $this->getConfiguration();
    $icon_url = '';

    // Check if the 'icon' key is set and is not empty.
    if (!empty($config['icon'])) {
        $file = File::load($config['icon']);
        if ($file) {
            // Use the file URL generator service to get the URL of the file.
            $icon_url = \Drupal::service('file_url_generator')->generateString($file->getFileUri());
        }
    }

    // Pass the variables to the Twig template.
    return [
        '#theme' => 'icon_card_block',
        '#icon' => $icon_url,
        '#title' => $config['title'] ?? '',
        '#description' => $config['description'] ?? '',
        '#attached' => [
            'library' => [
                'icon_card_block/icon_card_styles',
            ],
        ],
    ];
  }



    /**
     * {@inheritdoc}
     */
    public function blockForm($form, FormStateInterface $form_state) {
        $form = parent::blockForm($form, $form_state);
        $config = $this->getConfiguration();

        $form['icon'] = [
            '#type' => 'managed_file',
            '#title' => $this->t('Icon Image'),
            '#upload_location' => 'public://icon_images/',
            '#default_value' => !empty($config['icon']) ? [$config['icon']] : [],
            '#upload_validators' => [
                'file_validate_extensions' => ['png jpg jpeg gif svg'],
            ],
            '#description' => $this->t('Upload an image file for the icon.'),
        ];

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

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state) {
        $values = $form_state->getValues();

        // Save the file permanently.
        if (!empty($values['icon'][0])) {
            $file = File::load($values['icon'][0]);
            if ($file && $file->isTemporary()) {
                $file->setPermanent();
                $file->save();  // Save the file entity changes.
                \Drupal::service('file.usage')->add($file, 'icon_card_block', 'icon_card_block', $file->id());
            }
            $this->configuration['icon'] = $file->id();
        } else {
            $this->configuration['icon'] = NULL;
        }

        $this->configuration['title'] = $values['title'];
        $this->configuration['description'] = $values['description'];
    }
}
