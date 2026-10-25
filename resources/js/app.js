import './tenaga-kependidikan.js';
import './bkk-section.js';

/*
|--------------------------------------------------------------------------
| Hero slider
|--------------------------------------------------------------------------
|
| Rotates the hero photos on their own, holding each slide for the duration
| the config gives it, and lets the corner dots jump straight to one. The
| markup ships with the first slide already marked active, so this only takes
| over the timing — without it the banner still shows its first photo.
|
| Nothing here decides how anything looks: the motion lives in
| resources/css/app.css and is driven purely by the data-state this flips.
|
*/

const hero = document.querySelector('[data-hero-slider]');

if (hero) {
    const slides = [...hero.querySelectorAll('[data-hero-slide]')];
    const caption = hero.querySelector('[data-hero-caption]');
    const dots = [...hero.querySelectorAll('[data-hero-dot]')];
    const still = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    let current = 0;
    let timer;

    function show(next) {
        const previous = (next - 1 + slides.length) % slides.length;

        slides.forEach((slide, index) => {
            slide.dataset.state = index === next ? 'active' : index === previous ? 'before' : '';
        });

        dots.forEach((dot, index) => {
            dot.dataset.current = index === next ? 'true' : 'false';
        });

        if (caption) {
            caption.dataset.state = slides[next].dataset.heroText === 'true' ? 'active' : '';
        }
    }

    function schedule() {
        window.clearTimeout(timer);

        if (still || slides.length < 2) {
            return;
        }

        timer = window.setTimeout(function advance() {
            current = (current + 1) % slides.length;
            show(current);
            schedule();
        }, Number(slides[current].dataset.heroDuration) || 1000);
    }

    dots.forEach(function (dot) {
        dot.addEventListener('click', function () {
            current = Number(dot.dataset.heroDot) || 0;
            show(current);
            schedule();
        });
    });

    show(0);
    schedule();
}

/*
|--------------------------------------------------------------------------
| Scroll reveals
|--------------------------------------------------------------------------
|
| Reveals [data-reveal] blocks once they have been scrolled into view. The
| motion itself belongs to resources/css/app.css — this only decides when,
| and then stops watching, because these are entrance animations and not
| scroll-linked ones: the block should not replay every time it re-enters.
|
*/

const revealables = document.querySelectorAll('[data-reveal], [data-reveal-group]');

if (revealables.length && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver(
        function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.dataset.revealed = 'true';
                observer.unobserve(entry.target);
            });
        },
        { threshold: 0.2, rootMargin: '0px 0px -8% 0px' }
    );

    revealables.forEach(function (element) {
        observer.observe(element);
    });
}
