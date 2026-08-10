/**
 * Navegación, cabecera, hero, transiciones entre páginas y animaciones de entrada.
 */
(function () {
  var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  var isHome = document.body.classList.contains('home');

  /* ---------- Transición entre páginas ---------- */
  var overlay = document.querySelector('.fcb-transition-overlay');
  var rootEl = document.documentElement;

  // Llegada: si venimos de una navegación interna, el overlay cubre la página
  // y se desliza hacia arriba revelando el contenido.
  if (overlay && !prefersReducedMotion.matches) {
    var transitioning = sessionStorage.getItem('fcb-transition') === '1';
    sessionStorage.removeItem('fcb-transition');

    if (transitioning) {
      rootEl.classList.add('fcb-transitioning');
      setTimeout(function () {
        rootEl.classList.add('fcb-arriving');
      }, 40);
      setTimeout(function () {
        rootEl.classList.remove('fcb-transitioning');
        rootEl.classList.remove('fcb-arriving');
      }, 700);
    }

    // Salida: interceptar enlaces internos, subir el fondo blanco y navegar.
    var leaving = false;
    var homeLink = document.querySelector('.custom-logo-link');
    var homePath = '';
    if (homeLink) {
      try {
        homePath = new URL(homeLink.href).pathname;
      } catch (err) {
        homePath = '';
      }
    }

    document.addEventListener('click', function (e) {
      if (leaving || e.defaultPrevented) {
        return;
      }
      var link = e.target.closest('a[href]');
      if (!link) {
        return;
      }
      if (link.target && link.target !== '_self') {
        return;
      }
      if (link.hasAttribute('download') || link.getAttribute('href') === '#') {
        return;
      }
      if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) {
        return;
      }

      var url;
      try {
        url = new URL(link.href, window.location.href);
      } catch (err) {
        return;
      }

      if (url.origin !== window.location.origin) {
        return;
      }
      if (url.hash) {
        return;
      }
      if (homePath && url.pathname === homePath) {
        return; // la portada se carga sin transición (ya tiene sus efectos)
      }

      e.preventDefault();
      leaving = true;
      sessionStorage.setItem('fcb-transition', '1');
      document.body.classList.add('fcb-leaving');
      setTimeout(function () {
        window.location.href = link.href;
      }, 520);
    });
  }

  /* ---------- Texto de entradas: palabra a palabra con desenfoque ---------- */
  var entryContents = document.querySelectorAll('.entry-content');

  if (entryContents.length && !isHome && !prefersReducedMotion.matches) {
    var wordIndex = 0;

    var splitWords = function (root) {
      var texts = [];
      var walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, {
        acceptNode: function (node) {
          var parent = node.parentNode;
          if (!parent) {
            return NodeFilter.FILTER_REJECT;
          }
          var tag = parent.tagName ? parent.tagName.toLowerCase() : '';
          if (tag === 'script' || tag === 'style' || tag === 'code' || tag === 'pre') {
            return NodeFilter.FILTER_REJECT;
          }
          var value = node.nodeValue.replace(/\s+/g, ' ');
          if (!value.trim()) {
            return NodeFilter.FILTER_REJECT;
          }
          return NodeFilter.FILTER_ACCEPT;
        }
      }, false);

      while (walker.nextNode()) {
        texts.push(walker.currentNode);
      }

      texts.forEach(function (textNode) {
        var words = textNode.nodeValue.split(/(\s+)/);
        var frag = document.createDocumentFragment();
        words.forEach(function (part) {
          if (part === '') {
            return;
          }
          if (/^\s+$/.test(part)) {
            frag.appendChild(document.createTextNode(' '));
          } else {
            var word = document.createElement('span');
            word.className = 'word';
            var inner = document.createElement('span');
            inner.className = 'word-inner';
            inner.style.setProperty('--wi', wordIndex);
            inner.textContent = part;
            word.appendChild(inner);
            frag.appendChild(word);
            wordIndex++;
          }
        });
        textNode.parentNode.replaceChild(frag, textNode);
      });
    };

    entryContents.forEach(function (content) {
      splitWords(content);
    });
    requestAnimationFrame(function () {
      entryContents.forEach(function (content) {
        content.classList.add('is-visible');
      });
    });
  }

  /* ---------- Cabecera sólida al salir del hero ---------- */
  var header = document.querySelector('.site-header');

  function headerThreshold() {
    var heroH = document.querySelector('.hero') ? document.querySelector('.hero').offsetHeight : 0;
    var headerH = header ? header.offsetHeight : 0;
    return Math.max(1, heroH - headerH);
  }

  if (header && isHome) {
    var threshold = headerThreshold();
    var updateHeader = function () {
      header.classList.toggle('is-solid', window.scrollY > threshold);
    };
    updateHeader();
    window.addEventListener('scroll', updateHeader, { passive: true });
    window.addEventListener('resize', function () {
      threshold = headerThreshold();
      updateHeader();
    });
  }

  /* ---------- Hero: overlay de carga con barra de progreso ---------- */
  var hero = document.querySelector('.hero');
  if (hero) {
    hero.classList.add('is-loading');
    document.body.classList.add('hero-loading');
    var video = hero.querySelector('.hero-video');
    var barFill = hero.querySelector('.hero-loader__bar-fill');
    var loaded = false;

    var finish = function () {
      if (loaded) {
        return;
      }
      loaded = true;
      if (barFill) {
        barFill.style.transform = 'scaleX(1)';
      }
      hero.classList.remove('is-loading');
      hero.classList.add('is-loaded');
      document.body.classList.remove('hero-loading');
      document.body.classList.add('hero-loaded');
    };

    var updateProgress = function () {
      if (!barFill || loaded) {
        return;
      }
      var duration = video.duration;
      if (!duration || !isFinite(duration) || duration <= 0) {
        return;
      }
      var buffered = video.buffered && video.buffered.length
        ? video.buffered.end(video.buffered.length - 1)
        : 0;
      barFill.style.transform = 'scaleX(' + Math.min(1, buffered / duration) + ')';
    };

    if (video && !prefersReducedMotion.matches) {
      video.addEventListener('progress', updateProgress);
      video.addEventListener('loadeddata', updateProgress);
      video.addEventListener('canplay', finish);
      video.addEventListener('canplaythrough', finish);
      setTimeout(finish, 4000);
    } else {
      finish();
    }
  }

  /* ---------- Secciones: se revelan al entrar en pantalla ---------- */
  var sections = document.querySelectorAll('.section');
  if (sections.length) {
    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            io.unobserve(entry.target);
          }
        });
      }, { threshold: 0.15, rootMargin: '0px 0px -8% 0px' });
      sections.forEach(function (section) {
        io.observe(section);
      });
    } else {
      sections.forEach(function (section) {
        section.classList.add('is-visible');
      });
    }
  }

  /* ---------- Menú móvil y tarjetas colapsables ---------- */
  var toggle = document.querySelector('.nav-toggle');
  var nav = document.querySelector('.main-navigation');

  if (toggle && nav) {
    var parentLinks = nav.querySelectorAll('.menu-item-has-children > a');
    parentLinks.forEach(function (link) {
      if (!link.querySelector('[data-lucide="chevron-down"]')) {
        var icon = document.createElement('i');
        icon.setAttribute('data-lucide', 'chevron-down');
        icon.className = 'menu-chevron';
        link.appendChild(icon);
      }
    });

    toggle.addEventListener('click', function () {
      var isOpen = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    parentLinks.forEach(function (link) {
      link.addEventListener('click', function (e) {
        if (window.innerWidth <= 820) {
          var parentLi = link.parentNode;
          if (!parentLi.classList.contains('sub-menu-open')) {
            e.preventDefault();
            var siblings = parentLi.parentNode.querySelectorAll('.menu-item-has-children');
            siblings.forEach(function (sib) {
              if (sib !== parentLi) {
                sib.classList.remove('sub-menu-open');
              }
            });
            parentLi.classList.add('sub-menu-open');
          }
        }
      });
    });

    document.addEventListener('click', function (event) {
      if (!event.target.closest('.site-header')) {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  var cardsWithSubpages = document.querySelectorAll('.card--page.has-subpages');
  cardsWithSubpages.forEach(function (card) {
    card.addEventListener('click', function (e) {
      if (e.target.closest('.card-subpages-list a')) {
        return;
      }

      if (!card.classList.contains('is-expanded')) {
        e.preventDefault();
        e.stopPropagation();

        cardsWithSubpages.forEach(function (c) {
          if (c !== card) {
            c.classList.remove('is-expanded');
          }
        });

        card.classList.add('is-expanded');
      }
    });
  });

  /* ---------- Compartir nativo (Android/iOS) ---------- */
  var shareBtn = document.querySelector('[data-share-native]');
  if (shareBtn) {
    shareBtn.addEventListener('click', function () {
      var title = shareBtn.getAttribute('data-share-title');
      var url = shareBtn.getAttribute('data-share-url');

      if (navigator.share) {
        navigator.share({ title: title || '', url: url || '' }).catch(function () {});
      } else {
        var temp = document.createElement('textarea');
        temp.value = url || window.location.href;
        temp.style.position = 'fixed';
        temp.style.opacity = '0';
        document.body.appendChild(temp);
        temp.select();
        try {
          document.execCommand('copy');
        } catch (err) {}
        document.body.removeChild(temp);
        shareBtn.classList.add('is-copied');
        shareBtn.setAttribute('data-copied-text', 'Enlace copiado');
        setTimeout(function () {
          shareBtn.classList.remove('is-copied');
        }, 2000);
      }
    });
  }

  if (typeof lucide !== 'undefined') {
    lucide.createIcons();
  }
})();
