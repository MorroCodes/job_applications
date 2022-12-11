<?php

namespace Drupal\job_applications\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\job_applications\Entity\Jobs;

/**
 * Provides a Job applications form.
 */
class ApplicationForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'job_applications_application';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last name'),
      '#required' => TRUE,
    ];

    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First name'),
      '#required' => TRUE,
    ];

    $form['job_position'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Job position'),
      '#required' => TRUE,
    ];

    $form['motivation'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Motivation'),
      '#required' => FALSE,
    ];

    // upload managed files to private storage to restrict public access
    $form['cv_file'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Curriculum vitae'),
      '#required' => TRUE,
      '#upload_validators' => [
        'file_validate_extensions' => [
          'txt doc docx pdf rtf odt'
        ]
      ],
      '#upload_location' => 'private://curriculum_vitae'
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Apply'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $result = $form_state->getValues();
    //remove HTML tags from results to avoid attempted XSS-attacks
    $this->stripTags($result);
    //create a new Job entity and add data from form fields
    $job = Jobs::create();
    $job->set('first_name', $result['first_name'])
      ->set('last_name', $result['last_name'])
      ->set('job_position', $result['job_position'])
      ->set('motivation', $result['motivation'])
      ->set('cv_file', $result['cv_file']);
    $job->save();

    // show confirmation message
    $this->messenger()->addStatus(t("Thank you for your application, @name. We'll get back to you soon!", ['@name' => $result['first_name']]));
    return $form;
  }

  /**
   * @param array $result
   * remove HTML tags from array
   */
  protected function stripTags(array &$result) {
    foreach ($result as &$value) {
      if (is_string($value)) {
        $value = strip_tags($value);
      }
    }
  }
}
