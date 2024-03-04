<?php

namespace Drupal\crud\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class StudentsForm extends FormBase {

  public function getFormId() {
    return 'students_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $id = \Drupal::request()->query->get('id');

    if ($id) {
      $student = \Drupal::database()->query("SELECT * FROM _students WHERE id=$id")->fetchObject();

      $form['table_id'] = ['#type' => 'hidden', '#value' => $student->id];

      // $this->messenger()->addMessage("student: $student->name");
    }


    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#required' => TRUE,
      '#default_value' =>  $student->name
    ];

    $form['age'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Age'),
      '#required' => TRUE,
      '#default_value' =>  $student->age
    ];

    $form['gender'] = [
      '#type' => 'select',
      '#title' => $this->t('Gender'),
      '#default_value' =>  $student->gender,
      '#options' => [
        'male' => 'Male',
        'female' => 'Female',
      ]
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    if ($id) {
      $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Update'),
      ];
    }

    else {
      // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
    }



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

    $op = $form_state->getValue('op');

    $name = $form_state->getValue('name');
    $age = $form_state->getValue('age');
    $gender = $form_state->getValue('gender');
    $table_id = $form_state->getValue('table_id');

    if ($op == 'Submit') {
      $fields = [
        'name' => $name,
        'age' => $age,
        'gender' => $gender,

      ];

      $db_id = \Drupal::database()->insert('_students')->fields($fields)->execute();

    }

    else {

      $query = \Drupal::database()->update('_students');
          $query->fields([
            'name' => $name,
            'age' => $age,
            'gender' =>$gender
          ]);
          $query->condition('id', $table_id);
          $res = $query->execute();
          $this->messenger()->addMessage("Updated");


    }



    $this->messenger()->addMessage("Name: $name, Age: $age, Gender: $gender, Record ID: $db_id");
  }

}
