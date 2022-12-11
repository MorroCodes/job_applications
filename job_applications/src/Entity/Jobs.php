<?php

namespace Drupal\job_applications\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\job_applications\JobsInterface;

/**
 * Defines the job applications entity class.
 *
 * @ContentEntityType(
 *   id = "jobs",
 *   label = @Translation("Job application"),
 *   label_collection = @Translation("Job applications"),
 *   label_singular = @Translation("job application"),
 *   label_plural = @Translation("job applications"),
 *   label_count = @PluralTranslation(
 *     singular = "@count job application",
 *     plural = "@count job applications",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\job_applications\JobsListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "jobs",
 *   admin_permission = "view job applications",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "id",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "collection" = "/job/overview",
 *     "canonical" = "/job/{jobs}",
 *     "delete-form" = "/job/{jobs}/delete"
 *   },
 * )
 */
class Jobs extends ContentEntityBase implements JobsInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the job application was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the testjob was last edited.'));

    $fields['first_name'] = BaseFieldDefinition::create('text')
      ->setLabel(t('First name'));
    $fields['last_name'] = BaseFieldDefinition::create('text')
      ->setLabel(t('Last name'));
    $fields['job_position'] = BaseFieldDefinition::create('text')
      ->setLabel(t('Job position'));
    $fields['motivation'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Motivation'));
    $fields['cv_file'] = BaseFieldDefinition::create('file')
      ->setLabel(t('Curriculum vitae'))
      ->setSettings([
        'uri_scheme' => 'private',
        'file_directory' => 'curriculum_vitae',
        'file_extensions' => 'txt doc docx pdf rtf odt',
      ]);
    return $fields;
  }

}
