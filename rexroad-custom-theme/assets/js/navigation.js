(() => {
    'use strict';

    const button = document.querySelector('.menu-toggle');
    const panel = document.querySelector('.header-navigation-panel');

    if (!button || !panel) {
        return;
    }

    const closeMenu = () => {
        panel.classList.remove('is-open');
        button.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('navigation-open');
    };

    button.addEventListener('click', () => {
        const isOpen = panel.classList.toggle('is-open');
        button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        document.body.classList.toggle('navigation-open', isOpen);
    });

    document.addEventListener('click', (event) => {
        if (!panel.classList.contains('is-open')) {
            return;
        }

        if (panel.contains(event.target) || button.contains(event.target)) {
            return;
        }

        closeMenu();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenu();
            button.focus();
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 1020) {
            closeMenu();
        }
    });
})();
