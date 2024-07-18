<?php

namespace Drupal\provus_search_ai\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the Provus Search AI Log entity.
 *
 * @ContentEntityType(
 *   id = "provus_search_query",
 *   label = @Translation("Provus Search AI Log"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "form" = {
 *       "add" = "Drupal\Core\Entity\ContentEntityForm",
 *       "edit" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "access" = "Drupal\Core\Entity\EntityAccessControlHandler",
 *   },
 *   base_table = "provus_search_query",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/provus-search-ai-log/{provus_search_query}",
 *     "add-form" = "/admin/structure/provus-search-ai-log/add",
 *     "edit-form" = "/admin/structure/provus-search-ai-log/{provus_search_query}/edit",
 *     "delete-form" = "/admin/structure/provus-search-ai-log/{provus_search_query}/delete",
 *     "collection" = "/admin/structure/provus-search-ai-log",
 *   },
 * )
 */
class ProvusSearchAiLog extends ContentEntityBase {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Question field.
    $fields['question'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Question'))
      ->setDescription(t('The question associated with the search query.'));

    // Answer field.
    $fields['answer'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Answer'))
      ->setDescription(t('The answer to the search query.'));

    // Session ID field.
    $fields['session_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Session ID'))
      ->setDescription(t('The session ID associated with the search query.'));

    // User ID field.
    $fields['user_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('User ID'))
      ->setDescription(t('The user ID associated with the search query.'));

    // Datetime field.
    $fields['datetime'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Datetime'))
      ->setDescription(t('The date and time when the search query was made.'));

    return $fields;
  }

  // Other methods and customizations for your entity class.
}
