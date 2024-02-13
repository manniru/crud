<?php

namespace Drupal\crud\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class StudentsForm extends FormBase {

  public function getFormId() {
    return 'students_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {


    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#required' => TRUE,
    ];

    $form['age'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Age'),
      '#required' => TRUE,
    ];

    $form['gender'] = [
      '#type' => 'select',
      '#title' => $this->t('Gender'),
      '#options' => [
        'male' => 'Male',
        'female' => 'Female',
      ]
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $title = $form_state->getValue('title');
    if (strlen($title) < 5) {
      // Set an error for the form element with a key of "title".
      // $form_state->setErrorByName('title', $this->t('The title must be at least 5 characters long.'));
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $name = $form_state->getValue('name');
    $age = $form_state->getValue('age');
    $gender = $form_state->getValue('gender');

    $fields = [
      'name' => $name,
      'age' => $age,
      'gender' => $gender,

    ];

    $db_id = \Drupal::database()->insert('_students')->fields($fields)->execute();



    $this->messenger()->addMessage("Name: $name, Age: $age, Gender: $gender, Record ID: $db_id");
  }

}
