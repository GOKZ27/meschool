const bkkSection = document.querySelector('[data-bkk-section]');

if (bkkSection) {
    const filterButtons = [...bkkSection.querySelectorAll('[data-bkk-filter]')];
    const jobs = [...bkkSection.querySelectorAll('[data-bkk-job]')];
    const count = bkkSection.querySelector('[data-bkk-count]');
    const emptyFilter = bkkSection.querySelector('[data-bkk-empty-filter]');

    function filterJobs(category) {
        let visible = 0;

        jobs.forEach((job) => {
            const matches = category === 'all' || job.dataset.category === category;
            job.hidden = !matches;
            visible += Number(matches);
        });

        if (count) {
            count.textContent = `Menampilkan ${visible} lowongan terbaru.`;
        }

        if (emptyFilter) {
            emptyFilter.hidden = visible !== 0;
        }
    }

    filterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            filterButtons.forEach((item) => {
                item.setAttribute('aria-pressed', String(item === button));
            });

            filterJobs(button.dataset.bkkFilter);
        });
    });

    const revealItems = [...bkkSection.querySelectorAll('[data-bkk-reveal]')];

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries, currentObserver) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.dataset.bkkRevealed = 'true';
                    currentObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.12,
            rootMargin: '0px 0px -6% 0px',
        });

        revealItems.forEach((item) => observer.observe(item));
    } else {
        revealItems.forEach((item) => {
            item.dataset.bkkRevealed = 'true';
        });
    }
}
