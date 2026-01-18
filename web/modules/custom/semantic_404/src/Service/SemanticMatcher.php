<?php

declare(strict_types=1);

namespace Drupal\semantic_404\Service;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Drupal\Core\Config\ConfigFactoryInterface;
use Psr\Log\LoggerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

/**
 * Calls the FastAPI AI engine to find the best-matching page for a 404 path.
 */
class SemanticMatcher {

  /**
   * Default AI engine base URL (overridable via config).
   */
  private const DEFAULT_AI_URL = 'http://127.0.0.1:8000';

  /**
   * HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected ClientInterface $httpClient;

  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * Logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected LoggerInterface $logger;

  /**
   * Constructs a SemanticMatcher.
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The HTTP client service (Guzzle).
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger channel factory.
   */
  public function __construct(
    ClientInterface $http_client,
    ConfigFactoryInterface $config_factory,
    LoggerChannelFactoryInterface $logger_factory
  ) {
    $this->httpClient    = $http_client;
    $this->configFactory = $config_factory;
    $this->logger        = $logger_factory->get('semantic_404');
  }

  /**
   * Returns the AI engine base URL from config or falls back to the default.
   */
  private function getAiBaseUrl(): string {
    $config = $this->configFactory->get('semantic_404.settings');
    return rtrim((string) ($config->get('ai_engine_url') ?? self::DEFAULT_AI_URL), '/');
  }

  /**
   * Queries the AI engine for the best matching page.
   *
   * @param string $path
   *   The broken URL path (e.g. "/investmentt-tips").
   *
   * @return array|null
   *   Associative array with keys: title, url, snippet, score.
   *   Returns NULL on failure or no usable response.
   */
  public function match(string $path): ?array {
    $base = $this->getAiBaseUrl();
    $endpoint = $base . '/match';

    try {
      $response = $this->httpClient->request('GET', $endpoint, [
        'query'   => ['path' => $path],
        'timeout' => 3,
      ]);

      $body = (string) $response->getBody();
      $data = json_decode($body, TRUE, 512, JSON_THROW_ON_ERROR);

      if (
        isset($data['title'], $data['url'], $data['snippet'], $data['score'])
        && is_float($data['score'] + 0)
      ) {
        return $data;
      }

      $this->logger->warning('AI engine returned unexpected payload for path @path: @body', [
        '@path' => $path,
        '@body' => $body,
      ]);
      return NULL;
    }
    catch (RequestException $e) {
      $this->logger->error('AI engine request failed for path @path: @msg', [
        '@path' => $path,
        '@msg'  => $e->getMessage(),
      ]);
      return NULL;
    }
    catch (\JsonException $e) {
      $this->logger->error('AI engine JSON parse error for path @path: @msg', [
        '@path' => $path,
        '@msg'  => $e->getMessage(),
      ]);
      return NULL;
    }
  }

}
