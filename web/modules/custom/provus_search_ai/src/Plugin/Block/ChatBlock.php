<?php

namespace Drupal\provus_search_ai\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Search chat block' Block.
 *
 * @Block(
 *   id = "provus_search_ai_chat_block",
 *   admin_label = @Translation("Search chat block"),
 *   category = @Translation("Provus Search AI"),
 * )
 */
class ChatBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'chat_block',
      '#wrapper_id' => 'search-chat-app-floating',
    ];
  }

}
