// MENU HAMBURGER

$(document).ready(function(){
    $('.btn-burger').click(function(){
        $(this).find('.icon-bar').toggleClass('cross');
        $('.nav-content').toggleClass('isOpen');
        $('.btn-burger').toggleClass('menu-open');
        $('.bg-opacity').toggleClass('active');
    });

    $('.btn-burger-filter').click(function(){
        $(this).find('.icon-bar-filter').toggleClass('cross-filter');
        $('.collumn-filter').toggleClass('show-filter');
    });
    
    $('.login_button').click(function(){
        $('.loginchoise').toggleClass('isOpen');
    });

    $('.photo-slider').click(function(){
        $('.photo-container-zoom').toggleClass('active');
    });
    
    $('.btn-exit').click(function(){
      $('.photo-container-zoom').toggleClass('active');
    });
    
    $('.btn-add').click(function(){
        $('.form-add-pic').toggleClass('active');
    });

    $('.btn-close').click(function(){
        $('.form-add-pic').toggleClass('active');
    });
});

// PHOTO SLIDE PAGE ARTICLE 

$(function() {
  
    var N = 0;
    var K = 0;
    var tt
    
    START();

    function START() {
      tt = setInterval(NEXT, 30000);
    }

    function NEXT() {

      if( N < $('.photo-slider-img').length - 1 ) {
        N++;
      } else {
        N = 0;
      }

      CHANGE();
    }

    function CHANGE() {
      K = 1;   
      $('.photo-slider-img.NOW').stop().animate({left: '-100%'}, 500);
      $('.photo-slider-img').eq(N).stop().css({left: '100%'}).animate({left: 0}, 500, OK);
    }

    function OK() {
      K = 0;
      $('.photo-page').removeClass('active').eq(N).addClass('active');
      $('.photo-slider-img').removeClass('NOW').eq(N).addClass('NOW');
    }



    $('.photo-page').on('click', function() {

      if( $(this).index() == N || K == 1 ) return;
      
      if ( tt ) {
         clearInterval( tt );
         tt = 0;
         N = $(this).index();
         CHANGE();
         START();
       }
    });

});

// ZOOM PHOTO

$(function() {
  
  var N = 0;
  var K = 0;
  var tt
  
  START();

  function START() {
    tt = setInterval(NEXT, 30000);
  }

  function NEXT() {

    if( N < $('.photo-slider-img-zoom').length - 1 ) {
      N++;
    } else {
      N = 0;
    }

    CHANGE();
  }

  function CHANGE() {
    K = 1;   
    $('.photo-slider-img-zoom.NOW').stop().animate({left: '-100%'}, 500);
    $('.photo-slider-img-zoom').eq(N).stop().css({left: '100%'}).animate({left: 0}, 500, OK);
  }

  function OK() {
    K = 0;
    $('.photo-page-zoom').removeClass('active').eq(N).addClass('active');
    $('.photo-slider-img-zoom').removeClass('NOW').eq(N).addClass('NOW');
  }



  $('.photo-page-zoom').on('click', function() {

    if( $(this).index() == N || K == 1 ) return;
    
    if ( tt ) {
       clearInterval( tt );
       tt = 0;
       N = $(this).index();
       CHANGE();
       START();
     }
  });

});

// CARROUSEL HOME PAGE 

$(function() {
  
  var N = 0;
  var K = 0;
  var tt
  
  START();

  function START() {
    tt = setInterval(NEXT,10000);
  }

  function NEXT() {

    if( N < $('.carrousel-slider-img').length - 1 ) {
      N++;
    } else {
      N = 0;
    }

    CHANGE();
  }

  function CHANGE() {
    K = 1;   
    $('.carrousel-slider-img.NOW').stop().animate({left: '-100%'}, 500);
    $('.carrousel-slider-img').eq(N).stop().css({left: '100%'}).animate({left: 0}, 500, OK);
  }

  function OK() {
    K = 0;
    $('.carrousel-page').removeClass('active').eq(N).addClass('active');
    $('.carrousel-slider-img').removeClass('NOW').eq(N).addClass('NOW');
  }



  $('.carrousel-page').on('click', function() {

    if( $(this).index() == N || K == 1 ) return;
    
    if ( tt ) {
       clearInterval( tt );
       tt = 0;
       N = $(this).index();
       CHANGE();
       START();
     }
  });

});


// PHOTO AJOUT PANIER

function showPreview(event) {
  if (event.target.files.length > 0) {
      var src = URL.createObjectURL(event.target.files[0]);
      var preview = document.getElementById("file-ip-1-preview");
      var addToCart = document.getElementById("add-to-cart");
      var label = document.getElementById("label-add-to-cart");
      preview.src = src;
      preview.style.display = "block";
      addToCart.style.display = "block";
      label.textContent = 'Changer la photo';
  }
}