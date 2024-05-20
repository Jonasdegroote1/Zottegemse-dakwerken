<?php

namespace Drupal\accordion_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides an 'Accordion' Block.
 *
 * @Block(
 *   id = "accordion_block",
 *   admin_label = @Translation("Accordion Block"),
 *   category = @Translation("Custom")
 * )
 */
class AccordionBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'header' => 'Accordion Item',
      'content' => 'Content for the accordion item.',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $form['header'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Header'),
      '#default_value' => $config['header'],
    ];

    $form['content'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Content'),
      '#default_value' => $config['content'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->configuration['header'] = $values['header'];
    $this->configuration['content'] = $values['content'];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();

    $content = '<div class="accordion">';
    $content .= '<div class="accordion-item">';
    $content .= '<div class="accordion-header">' . $config['header'] . '</div>';
    $content .= '<div class="accordion-content">' . $config['content'] . '</div>';
    $content .= '</div>';
    $content .= '</div>';

    return [
      '#markup' => $content,
      '#attached' => [
        'library' => [
          'accordion_block/accordion',
        ],
      ],
    ];
  }

}
