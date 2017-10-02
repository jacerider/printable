<?php

namespace Drupal\printable\Plugin\views\display;

use Drupal\views\Plugin\views\display\Page;
use Drupal\views\Plugin\views\display\ResponseDisplayPluginInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\Core\Render\HtmlResponse;

/**
 * The plugin that handles a full print.
 *
 * @ingroup views_display_plugins
 *
 * @ViewsDisplay(
 *   id = "printable",
 *   title = @Translation("Print"),
 *   help = @Translation("Display the view as print-ready, with a URL and menu links."),
 *   uses_menu_links = TRUE,
 *   uses_route = TRUE,
 *   admin = @Translation("Print"),
 *   theme = "views_view",
 *   returns_response = TRUE
 * )
 */
class Printable extends Page implements ResponseDisplayPluginInterface {

  /**
   * {@inheritdoc}
   */
  public static function buildResponse($view_id, $display_id, array $args = []) {
    $request = \Drupal::request();
    $request->setRequestFormat('drupal_printable');
    return static::buildBasicRenderable($view_id, $display_id, $args);
  }

  /**
   * {@inheritdoc}
   */
  public function preview() {
    return $this->view->render();
  }

  /**
   * {@inheritdoc}
   */
  public static function buildBasicRenderable($view_id, $display_id, array $args = []) {
    $build = [
      '#type' => 'view',
      '#name' => $view_id,
      '#display_id' => $display_id,
      '#arguments' => $args,
      '#embed' => FALSE,
      '#cache' => [
        'keys' => ['view', $view_id, 'display', $display_id],
      ],
    ];

    if ($args) {
      $build['#cache']['keys'][] = 'args';
      $build['#cache']['keys'][] = implode(',', $args);
    }

    $build['#cache_properties'] = ['#view_id', '#view_display_show_admin_links', '#view_display_plugin_id'];

    return $build;
  }

}
