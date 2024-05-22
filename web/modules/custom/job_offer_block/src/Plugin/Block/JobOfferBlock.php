<?php

namespace Drupal\job_offer_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Job Offer' Block.
 *
 * @Block(
 *   id = "job_offer_block",
 *   admin_label = @Translation("Job Offer Block"),
 * )
 */
class JobOfferBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Retrieve configuration settings.
    $config = $this->getConfiguration();

    return [
      '#theme' => 'job_offer_block',
      '#title' => $config['job_title'] ?? '',
      '#location' => $config['job_location'] ?? '',
      '#description' => $config['job_description'] ?? '',
      '#email' => $config['job_email'] ?? '',
      '#attached' => [
        'library' => [
          'job_offer_block/job_offer_block',
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

    $form['job_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Job Title'),
      '#default_value' => $config['job_title'] ?? '',
    ];
    $form['job_location'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Job Location'),
      '#default_value' => $config['job_location'] ?? '',
    ];
    $form['job_description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Job Description'),
      '#default_value' => $config['job_description'] ?? '',
    ];
    $form['job_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email Address'),
      '#default_value' => $config['job_email'] ?? '',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $this->setConfigurationValue('job_title', $form_state->getValue('job_title'));
    $this->setConfigurationValue('job_location', $form_state->getValue('job_location'));
    $this->setConfigurationValue('job_description', $form_state->getValue('job_description'));
    $this->setConfigurationValue('job_email', $form_state->getValue('job_email'));
  }
}
