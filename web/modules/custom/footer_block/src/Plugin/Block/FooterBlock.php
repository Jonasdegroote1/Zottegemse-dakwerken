<?php

namespace Drupal\footer_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Menu\MenuTreeParameters;

/**
 * Provides a 'Footer' Block.
 *
 * @Block(
 *   id = "footer_block",
 *   admin_label = @Translation("Footer Block"),
 * )
 */
class FooterBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();

    // Load the menu tree.
    $menu_name = 'footer-menu';
    $menu_tree = \Drupal::menuTree();
    $parameters = new MenuTreeParameters();
    $tree = $menu_tree->load($menu_name, $parameters);

    // Transform the tree using the default manipulators.
    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];
    $tree = $menu_tree->transform($tree, $manipulators);

    // Build the render array for the menu.
    $menu = $menu_tree->build($tree);

    return [
      '#theme' => 'footer_block',
      '#logo' => $config['footer_logo'] ? File::load($config['footer_logo'])->createFileUrl() : '',
      '#contact_info' => $config['footer_contact_info'],
      '#menu' => $menu,
      '#attached' => [
        'library' => [
          'footer_block/footer_block',
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

    $form['footer_logo'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Footer Logo'),
      '#upload_location' => 'public://footer_logos/',
      '#default_value' => isset($config['footer_logo']) ? [$config['footer_logo']] : [],
      '#description' => $this->t('Upload a logo to display in the footer.'),
      '#upload_validators' => [
        'file_validate_extensions' => ['png jpg jpeg gif'],
      ],
    ];

    $form['footer_contact_info'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Contact Information'),
      '#description' => $this->t('Enter the contact information to display in the footer.'),
      '#default_value' => $config['footer_contact_info'] ?? '',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);

    // Save the uploaded logo file.
    $logo_fid = $form_state->getValue('footer_logo');
    if (!empty($logo_fid[0])) {
      $file = File::load($logo_fid[0]);
      if ($file) {
        $file->setPermanent();
        $file->save();
        $this->setConfigurationValue('footer_logo', $file->id());
      }
    } else {
      $this->setConfigurationValue('footer_logo', '');
    }

    $this->setConfigurationValue('footer_contact_info', $form_state->getValue('footer_contact_info'));
  }
}
