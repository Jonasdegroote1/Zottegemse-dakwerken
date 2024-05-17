<?php 

//** @file */

namespace Drupal\rsvplist\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class RSVPForm extends FormBase{
  //**{@inheritdoc}*/

  public function getFormId(){
    return 'rsvplist_email_form';
  }

  //**{@inheritdoc}*/
  public function buildForm(array $form, FormStateInterface $form_state){
    $node = \Drupal::routeMatch()->getParameter('node');
      
    if(!(is_null($node))){
      $nid = $node->id();
    }else{
      $nid = 0;
    }

    $form['email'] = [
      '#title' => $this->t('Email'),
      '#type' => 'textfield',
      '#size' => 25,
      '#description' => $this->t('We will send updates to this address'),
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('RSVP'),
    ];
    $form['nid'] = [
      '#type' => 'hidden',
      '#value' => $nid,
    ];
    return $form;
  }

  //**{@inheritdoc}*/
  public function validateForm(array &$form, FormStateInterface $form_state){
    $value = $form_state->getValue('email');
    if(strpos($value['email'], '@') === FALSE){
      $form_state->setErrorByName('email', $this->t('This is not a valid email address'));
    }
  }

  //**{@inheritdoc}*/

  public function submitForm(array &$form, FormStateInterface $form_state){
    $submit_values = $form_state->getValues('email');
    $this->messenger()->addMessage($this->t('The email address %mail has been added to the RSVP list', ['%mail' => $submit_values['email']]));
  }
}