<?php

namespace Drupal\provus_search_ai\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Search chat full block' Block.
 *
 * @Block(
 *   id = "provus_search_ai_chat_full_block",
 *   admin_label = @Translation("Search full chat block"),
 *   category = @Translation("Provus Search AI"),
 * )
 */
class ChatFullBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'chat_full_block',
      '#wrapper_id' => 'search-chat-app-full',
    ];
  }

}
