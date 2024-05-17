<?php 

//**
// * @file */

namespace Drupal\mymodule\Controller;

use Drupal\Core\Controller\ControllerBase;

class FirstController extends ControllerBase {
  public function simpleContent() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Hello, World!'),
    ];
  }

  public function variableContent($name) {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Hello, @name!', ['@name' => $name]),
    ];
  }
}