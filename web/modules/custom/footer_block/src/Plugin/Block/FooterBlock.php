<?php

namespace Drupal\footer_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;

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
    return [
      '#theme' => 'footer_block',
    ];
  }

}
