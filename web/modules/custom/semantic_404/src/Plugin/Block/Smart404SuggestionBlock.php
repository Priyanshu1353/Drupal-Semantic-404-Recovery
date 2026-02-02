<?php

declare(strict_types=1);

namespace Drupal\semantic_404\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Path\CurrentPathStack;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\semantic_404\Service\SemanticMatcher;

/**
 * Provides the Smart 404 Suggestion block.
 *
 * @Block(
 *   id = "smart_404_suggestion",
 *   admin_label = @Translation("Smart 404 Suggestion"),
 *   category = @Translation("Semantic 404"),
 * )
 */
class Smart404SuggestionBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The semantic matcher service.
   *
   * @var \Drupal\semantic_404\Service\SemanticMatcher
   */
  protected SemanticMatcher $matcher;

  /**
   * The current path stack.
   *
   * @var \Drupal\Core\Path\CurrentPathStack
   */
  protected CurrentPathStack $currentPath;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected AccountInterface $currentUser;

  /**
   * Constructs a Smart404SuggestionBlock.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    SemanticMatcher $matcher,
    CurrentPathStack $current_path,
    EntityTypeManagerInterface $entity_type_manager,
    AccountInterface $current_user
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->matcher           = $matcher;
    $this->currentPath       = $current_path;
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser       = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('semantic_404.semantic_matcher'),
      $container->get('path.current'),
      $container->get('entity_type.manager'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $path  = $this->currentPath->getPath();
    $match = $this->matcher->match($path);

    // Only render when confidence exceeds threshold.
    if (empty($match) || $match['score'] < 0.50) {
      return [];
    }

    // --- Access control: verify the suggested node is viewable ----------
    // Attempt to resolve the matched URL to a node alias if needed.
    // For simplicity we check generic 'access content' permission here;
    // production code should resolve the alias → nid and check per-entity.
    if (!$this->currentUser->hasPermission('access content')) {
      return [];
    }

    $score_percent = (int) round($match['score'] * 100);

    return [
      '#theme'          => 'semantic_404_card',
      '#title'          => $match['title'],
      '#url'            => $match['url'],
      '#snippet'        => $match['snippet'],
      '#score'          => $score_percent,
      '#attached'       => ['library' => ['semantic_404/global-styles']],
      '#cache'          => ['contexts' => ['url.path']],
    ];
  }

  /**
   * {@inheritdoc}
   *
   * Only users who can 'access content' may see this block.
   */
  protected function blockAccess(AccountInterface $account): AccessResult {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

}
