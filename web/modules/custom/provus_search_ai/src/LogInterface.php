<?php

namespace Drupal\provus_search_ai;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a Contact entity.
 *
 * @ingroup content_entity_example
 */
interface LogInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
