<?php

/**
 * @file
 * Local development settings for Drupal.
 *
 * This file is excluded from version control.
 * Copy from settings.local.php.example and adjust as needed.
 */

// ── Database: SQLite (zero-config for local dev) ─────────────────────────
$databases['default']['default'] = [
  'driver'   => 'sqlite',
  'database' => __DIR__ . '/files/.drupal.sqlite',
  'prefix'   => '',
  'namespace' => 'Drupal\\sqlite\\Driver\\Database\\sqlite',
  'autoload'  => 'core/modules/sqlite/src/Driver/Database/sqlite/',
];

// ── Semantic 404: AI Engine URL ────────────────────────────────────────────
// Points to the local FastAPI service. Change to http://ai_engine:8000
// when running inside Docker Compose.
$config['semantic_404.settings']['ai_engine_url'] = 'http://127.0.0.1:8000';

// ── Performance / Caching (dev mode) ──────────────────────────────────────
$settings['cache']['bins']['render']       = 'cache.backend.null';
$settings['cache']['bins']['page']         = 'cache.backend.null';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';

$config['system.performance']['css']['preprocess']  = FALSE;
$config['system.performance']['js']['preprocess']   = FALSE;

// ── Error display ─────────────────────────────────────────────────────────
$config['system.logging']['error_level'] = 'verbose';

// ── Trusted host patterns ─────────────────────────────────────────────────
$settings['trusted_host_patterns'] = [
  '^localhost$',
  '^127\.0\.0\.1$',
];
