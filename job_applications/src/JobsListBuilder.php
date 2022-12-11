<?php

namespace Drupal\job_applications;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\file\Entity\File;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a list controller for the job applications entity type.
 */
class JobsListBuilder extends EntityListBuilder {

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Constructs a new JobsListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, DateFormatterInterface $date_formatter) {
    parent::__construct($entity_type, $storage);
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity_type.manager')->getStorage($entity_type->id()),
      $container->get('date.formatter'),
    );
  }


  /**
   * @return array|int
   *
   * overwrite default query to change sorting to show newest submissions firts
   */
  protected function getEntityIds() {
    $query = $this->getStorage()->getQuery()
      ->accessCheck(TRUE)
      ->sort($this->entityType->getKey('id'), 'DESC');
    // Only add the pager if a limit is specified.
    if ($this->limit) {
      $query->pager($this->limit);
    }
    return $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function render() {

    //set pager limit
    $this->limit = 10;
    // Count the total job applications
    $total = $this->getStorage()
      ->getQuery()
      ->accessCheck(FALSE)
      ->count()
      ->execute();

    $build['summary']['#markup'] = $this->t('Total job applications: @total', ['@total' => $total]);
    $build['table'] = parent::render();




    return $build;
  }

  /**
   * {@inheritdoc}
   *
   * create table header of overview
   */
  public function buildHeader() {
    $header['id'] = $this->t('ID');
    $header['created'] = $this->t('Date');
    $header['last_name'] = $this->t('Last name');
    $header['first_name'] = $this->t('First name');
    $header['job_position'] = $this->t('Job position');
    $header['motivation'] = $this->t('Motivation');
    $header['cv_file'] = $this->t('Curriculum vitae');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   *
   * adds row for each enity
   * formats data of entity
   */
  public function buildRow(EntityInterface $entity) {
    /** @var JobsInterface $entity */
    $row['id'] = $entity->toLink();
    $row['created'] = $this->dateFormatter->format($entity->get('created')->value);
    $row['last_name'] = $entity->get('last_name')->value;
    $row['first_name'] = $entity->get('first_name')->value;
    $row['job_position'] = $entity->get('job_position')->value;


    // limit motivation characters to avoid table overflowing
    $motivation = $entity->get('motivation')->value;
    if (empty($motivation)) {
      // add empty value for motivation
      $motivation = new FormattableMarkup('<i>(:empty)</i>', [':empty' => t('empty')]);
    } elseif (strlen($motivation) > 50) {
      $motivation = substr($motivation, 0, 50) . '...';
    }
    $row['motivation'] = $motivation;

    // create a link for private file
    /** @var File $file */
    $file = $entity->get('cv_file')->referencedEntities()[0];
    $row['cv_file'] = new FormattableMarkup('<a href=":link" target="_blank">@file</a>', [':link' => $file->createFileUrl(), '@file' => t('CV file')]);
    return $row + parent::buildRow($entity);
  }
}
