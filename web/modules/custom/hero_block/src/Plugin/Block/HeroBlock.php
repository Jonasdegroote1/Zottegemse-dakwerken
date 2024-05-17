<?php

namespace Drupal\hero_block\Plugin\Block;

use Drupal\Core\Url;


use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * Provides a 'Hero Block' Block.
 *
 * @Block(
 *   id = "hero_block",
 *   admin_label = @Translation("Hero Block"),
 *   category = @Translation("Custom")
 * )
 */
class HeroBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */

public function build() {
    $config = $this->getConfiguration();
    $is_front_page = \Drupal::service('path.matcher')->isFrontPage();
    $route_match = \Drupal::routeMatch();
    $node = $route_match->getParameter('node');

    $title = $config['title'] ?? '';
    $description = $config['description'] ?? '';
    $site_name = \Drupal::config('system.site')->get('name');

    if ($is_front_page) {
        $title = "Welcome to " . $site_name;
    } else if ($node instanceof \Drupal\node\NodeInterface) {
        $title = $node->getTitle();
    }

    $image_url = '';
    if (!empty($config['image'])) {
        $file = \Drupal\file\Entity\File::load($config['image'][0]);
        if ($file) {
            $uri = $file->getFileUri();
            $image_url = \Drupal::service('file_url_generator')->generateAbsoluteString($uri);
        }
    }

    $build = [
        '#theme' => 'hero_block',
        '#title' => $title,
        '#description' => $description,
        '#image_url' => $image_url,
    ];

    // Add cache contexts
    $build['#cache'] = [
        'contexts' => [
            'url.path',
            'route.name', // You might also want to consider this if you have other logic depending on the route
        ],
    ];

    return $build;
}

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $config['title'] ?? '',
      '#maxlength' => 255,
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $config['description'] ?? '',
    ];

    $form['image'] = [
      '#title' => $this->t('Image'),
      '#type' => 'managed_file',
      '#default_value' => $config['image'] ?? '',
      '#upload_location' => 'public://hero_images/',
      '#description' => $this->t('Upload a hero image file'),
      '#upload_validators' => [
        'file_validate_extensions' => ['png jpg jpeg'],
      ],
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
/**
 * {@inheritdoc}
 */
public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['title'] = $form_state->getValue('title');
    $this->configuration['description'] = $form_state->getValue('description');
    
    // Get the current image if there's one already stored
    $current_fid = isset($this->configuration['image'][0]) ? $this->configuration['image'][0] : NULL;
    $new_fid = $form_state->getValue(['image', 0]);

    // If new file is uploaded, handle file usage
    if ($new_fid && $new_fid !== $current_fid) {
        // Load the file object
        $file = File::load($new_fid);
        if ($file) {
            // Make the file usage permanent
            $file->setPermanent();
            $file->save();
            
            // Add file usage, we use the 'hero_block' as the module and $this->getPluginId() for uniqueness
            \Drupal::service('file.usage')->add($file, 'hero_block', 'block', $this->getPluginId());
        }

        // If there was an old file, decrement its usage count
        if ($current_fid) {
            $old_file = File::load($current_fid);
            if ($old_file) {
                \Drupal::service('file.usage')->delete($old_file, 'hero_block', 'block', $this->getPluginId());
            }
        }

        // Store the new file ID in configuration
        $this->configuration['image'] = [$new_fid];
    } elseif (!$new_fid) {
        // If no new file and the field was cleared, remove usage of the old file
        if ($current_fid) {
            $old_file = File::load($current_fid);
            if ($old_file) {
                \Drupal::service('file.usage')->delete($old_file, 'hero_block', 'block', $this->getPluginId());
                $this->configuration['image'] = [];
            }
        }
    } else {
        // No change in the file, so do nothing special.
        $this->configuration['image'] = $form_state->getValue('image');
    }
}

}
