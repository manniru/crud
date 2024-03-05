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


    /*
    $faker = \Faker\Factory::create();

    $students = [];

    for ($i = 0; $i <= 10000; $i++) {
      $student = [
        'name' => $faker->name(),
        'gender' => $faker->randomElement(['Male', 'Female']),
        'age' => $faker->numberBetween(18, 30),
      ];

      $students[] = $student;
    }

    // Optionally truncate the table if you want to remove all existing data before inserting new records
    // \Drupal::database()->truncate('_students')->execute();

    foreach ($students as $record) {
        // Insert each record individually
        \Drupal::database()->insert('_students')
            ->fields(['name', 'gender', 'age'])
            ->values([
                'name' => $record['name'],
                'gender' => $record['gender'],
                'age' => $record['age'],
            ])
            ->execute();
    }

    print('<pre>' . print_r($students, TRUE) . '</pre>'); exit();
    */

    $search = $_SESSION['search'];

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

    // if ($search) {
    //   $query->condition('name', '%' . $search . '%', 'LIKE');
    // }

    if ($search) {
      $orGroup = $query->orConditionGroup()
      ->condition('name','%'. $search.'%', 'LIKE')
      ->condition('age','%'. $search.'%', 'LIKE')
      ->condition('gender','%'. $search.'%', 'LIKE')
  ;
    $query->condition($orGroup);
    }

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


    $form['f1'] = ['#type' => 'container', '#attributes' => ['class' => ['container-inline'],],];

    $form['f1']['search'] = [
      '#type' => 'search', '#size' => 20,
      '#default_value' => $search ?? '',
      '#placeholder' => $this->t('Search enter keyword'),
    ];

    // $form['f1']['sortby'] = [
    //   '#type' => 'select',
    //   '#options' => $fields,
    //   '#default_value' => $sortby ?? '',
    // ];

    $form['f1']['actions'] = ['#type' => 'actions'];
    $form['f1']['actions']['search'] = ['#type' => 'submit', '#value' => $this->t('Search')];
    $form['f1']['actions']['reset'] = ['#type' => 'submit', '#value' => $this->t('Reset')];

    $form['table'] = array(
      '#type' => 'table',
      '#caption' => $this->t('Student Table'),
      '#header' => $header,
      '#rows' => $rows,
    );

    $form['pager'] = ['#type' => 'pager'];


    $form['actions'] = [
      '#type' => 'actions',
    ];

    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

        $form['pager'] = ['#type' => 'pager'];

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


    if ($op == 'Submit') {
      $fields = [
        'name' => $name,
        'age' => $age,
        'gender' => $gender,

      ];

      $db_id = \Drupal::database()->insert('_students')->fields($fields)->execute();



      $this->messenger()->addMessage("Name: $name, Age: $age, Gender: $gender, Record ID: $db_id");
    }

    if ($op == 'Search') {
      $search = $form_state->getValue('search');
      $_SESSION['search'] = $search;
    }

   if ($op == 'Reset') {
    unset($_SESSION['search']);
   }


  }

}
