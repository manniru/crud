<?php

namespace Drupal\crud\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

class StudentListForm extends FormBase {

  public function getFormId() {
    return 'students_list_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {


    $header = [
      'id' => 'ID',
      'name' => 'Name',
      'age' => 'Age',
      'gender' => 'Gender'

    ];

    $rows = [];


    $query = \Drupal::database()->select('_students', 'tb');
    $query->fields('tb');
    // if ($id == 'generated') {
    //   $query->isNotNull('cp.rrr');
    // }
    // if ($id == 'approved') {
    //   $query->isNotNull('cp.rrrstatus');
    // }

    // $cloned_query = clone $query;
    // $cloned_query = $cloned_query->condition('status', '1');
    // $cloned_entries = $cloned_query->execute()->fetchAll(\PDO::FETCH_ASSOC);
    // $counts = count($cloned_entries);
    // $total = array_sum(array_column($cloned_entries, 'totalAmount'));

    $query->orderBy('tb.id', 'ASC');

    $pager = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(20);
    $results = $pager->execute()->fetchAll();

    foreach ($results as $row) {

            // print('<pre>' . print_r($row, TRUE) . '</pre>'); exit();

      $edit = Link::fromTextAndUrl('Edit', new Url('crud.student', ['id' => $row->id], ['attributes' => ['class' => ['button primary']]]));


      $rows[] = [
        'id' => $row->id,
        'name' => $row->name,
        'age' => $row->age,
        'gender' => $row->gender,
        'edit' => $edit,
      ];

    }


    $form['table'] = array(
      '#type' => 'table',
      '#caption' => $this->t('Student Table'),
      '#header' => $header,
      '#rows' => $rows,
    );

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
