<?php

namespace Drupal\printable;

use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Render\MainContent\HtmlRenderer;

/**
 * Show only the body of the page.
 */
class PrintableRenderer extends HtmlRenderer {

  /**
   * Prepares the HTML body: wraps the main content in #type 'page'.
   *
   * @param array $main_content
   *   The render array representing the main content.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object, for context.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match, for context.
   *
   * @return array
   *   An array with two values:
   *   0. A #type 'page' render array.
   *   1. The page title.
   *
   * @throws \LogicException
   *   If the selected display variant does not implement PageVariantInterface.
   */
  public function prepare(array $main_content, Request $request, RouteMatchInterface $route_match) {
    // Determine the title: use the title provided by the main content if any,
    // otherwise get it from the routing information.
    $get_title = function (array $main_content) use ($request, $route_match) {
      return isset($main_content['#title']) ? $main_content['#title'] : $this->titleResolver->getTitle($request, $route_match->getRouteObject());
    };

    // If the _controller result already is #type => page,
    // we have no work to do: The "main content" already is an entire "page"
    // (see html.html.twig).
    if (isset($main_content['#type']) && $main_content['#type'] === 'page') {
      $page = $main_content;
      $title = $get_title($page);
    }
    // Otherwise, render it as the main content of a #type => page, by selecting
    // page display variant to do that and building that page display variant.
    else {
      $title = $get_title($main_content);
      $page = [
        '#type' => 'page',
        'content' => $main_content,
      ];
    }

    $page['#attached']['library'][] = 'printable/printable';
    $page['#attributes']['class'][] = 'printable-body';

    // Allow hooks to add attachments to $page['#attached'].
    $this->invokePageAttachmentHooks($page);

    return [$page, $title];
  }

  /**
   * {@inheritdoc}
   */
  public function buildPageTopAndBottom(array &$html) {
    // Do nothing.
  }

}
