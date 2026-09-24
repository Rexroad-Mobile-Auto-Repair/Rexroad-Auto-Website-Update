(() => {
    'use strict';

    const revealItems = document.querySelectorAll('.rr-reveal');

    if (!revealItems.length) {
        return;
    }

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) {
        revealItems.forEach((item) => item.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries, instance) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add('is-visible');
                instance.unobserve(entry.target);
            });
        },
        {
            rootMargin: '0px 0px -10% 0px',
            threshold: 0.12,
        }
    );

    revealItems.forEach((item) => observer.observe(item));
})();
