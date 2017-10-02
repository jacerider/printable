/**
 * @file
 * Printable Core.
 */
(function ($, window, document) {

  'use strict';

  Drupal.behaviors.printable = {

    attach: function (context, settings) {
      var $context = $(context);
      if ($context.find('.printable-body').once().length) {
        window.print();
      }

      $context.find('.printable-trigger').once().on('click', function (e) {
        e.preventDefault();
        var $body = $('body');
        var html = $body.html();
        var $content = $('.printable-content');
        var $close = $('<a class="printable-close">Exit Print Mode</a>').on('click', function(e) {
          e.preventDefault();
          $body.removeClass('printable-body').empty().html(html);
          Drupal.attachBehaviors($body[0], settings);
        });

        Drupal.detachBehaviors($body[0], settings);
        $body.empty();
        $content.each(function (e) {
          var $this = $(this);
          var $item = $('<div>').html($this.html());
          $item.attr('class', $this.attr('class'));
          $body.append($item);
        });
        $body.addClass('printable-body');

        $body.prepend($close);
        // Scroll to top of page.
        window.scrollTo(0, 0);

        setTimeout(function () {
          window.print();
        }, 100);
      });
    },
  };

}(jQuery, window, document));
