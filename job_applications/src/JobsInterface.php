<?php

namespace Drupal\job_applications;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a job applications entity type.
 */
interface JobsInterface extends ContentEntityInterface, EntityChangedInterface {

}
