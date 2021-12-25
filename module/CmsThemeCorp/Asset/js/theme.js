/* Sticky Navigation */
$(function() {
  
  var sticky = $('.sticky');
  var contentOffset;
  var nav_height;
  
  if (sticky.length) {
    
    if ( sticky.data('offset') ) {
      contentOffset = sticky.data('offset');
    }
    else {
      contentOffset = sticky.offset().top;
    }
    nav_height = sticky.height();
  }
  
  var scrollTop = $(window).scrollTop();
  var window_height = $(window).height();
  var doc_height = $(document).height();
  
  $(window).bind('resize', function() {
    scrollTop = $(window).scrollTop();
    window_height = $(window).height();
    doc_height = $(document).height();
    navHeight();
  });
  
  $(window).bind('scroll', function() {
    stickyNav();
  });
  
  function navHeight() {
    sticky.css('max-height', window_height + 'px');
  }
  
  function stickyNav() {
    scrollTop = $(window).scrollTop();
    if (scrollTop > contentOffset) {
      sticky.addClass('fixed');
    }
    else {
      sticky.removeClass('fixed');
    }
  }
  
});

$('document').ready(function() {
  var nav_height = 70;
  
  $("a[data-role='smoothscroll']").click(function(e) {
    e.preventDefault();
    
    var position = $($(this).attr("href")).offset().top - nav_height;
    
    $("body, html").animate({
      scrollTop: position
    }, 1000 );
    return false;
  });
});

$('document').ready(function() {
  // Back to top
  var backTop = $(".back-to-top");
  
  $(window).scroll(function() {
    if($(document).scrollTop() > 400) {
      backTop.css('visibility', 'visible');
    }
    else if($(document).scrollTop() < 400) {
      backTop.css('visibility', 'hidden');
    }
  });
  
  backTop.click(function() {
    $('html').animate({
      scrollTop: 0
    }, 1000);
    return false;
  });
});


$('document').ready(function() {
  
  // Loader
  $(window).on('load', function() {
    $('.loader-container').fadeOut();
  });
  
  // Tooltips
  $('[data-toggle="tooltip"]').tooltip();
  
  // Popovers
  $('[data-toggle="popover"]').popover();
  
  // Page scroll animate
  new WOW().init();
});

$("document").ready(function() {
  $(".hero-carousel").owlCarousel({
    items: 1,
    nav: true,
    navText: ["<span class='mai-chevron-back'></span>", "<span class='mai-chevron-forward'></span>"],
    loop: true,
    autoplay: true,
    autoplayTimeout: 5000,
  });

  $(".team-carousel").owlCarousel({
    margin: 16,
    responsive: {
      0: {
        items: 1
      },
      600: {
        items: 2
      },
      800: {
        items: 3
      }
    }
  })

  $(".testimonial-carousel").owlCarousel({
    responsive: {
      0: {
        items: 1,
        margin: 16
      },
      768: {
        items: 2,
        margin: 24
      },
      992: {
        items: 3,
        margin: 24
      }
    }
  });
});


var $grid = $('.grid');
$grid.isotope({
  // options
  itemSelector: '.grid-item',
  layoutMode: 'fitRows'
});

$('.filterable-btn').on( 'click', 'button', function() {
  var filterValue = $(this).attr('data-filter');
  $(this).toggleClass('active').siblings().removeClass('active');
  $grid.isotope({ filter: filterValue });
});

