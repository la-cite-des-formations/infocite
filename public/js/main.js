/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!******************************!*\
  !*** ./resources/js/main.js ***!
  \******************************/
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

/**
* Template Name: Arsha - v4.7.1
* Template URL: https://bootstrapmade.com/arsha-free-bootstrap-html-template-corporate/
* Author: BootstrapMade.com
* License: https://bootstrapmade.com/license/
*/
(function () {
  "use strict";
  /**
   * Easy selector helper function
   */

  var select = function select(el) {
    var all = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
    el = el.trim();

    if (all) {
      return _toConsumableArray(document.querySelectorAll(el));
    } else {
      return document.querySelector(el);
    }
  };
  /**
   * Easy event listener function
   */


  var on = function on(type, el, listener) {
    var all = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
    var selectEl = select(el, all);

    if (selectEl) {
      if (all) {
        selectEl.forEach(function (e) {
          return e.addEventListener(type, listener);
        });
      } else {
        selectEl.addEventListener(type, listener);
      }
    }
  };
  /**
   * Easy on scroll event listener
   */


  var onscroll = function onscroll(el, listener) {
    el.addEventListener('scroll', listener);
  };
  /**
   * Navbar links active state on scroll
   */


  var navbarlinks = select('#navbar .scrollto', true);

  var navbarlinksActive = function navbarlinksActive() {
    var position = window.scrollY + 200;
    navbarlinks.forEach(function (navbarlink) {
      if (!navbarlink.hash) return;
      var section = select(navbarlink.hash);
      if (!section) return;
      var parent = navbarlink.parentElement.parentElement.previousElementSibling;

      if (position >= section.offsetTop && position <= section.offsetTop + section.offsetHeight) {
        navbarlink.classList.add('active');

        if (parent && parent.localName == 'a') {
          parent.classList.add('active');
        }
      } else {
        navbarlink.classList.remove('active');

        if (parent && parent.localName == 'a') {
          parent.classList.remove('active');
        }
      }
    });
  };

  window.addEventListener('load', navbarlinksActive);
  onscroll(document, navbarlinksActive);
  /**
   * Scrolls to an element with header offset
   */

  var scrollto = function scrollto(el) {
    var header = select('#header');
    var offset = header.offsetHeight;
    var elementPos = select(el).offsetTop;
    window.scrollTo({
      top: elementPos - offset,
      behavior: 'smooth'
    });
  };
  /**
   * Toggle .header-scrolled class to #header when page is scrolled
   */


  var selectHeader = select('#header');

  if (selectHeader) {
    var headerScrolled = function headerScrolled() {
      if (window.scrollY > 100) {
        selectHeader.classList.add('header-scrolled');
      } else {
        selectHeader.classList.remove('header-scrolled');
      }
    };

    window.addEventListener('load', headerScrolled);
    onscroll(document, headerScrolled);
  }
  /**
   * Back to top button
   */


  var backtotop = select('.back-to-top');

  if (backtotop) {
    var toggleBacktotop = function toggleBacktotop() {
      if (window.scrollY > 100) {
        backtotop.classList.add('active');
      } else {
        backtotop.classList.remove('active');
      }
    };

    window.addEventListener('load', toggleBacktotop);
    onscroll(document, toggleBacktotop);
  }
  /**
   * Mobile nav toggle
   */


  on('click', '.mobile-nav-toggle', function (e) {
    select('#navbar').classList.toggle('navbar-mobile');
    this.classList.toggle('bi-list');
    this.classList.toggle('bi-x');
  });
  /**
   * Mobile nav dropdowns activate
   */

  on('click', '.navbar .dropdown > a', function (e) {
    if (select('#navbar').classList.contains('navbar-mobile')) {
      e.preventDefault();
      this.nextElementSibling.classList.toggle('dropdown-active');
    }
  }, true);
  /**
   * Scrool with ofset on links with a class name .scrollto
   */

  on('click', '.scrollto', function (e) {
    if (select(this.hash)) {
      e.preventDefault();
      var navbar = select('#navbar');

      if (navbar.classList.contains('navbar-mobile')) {
        navbar.classList.remove('navbar-mobile');
        var navbarToggle = select('.mobile-nav-toggle');
        navbarToggle.classList.toggle('bi-list');
        navbarToggle.classList.toggle('bi-x');
      }

      scrollto(this.hash);
    }
  }, true);
  /**
   * Scroll with ofset on page load with hash links in the url
   */

  window.addEventListener('load', function () {
    if (window.location.hash) {
      if (select(window.location.hash)) {
        scrollto(window.location.hash);
      }
    }
  });
  /**
   * Preloader
   */

  var preloader = select('#preloader');

  if (preloader) {
    window.addEventListener('load', function () {
      preloader.remove();
    });
  }
  /**
   * Initiate  glightbox
   */


  var glightbox = GLightbox({
    selector: '.glightbox'
  });
  /**
   * Skills animation
   */

  var skilsContent = select('.skills-content');

  if (skilsContent) {
    new Waypoint({
      element: skilsContent,
      offset: '80%',
      handler: function handler(direction) {
        var progress = select('.progress .progress-bar', true);
        progress.forEach(function (el) {
          el.style.width = el.getAttribute('aria-valuenow') + '%';
        });
      }
    });
  }
  /**
   * Porfolio isotope and filter
   */


  window.addEventListener('load', function () {
    var portfolioContainer = select('.portfolio-container');

    if (portfolioContainer) {
      var portfolioIsotope = new Isotope(portfolioContainer, {
        itemSelector: '.portfolio-item'
      });
      var portfolioFilters = select('#portfolio-flters li', true);
      on('click', '#portfolio-flters li', function (e) {
        e.preventDefault();
        portfolioFilters.forEach(function (el) {
          el.classList.remove('filter-active');
        });
        this.classList.add('filter-active');
        portfolioIsotope.arrange({
          filter: this.getAttribute('data-filter')
        });
        portfolioIsotope.on('arrangeComplete', function () {
          AOS.refresh();
        });
      }, true);
    }
  });
  /**
   * Initiate portfolio lightbox
   */

  var portfolioLightbox = GLightbox({
    selector: '.portfolio-lightbox'
  });
  /**
   * Portfolio details slider
   */

  new Swiper('.portfolio-details-slider', {
    speed: 400,
    loop: true,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false
    },
    pagination: {
      el: '.swiper-pagination',
      type: 'bullets',
      clickable: true
    }
  });
  /**
   * Animation on scroll
   */

  window.addEventListener('load', function () {
    AOS.init({
      duration: 1000,
      easing: "ease-in-out",
      once: true,
      mirror: false
    });
  });

  function scrollToAnchorWithOffset(anchorID, offset) {
    var target = document.getElementById(anchorID);

    if (target) {
      var targetPosition = target.offsetTop - offset;
      window.scrollTo({
        top: targetPosition,
        behavior: "smooth"
      });
    }
  }

  if (document.getElementById('paginationContainer')) {
    document.getElementById('paginationContainer').addEventListener('click', function () {
      scrollToAnchorWithOffset('scrollToResult', 100);
      console.log('paginationContainer');
    });
  }

  Echo.channel("notificationPostChannel.".concat(window.userId)).listen('NotificationPusher', function (notification) {
    // Création des éléments de la notification
    var notificationElement = document.createElement('div');
    notificationElement.classList.add('toast', 'mb-2');
    notificationElement.setAttribute('role', 'alert');
    notificationElement.setAttribute('aria-live', 'assertive');
    notificationElement.setAttribute('aria-atomic', 'true');
    var header = document.createElement('div');
    header.classList.add('toast-header');
    var title = document.createElement('strong');
    title.classList.add('me-auto');
    title.innerText = notification.message;
    var time = document.createElement('small');
    time.id = "time-".concat(Date.now()); // Utiliser un ID unique pour chaque élément de temps

    var closeButton = document.createElement('button');
    closeButton.classList.add('btn-close');
    closeButton.setAttribute('data-bs-dismiss', 'toast');
    closeButton.setAttribute('aria-label', 'Close');
    header.appendChild(title);
    header.appendChild(time);
    header.appendChild(closeButton);
    var body = document.createElement('div');
    body.classList.add('toast-body');
    var postRedirect = document.createElement('a');
    postRedirect.href = notification.href;
    var postBody = document.createElement('div');
    postBody.classList.add('toast-body');
    postBody.innerText = notification.post_title;
    postRedirect.appendChild(postBody);
    body.appendChild(postRedirect);
    notificationElement.appendChild(header);
    notificationElement.appendChild(body);
    document.getElementById('toast-container').appendChild(notificationElement); // Horodatage de la notification

    var receivedTime = new Date();

    function updateElapsedTime() {
      var now = new Date();
      var elapsed = Math.floor((now - receivedTime) / 1000); // temps écoulé en secondes

      var minutes = Math.floor(elapsed / 60);
      time.innerText = "Il y a ".concat(minutes, " mins");
    }

    setInterval(updateElapsedTime, 60000); // Mettre à jour toutes les minutes
    // Création de la notification Bootstrap

    var notificationPush = new bootstrap.Toast(notificationElement, {
      autohide: false
    }); // Affichage de la notification et modification du titre de l'onglet

    notificationPush.show();
    var originalTitle = document.title;
    var newTitle = "New notification";
    var blink = true;
    var interval = setInterval(function () {
      document.title = blink ? newTitle : originalTitle;
      blink = !blink;
    }, 1000); // Stop blink quand l'utilisateur focus la page

    window.addEventListener('focus', function () {
      clearInterval(interval);
      document.title = originalTitle;
    });
  }); // Echo.channel(`notificationPostChannel.${window.userId}`)
  //     .listen('NotificationPusher', (notification) => {
  //
  //         Toastify({
  //             text: `${notification.message} ${notification.post_title} `,
  //             duration: -1,
  //             destination: notification.href,
  //             newWindow: false,
  //             close: true,
  //             gravity: "bottom", // `top` or `bottom`
  //             position: "right", // `left`, `center` or `right`
  //             stopOnFocus: true, // Prevents dismissing of toast on hover
  //             style: {
  //                 color :"var(--select-color-11)",
  //                 background: "linear-gradient(to right, var(--select-color-10),var(--select-color-16),var(--select-color-11))",
  //             },
  //         }).showToast()
  //
  //         let originalTitle = document.title
  //         let newTilte = "New notification"
  //         let blink = true
  //
  //         let interval = setInterval(()=>{
  //             document.title = blink ? newTilte : originalTitle
  //             blink = !blink
  //         }, 1000)
  //
  //         //Stop blink quand l'utilisateur focus la page
  //         window.addEventListener('focus',()=>{
  //             clearInterval(interval);
  //             document.title = originalTitle
  //         });
  //     });
})();
/******/ })()
;