(function($){
  'use strict';
  $(function(){
    var $wrapper = $('.offcanvas_menu_wrapper');
    var $overlay = $('.off_canvars_overlay');
    var $button = $('.canvas_open');

    function openMenu(){
      $wrapper.addClass('active');
      $overlay.addClass('active');
      $button.addClass('is-open');
      $('body').addClass('mobile-menu-open');
    }
    function closeMenu(){
      $wrapper.removeClass('active');
      $overlay.removeClass('active');
      $button.removeClass('is-open');
      $('body').removeClass('mobile-menu-open');
    }

    $button.off('click.shivaMenu').on('click.shivaMenu', function(e){
      e.preventDefault();
      openMenu();
    });
    $('.canvas_close, .off_canvars_overlay').off('click.shivaMenu').on('click.shivaMenu', function(e){
      e.preventDefault();
      closeMenu();
    });

    // These are real page links. Do not prevent their default navigation.
    $('.offcanvas_main_menu').off('click.shivaMenu', 'a').on('click.shivaMenu', 'a', function(){
      closeMenu();
    });

    $(document).off('keydown.shivaMenu').on('keydown.shivaMenu', function(e){
      if(e.key === 'Escape') closeMenu();
    });
  });
})(jQuery);
