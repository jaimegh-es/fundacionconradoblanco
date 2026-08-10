/**
 * Navegación y comportamiento de tarjetas interactivas.
 */
(function () {
  var toggle = document.querySelector('.nav-toggle');
  var nav = document.querySelector('.main-navigation');

  if (toggle && nav) {
    // Inyectar iconos Lucide en elementos con submenú
    var parentLinks = nav.querySelectorAll('.menu-item-has-children > a');
    parentLinks.forEach(function (link) {
      if (!link.querySelector('[data-lucide="chevron-down"]')) {
        var icon = document.createElement('i');
        icon.setAttribute('data-lucide', 'chevron-down');
        icon.className = 'menu-chevron';
        link.appendChild(icon);
      }
    });

    // Abrir/cerrar menú principal en móvil
    toggle.addEventListener('click', function () {
      var isOpen = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    // Menú de navegación móvil: primer clic despliega, segundo navega
    parentLinks.forEach(function (link) {
      link.addEventListener('click', function (e) {
        if (window.innerWidth <= 820) {
          var parentLi = link.parentNode;
          if (!parentLi.classList.contains('sub-menu-open')) {
            e.preventDefault();
            // Colapsar submenús hermanos
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

    // Cerrar menú al hacer clic fuera del header
    document.addEventListener('click', function (event) {
      if (!event.target.closest('.site-header')) {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  // Tarjetas colapsables en la portada: primer clic despliega, segundo navega
  var cardsWithSubpages = document.querySelectorAll('.card--page.has-subpages');
  cardsWithSubpages.forEach(function (card) {
    card.addEventListener('click', function (e) {
      // Si el clic es en un subenlace, dejamos que navegue normalmente
      if (e.target.closest('.card-subpages-list a')) {
        return;
      }

      if (!card.classList.contains('is-expanded')) {
        e.preventDefault();
        e.stopPropagation();

        // Colapsar otras tarjetas para que solo una esté abierta a la vez
        cardsWithSubpages.forEach(function (c) {
          if (c !== card) {
            c.classList.remove('is-expanded');
          }
        });

        card.classList.add('is-expanded');
      }
    });
  });

  // Inicializar Lucide Icons
  if (typeof lucide !== 'undefined') {
    lucide.createIcons();
  }
})();
