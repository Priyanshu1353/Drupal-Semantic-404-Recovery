<?php

namespace Drupal\semantic_404\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\semantic_404\Service\SemanticMatcher;

/**
 * Provides an AI Semantic Search block.
 *
 * @Block(
 *   id = "ai_search_block",
 *   admin_label = @Translation("AI Semantic Search"),
 *   category = @Translation("Semantic 404"),
 * )
 */
class AiSearchBlock extends BlockBase implements ContainerFactoryPluginInterface {

  protected SemanticMatcher $matcher;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, SemanticMatcher $matcher) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->matcher = $matcher;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('semantic_404.semantic_matcher')
    );
  }

  public function build(): array {
    $request = \Drupal::request();
    $query = $request->query->get('ai_query', '');

    $build = [
      '#attached' => ['library' => ['semantic_404/global-styles']],
      '#cache' => ['max-age' => 0], // Do not cache so search results update dynamically!
    ];

    $build['form'] = [
      '#type' => 'html_tag',
      '#tag' => 'form',
      '#attributes' => ['method' => 'GET', 'style' => 'margin-bottom: 2rem;'],
      'input' => [
        '#type' => 'html_tag',
        '#tag' => 'input',
        '#attributes' => [
          'type' => 'text',
          'name' => 'ai_query',
          'value' => htmlspecialchars($query),
          'placeholder' => 'Ask AI (e.g. investment tips)',
          'style' => 'padding: 12px; width: 70%; max-width: 400px; border-radius: 6px; border: 1px solid #ccc; font-size: 16px;',
        ],
      ],
      'submit' => [
        '#type' => 'html_tag',
        '#tag' => 'input',
        '#attributes' => [
          'type' => 'submit',
          'value' => 'Search',
          'style' => 'padding: 12px 24px; margin-left: 10px; background: #007bff; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; transition: background 0.3s;',
          'onmouseover' => 'this.style.background="#0056b3"',
          'onmouseout' => 'this.style.background="#007bff"',
        ],
      ],
    ];

    if (!empty($query)) {
      $match = $this->matcher->match($query);
      if (!empty($match) && $match['score'] > 0.1) {
        $score_percent = (int) round($match['score'] * 100);
        $build['result_wrapper'] = [
          '#type' => 'container',
          '#attributes' => ['style' => 'margin-top: 20px; padding: 10px; border-top: 2px solid #eee;'],
          'title' => [
             '#markup' => '<h3 style="margin-bottom: 15px; color: #333;">AI Top Result:</h3>',
          ],
          'card' => [
            '#theme' => 'semantic_404_card',
            '#title' => $match['title'],
            '#url' => $match['url'],
            '#snippet' => $match['snippet'],
            '#score' => $score_percent,
          ]
        ];
      } else {
        $build['result_empty'] = [
          '#markup' => '<p style="margin-top:20px; padding: 10px; color: #d9534f; background: #fdf7f7; border-left: 4px solid #d9534f;">No matches found by AI Engine.</p>',
        ];
      }
    }

    return $build;
  }

}
