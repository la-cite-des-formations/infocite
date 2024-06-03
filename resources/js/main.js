/**
* Template Name: Arsha - v4.7.1
* Template URL: https://bootstrapmade.com/arsha-free-bootstrap-html-template-corporate/
* Author: BootstrapMade.com
* License: https://bootstrapmade.com/license/
*/
(function() {
  "use strict";

  /**
   * Easy selector helper function
   */
  const select = (el, all = false) => {
    el = el.trim()
    if (all) {
      return [...document.querySelectorAll(el)]
    } else {
      return document.querySelector(el)
    }
  }

  /**
   * Easy event listener function
   */
  const on = (type, el, listener, all = false) => {
    let selectEl = select(el, all)
    if (selectEl) {
      if (all) {
        selectEl.forEach(e => e.addEventListener(type, listener))
      } else {
        selectEl.addEventListener(type, listener)
      }
    }
  }

  /**
   * Easy on scroll event listener
   */
  const onscroll = (el, listener) => {
    el.addEventListener('scroll', listener)
  }

  /**
   * Navbar links active state on scroll
   */
  let navbarlinks = select('#navbar .scrollto', true)
  const navbarlinksActive = () => {
    let position = window.scrollY + 200
    navbarlinks.forEach(navbarlink => {
      if (!navbarlink.hash) return
      let section = select(navbarlink.hash)
      if (!section) return
      let parent = navbarlink.parentElement.parentElement.previousElementSibling
      if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
        navbarlink.classList.add('active')
        if (parent && parent.localName == 'a') {
            parent.classList.add('active')
        }
      } else {
        navbarlink.classList.remove('active')
        if (parent && parent.localName == 'a') {
            parent.classList.remove('active')
        }
      }
    })
  }
  window.addEventListener('load', navbarlinksActive)
  onscroll(document, navbarlinksActive)

  /**
   * Scrolls to an element with header offset
   */
  const scrollto = (el) => {
    let header = select('#header')
    let offset = header.offsetHeight

    let elementPos = select(el).offsetTop
    window.scrollTo({
      top: elementPos - offset,
      behavior: 'smooth'
    })
  }

  /**
   * Toggle .header-scrolled class to #header when page is scrolled
   */
  let selectHeader = select('#header')
  if (selectHeader) {
    const headerScrolled = () => {
      if (window.scrollY > 100) {
        selectHeader.classList.add('header-scrolled')
      } else {
        selectHeader.classList.remove('header-scrolled')
      }
    }
    window.addEventListener('load', headerScrolled)
    onscroll(document, headerScrolled)
  }

  /**
   * Back to top button
   */
  let backtotop = select('.back-to-top')
  if (backtotop) {
    const toggleBacktotop = () => {
      if (window.scrollY > 100) {
        backtotop.classList.add('active')
      } else {
        backtotop.classList.remove('active')
      }
    }
    window.addEventListener('load', toggleBacktotop)
    onscroll(document, toggleBacktotop)
  }

  /**
   * Mobile nav toggle
   */
  on('click', '.mobile-nav-toggle', function(e) {
    select('#navbar').classList.toggle('navbar-mobile')
    this.classList.toggle('bi-list')
    this.classList.toggle('bi-x')
  })

  /**
   * Mobile nav dropdowns activate
   */
  on('click', '.navbar .dropdown > a', function(e) {
    if (select('#navbar').classList.contains('navbar-mobile')) {
      e.preventDefault()
      this.nextElementSibling.classList.toggle('dropdown-active')
    }
  }, true)

  /**
   * Scrool with ofset on links with a class name .scrollto
   */
  on('click', '.scrollto', function(e) {
    if (select(this.hash)) {
      e.preventDefault()

      let navbar = select('#navbar')
      if (navbar.classList.contains('navbar-mobile')) {
        navbar.classList.remove('navbar-mobile')
        let navbarToggle = select('.mobile-nav-toggle')
        navbarToggle.classList.toggle('bi-list')
        navbarToggle.classList.toggle('bi-x')
      }
      scrollto(this.hash)
    }
  }, true)

  /**
   * Scroll with ofset on page load with hash links in the url
   */
  window.addEventListener('load', () => {
    if (window.location.hash) {
      if (select(window.location.hash)) {
        scrollto(window.location.hash)
      }
    }
  });

  /**
   * Preloader
   */
  let preloader = select('#preloader');
  if (preloader) {
    window.addEventListener('load', () => {
      preloader.remove()
    });
  }

  /**
   * Initiate  glightbox
   */
  const glightbox = GLightbox({
    selector: '.glightbox'
  });

  /**
   * Skills animation
   */
  let skilsContent = select('.skills-content');
  if (skilsContent) {
    new Waypoint({
      element: skilsContent,
      offset: '80%',
      handler: function(direction) {
        let progress = select('.progress .progress-bar', true);
        progress.forEach((el) => {
          el.style.width = el.getAttribute('aria-valuenow') + '%'
        });
      }
    })
  }

  /**
   * Porfolio isotope and filter
   */
  window.addEventListener('load', () => {
    let portfolioContainer = select('.portfolio-container');
    if (portfolioContainer) {
      let portfolioIsotope = new Isotope(portfolioContainer, {
        itemSelector: '.portfolio-item'
      });

      let portfolioFilters = select('#portfolio-flters li', true);

      on('click', '#portfolio-flters li', function(e) {
        e.preventDefault();
        portfolioFilters.forEach(function(el) {
          el.classList.remove('filter-active');
        });
        this.classList.add('filter-active');

        portfolioIsotope.arrange({
          filter: this.getAttribute('data-filter')
        });
        portfolioIsotope.on('arrangeComplete', function() {
          AOS.refresh()
        });
      }, true);
    }

  });

  /**
   * Initiate portfolio lightbox
   */
  const portfolioLightbox = GLightbox({
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
  window.addEventListener('load', () => {
    AOS.init({
      duration: 1000,
      easing: "ease-in-out",
      once: true,
      mirror: false
    });
  });




    function scrollToAnchorWithOffset(anchorID, offset) {
        let target = document.getElementById(anchorID)
        if (target) {
            let targetPosition = target.offsetTop - offset
            window.scrollTo({
                top: targetPosition,
                behavior: "smooth"
            });
        }
    }

    if(document.getElementById('paginationContainer')){
        document.getElementById('paginationContainer').addEventListener('click', function() {
            scrollToAnchorWithOffset('scrollToResult', 100);
            console.log('paginationContainer')
        });
    }

    Echo.channel(`notificationPostChannel.${window.userId}`)
        .listen('NotificationPusher', (notification) => {

            // Création des éléments de la notification
            let notificationElement = document.createElement('div');
            notificationElement.classList.add('toast', 'mb-2');
            notificationElement.setAttribute('role', 'alert');
            notificationElement.setAttribute('aria-live', 'assertive');
            notificationElement.setAttribute('aria-atomic', 'true');

            let header = document.createElement('div');
            header.classList.add('toast-header');

            let title = document.createElement('strong');
            title.classList.add('me-auto');
            title.innerText = notification.message;

            let time = document.createElement('small');
            time.id = `time-${Date.now()}`;// Utiliser un ID unique pour chaque élément de temps

            let closeButton = document.createElement('button');
            closeButton.classList.add('btn-close');
            closeButton.setAttribute('data-bs-dismiss', 'toast');
            closeButton.setAttribute('aria-label', 'Close');

            header.appendChild(title);
            header.appendChild(time);
            header.appendChild(closeButton);

            let body = document.createElement('div');
            body.classList.add('toast-body');

            let postRedirect = document.createElement('a');
            postRedirect.href = notification.href;

            let postBody = document.createElement('div');
            postBody.classList.add('toast-body');
            postBody.innerText = notification.post_title;

            postRedirect.appendChild(postBody);
            body.appendChild(postRedirect);

            notificationElement.appendChild(header);
            notificationElement.appendChild(body);

            document.getElementById('toast-container').appendChild(notificationElement);

            // Horodatage de la notification
            let receivedTime = new Date();

            function updateElapsedTime() {
                let now = new Date();
                let elapsed = Math.floor((now - receivedTime) / 1000); // temps écoulé en secondes
                let minutes = Math.floor(elapsed / 60);
                time.innerText = `Il y a ${minutes} mins`;
            }

            setInterval(updateElapsedTime, 60000); // Mettre à jour toutes les minutes

            // Création de la notification Bootstrap
            let notificationPush = new bootstrap.Toast(notificationElement, {
                autohide: false
            });

            // Affichage de la notification et modification du titre de l'onglet
            notificationPush.show();

            let originalTitle = document.title;
            let newTitle = "New notification";
            let blink = true;

            let interval = setInterval(() => {
                document.title = blink ? newTitle : originalTitle;
                blink = !blink;
            }, 1000);

            // Stop blink quand l'utilisateur focus la page
            window.addEventListener('focus', () => {
                clearInterval(interval);
                document.title = originalTitle;
            });

        });
// Echo.channel(`notificationPostChannel.${window.userId}`)
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













})()
