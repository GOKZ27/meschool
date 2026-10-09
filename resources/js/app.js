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

    // Sliding the photos is exactly the kind of motion the OS setting is meant
    // to suppress, so a reduced-motion visitor keeps the first photo and can
    // still step through the rest with the dots.
    const still = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    let current = 0;
    let timer;

    function show(next) {
        const previous = (next - 1 + slides.length) % slides.length;

        slides.forEach((slide, index) => {
            // Three states: the slide on screen, the one it is replacing, and
            // every other one parked to the right waiting its turn.
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

        // Each slide keeps its own time, so the delay is read from whichever
        // slide is currently on screen.
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
| motion itself belongs to resources/css/app.css — this only decides *when*,
| and then stops watching, because these are entrance animations and not
| scroll-linked ones: the block should not replay every time it re-enters.
|
| The hidden state is gated on .has-js, which the layout sets in <head>, so a
| visitor without JavaScript reads the page normally.
|
*/

// A group is a reveal target in its own right: it carries the cascade hook and
// hands the visible state to its children, so it has to be watched even when it
// has no data-reveal of its own (a grid of cards, for instance).
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
        // A block counts as arrived once a decent slice of it is on screen, and
        // slightly early so the transition has finished by the time the reader's
        // eye reaches the content.
        { threshold: 0.2, rootMargin: '0px 0px -8% 0px' }
    );

    revealables.forEach(function (element) {
        observer.observe(element);
    });
}
/*
|--------------------------------------------------------------------------
| Staff rail and profile popup
|--------------------------------------------------------------------------
|
| The rail is an endless marquee plus a profile popup. Moving the rail needs no
| script beyond the loop itself: it is a native horizontal scroller, so a
| trackpad, a shift-wheel and a finger all work already. What is left here is
| the part CSS cannot do - the infinite sweep, dragging with a mouse, the two
| arrows, and the profile dialog.
|
| The rail is the page's only horizontal scroller and the dialog is a native
| <dialog>, which brings the top layer, the backdrop, the Escape key and the
| focus trap on its own. Both blocks sit behind a presence check so pages
| without a staff section are untouched.
|
*/

const staffRail = document.querySelector('[data-staff-rail]');

if (staffRail) {
    const staffModal = document.querySelector('[data-staff-modal]');
    const staffData = JSON.parse(document.querySelector('[data-staff-data]').textContent);

    // The arrows sit outside the rail, beside its heading, so they are looked up
    // on the document rather than inside the list.
    const previous = document.querySelector('[data-staff-prev]');
    const next = document.querySelector('[data-staff-next]');

    // The rail sweeps sideways on its own as a marquee, and the cards are
    // duplicated once at runtime so the sweep can wrap to the front without
    // ever reaching a dead end. Sweeping sideways is exactly the motion the OS
    // setting is meant to suppress, so a reduced-motion visitor keeps a still
    // rail and steps through it with the arrows instead.
    const still = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // A second run of cards, identical to the first, makes the seam invisible:
    // when the scroll has travelled one whole set it is wound back by that same
    // amount, so the reader never sees a join. The clones carry the same data
    // indexes, so the popup maps them back to the payload with %.
    const duplicates = [...staffRail.querySelectorAll('li')].map(function (card) {
        return card.cloneNode(true);
    });

    // How fast the sweep travels, in pixels per millisecond (~200 px/s), and
    // the width of one copy of the cards, measured once the rail has loaded.
    const speed = 0.2;

    let singleSet = 0;
    let raf = 0;
    let last = 0;
    let held = false;
    let marqueeOn = false;

    // One card plus the gap, which is how far an arrow should carry the rail.
    const step = () => {
        const card = staffRail.querySelector('li');
        const gap = parseFloat(getComputedStyle(staffRail).columnGap) || 0;

        return card ? card.offsetWidth + gap : 320;
    };

    // The width of one run of cards, measured as the distance between the first
    // card and the first card of the duplicate run. It is re-measured after a
    // resize, because the cards can reflow.
    function measureSet() {
        const cards = staffRail.querySelectorAll('li');
        const half = cards.length / 2;

        singleSet = cards.length > 1 && cards[half]
            ? cards[half].offsetLeft - cards[0].offsetLeft
            : 0;
    }

    // One sweep step, driven by the frame delta rather than a fixed amount per
    // frame, so the speed holds no matter how the browser paces its frames.
    function tick(now) {
        const delta = now - last;
        last = now;

        staffRail.scrollLeft += speed * delta;

        // Wound back by exactly one set the moment the duplicate run reaches the
        // place the first run started from, so the position is always inside
        // real content and the browser never has to relayout.
        if (staffRail.scrollLeft >= singleSet) {
            staffRail.scrollLeft -= singleSet;
        }

        raf = requestAnimationFrame(tick);
    }

    // The sweep only runs when it can: motion is allowed, nothing holds the
    // rail, the cards overflow it, and there is more than one of them.
    function startMarquee() {
        if (marqueeOn) {
            return;
        }

        const overflowing = staffRail.scrollWidth - staffRail.clientWidth;

        // Nothing to sweep when the cards already fit, or when only one is there.
        if (still || held || staffRail.querySelectorAll('li').length < 2) {
            return;
        }

        if (overflowing <= step()) {
            return;
        }

        // The duplicate run goes in only while the sweep is on, so the still
        // rail underneath keeps exactly the cards the page shipped.
        duplicates.forEach(function (card) {
            staffRail.appendChild(card);
        });

        measureSet();
        marqueeOn = true;
        staffRail.classList.add('is-marquee');
        syncArrows();

        last = performance.now();
        raf = requestAnimationFrame(tick);
    }

    function stopMarquee() {
        if (!marqueeOn) {
            return;
        }

        cancelAnimationFrame(raf);
        marqueeOn = false;
        staffRail.classList.remove('is-marquee');

        duplicates.forEach(function (card) {
            card.remove();
        });

        measureSet();
        syncArrows();
    }

    function endOfRail() {
        return staffRail.scrollLeft >= staffRail.scrollWidth - staffRail.clientWidth - 1;
    }

    // The arrows are disabled at whichever end the rail is already sitting at,
    // so they never look broken and never scroll into empty space. While the
    // sweep is on there is no end to sit at, so they stay enabled and nudge the
    // run instead.
    function syncArrows() {
        if (marqueeOn) {
            return;
        }

        const max = staffRail.scrollWidth - staffRail.clientWidth;

        if (previous) {
            previous.disabled = staffRail.scrollLeft <= 1;
        }

        if (next) {
            next.disabled = max <= 1 || staffRail.scrollLeft >= max - 1;
        }
    }

    // The arrow path for the still rail: past the last card it wraps to the
    // front rather than stopping, so it never dead-ends on a disabled arrow.
    function move(distance) {
        if (distance > 0 && endOfRail()) {
            staffRail.scrollTo({ left: 0, behavior: 'smooth' });
        } else {
            staffRail.scrollBy({ left: distance, behavior: 'smooth' });
        }
    }

    // Held only while the rail is actually being moved by hand or covered by the
    // popup. The sweep is deliberately not tied to the cursor resting nearby:
    // a parked mouse must not look like a broken carousel.
    function hold() {
        held = true;

        if (marqueeOn) {
            cancelAnimationFrame(raf);
        }
    }

    function release() {
        held = false;

        if (marqueeOn) {
            last = performance.now();
            raf = requestAnimationFrame(tick);
        }
    }

    if (previous) {
        previous.addEventListener('click', function () {
            if (marqueeOn) {
                staffRail.scrollBy({ left: -step(), behavior: 'smooth' });
                return;
            }

            move(-step());
        });
    }

    if (next) {
        next.addEventListener('click', function () {
            if (marqueeOn) {
                staffRail.scrollBy({ left: step(), behavior: 'smooth' });
                return;
            }

            move(step());
        });
    }

    staffRail.addEventListener('scroll', syncArrows, { passive: true });
    window.addEventListener('resize', function () {
        if (marqueeOn) {
            measureSet();

            if (staffRail.scrollWidth - staffRail.clientWidth <= step()) {
                stopMarquee();
            }
        } else {
            startMarquee();
        }

        syncArrows();
    });

    // Nothing should move behind a tab nobody is looking at.
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            hold();
        } else {
            release();
        }
    });

    syncArrows();
    startMarquee();

    /* Dragging with a mouse. A touch is deliberately left to the browser, which
       already pans the rail natively and does it with the right inertia. The
       threshold matters twice over: past it the rail stops snapping so it
       follows the pointer, and a drag must not read as a click on a card. */

    let dragging = false;
    let moved = 0;
    let startX = 0;
    let startScroll = 0;
    let swallowClick = false;

    staffRail.addEventListener('pointerdown', function (event) {
        if (event.pointerType === 'touch' || event.button !== 0) {
            return;
        }

        dragging = true;
        moved = 0;
        startX = event.clientX;
        startScroll = staffRail.scrollLeft;

        staffRail.classList.add('is-dragging');

        // A hand on the rail outranks the sweep.
        hold();
    });

    staffRail.addEventListener('pointermove', function (event) {
        if (!dragging) {
            return;
        }

        const distance = event.clientX - startX;

        moved = Math.max(moved, Math.abs(distance));

        if (moved > 3) {
            // Capture keeps the drag alive when the pointer leaves the rail, but
            // it throws if the pointer is already gone (released off-window),
            // which must not abort the scroll below.
            try {
                staffRail.setPointerCapture(event.pointerId);
            } catch {
                // Dragging still works without the capture.
            }
        }

        staffRail.scrollLeft = startScroll - distance;
    });

    function stopDragging() {
        if (!dragging) {
            return;
        }

        dragging = false;
        staffRail.classList.remove('is-dragging');

        swallowClick = moved > 6;

        release();
    }

    staffRail.addEventListener('pointerup', stopDragging);
    staffRail.addEventListener('pointercancel', stopDragging);
    staffRail.addEventListener('lostpointercapture', stopDragging);

    /* Profile dialog. The card triggers are plain buttons laid over the cards,
       and one dialog is filled from the JSON the section rendered, so the popup
       can never disagree with the card it was opened from. */

    const dialogName = staffModal.querySelector('[data-staff-modal-name]');
    const dialogPosition = staffModal.querySelector('[data-staff-modal-position]');
    const dialogDescription = staffModal.querySelector('[data-staff-modal-description]');
    const dialogExpertise = staffModal.querySelector('[data-staff-modal-expertise]');
    const dialogPhoto = staffModal.querySelector('[data-staff-modal-photo]');
    const dialogClose = staffModal.querySelector('[data-staff-close]');

    let opener = null;
    let hovered = -1;

    // Filling the panel is shared by both ways in: pointing at a card and
    // activating it with the keyboard end up in the same dialog.
    function fillProfile(index) {
        const person = staffData[index % staffData.length];

        if (!person) {
            return null;
        }

        dialogName.textContent = person.name;
        dialogPosition.textContent = person.position;
        dialogDescription.textContent = person.description;

        dialogExpertise.textContent = '';

        person.expertise.forEach(function (label) {
            const item = document.createElement('li');

            item.textContent = label;
            dialogExpertise.appendChild(item);
        });

        dialogPhoto.textContent = '';

        if (person.photo) {
            const image = document.createElement('img');

            image.src = person.photo;
            image.alt = 'Foto ' + person.name;

            dialogPhoto.appendChild(image);
        } else {
            const monogram = document.createElement('span');

            monogram.className = 'staff-monogram';
            monogram.setAttribute('aria-hidden', 'true');
            monogram.textContent = person.initials;

            dialogPhoto.appendChild(monogram);
        }

        return person;
    }

    /* Pointing at a card stops the drift and shows that card straight away.

       The dialog is opened with show(), not showModal(): a modal dialog makes
       the rest of the page inert, so the pointer could not travel on to the
       next card and the rail would be stuck on one profile. A non-modal dialog
       still gets the top layer and the Escape key, and it leaves the rail
       hoverable - which is the whole point here. */

    function showHover(index) {
        fillProfile(index);

        // Shown first, then measured and moved: a closed dialog has no box to
        // measure, and both happen in the same task so nothing is ever painted
        // in the wrong place.
        if (!staffModal.open) {
            staffModal.show();
        }

        const card = staffRail.querySelector('li:nth-child(' + (index + 1) + ')');

        if (card) {
            const box = card.getBoundingClientRect();
            const panel = staffModal.offsetWidth;
            const tall = staffModal.offsetHeight;
            const margin = 16;

            // Beside the card rather than under the cursor, so the pointer stays
            // on the card it is reading about. Flipped to the far side when
            // there is no room on this one, and only then dropped underneath -
            // never allowed to cover the card, or the card stops being hovered
            // and the whole thing flickers.
            let left = box.right + 12;
            let top = box.top;
            const flipped = box.left - 12 - panel;

            if (left + panel > window.innerWidth - margin) {
                if (flipped >= margin) {
                    left = flipped;
                } else {
                    left = Math.max(margin, Math.min(box.left, window.innerWidth - panel - margin));
                    top = box.bottom + 12;

                    if (top + tall > window.innerHeight - margin) {
                        top = box.top - 12 - tall;
                    }
                }
            }

            staffModal.style.left = Math.round(
                Math.max(margin, Math.min(left, window.innerWidth - panel - margin)),
            ) + 'px';
            staffModal.style.top = Math.round(Math.max(
                margin,
                Math.min(top, window.innerHeight - tall - margin),
            )) + 'px';
        }
    }

    staffRail.addEventListener('pointerover', function (event) {
        const card = event.target.closest('li');

        if (!card || !staffRail.contains(card) || dragging) {
            return;
        }

        const index = Array.prototype.indexOf.call(card.parentNode.children, card);

        // One card, one open: sliding along the rail refills the same dialog
        // instead of stacking popups.
        if (index === hovered && staffModal.open) {
            return;
        }

        hovered = index;

        // A hand on a card outranks the drift.
        hold();
        showHover(index);
    });

    // The rail and the dialog are one hover group, so moving between them keeps
    // the profile up; leaving both closes it and the drift carries on.
    function closeOnLeave(event) {
        if (event.relatedTarget && (staffRail.contains(event.relatedTarget) || staffModal.contains(event.relatedTarget))) {
            return;
        }

        hovered = -1;

        if (staffModal.open) {
            staffModal.close();
        }
    }

    staffRail.addEventListener('pointerout', closeOnLeave);
    staffModal.addEventListener('pointerout', closeOnLeave);

    // Keyboard and touch have no hover, so the button on each card still opens
    // the profile - modally, so focus is trapped and Escape closes it.
    function openProfile(index, trigger) {
        if (!fillProfile(index)) {
            return;
        }

        hovered = -1;

        if (staffModal.open) {
            staffModal.close();
        }

        staffModal.style.left = '';
        staffModal.style.top = '';

        // The card that opened the dialog, not whatever happened to be focused:
        // a scripted open has no mousedown to move focus, so reading
        // document.activeElement would hand focus back to the body.
        opener = trigger;

        // showModal() lifts the dialog to the top layer and makes the rest of
        // the page inert, which is also what keeps Tab inside the dialog. The
        // class is only there to stop the page behind from scrolling.
        document.documentElement.classList.add('staff-modal-open');
        staffModal.showModal();

        // A popup in the way means the drift waits.
        hold();
    }

    function closeProfile() {
        if (staffModal.open) {
            staffModal.close();
        }
    }

    staffRail.addEventListener('click', function (event) {
        const trigger = event.target.closest('[data-staff-open]');

        if (!trigger) {
            return;
        }

        // The click that ends a drag is not a click on the card.
        if (swallowClick) {
            swallowClick = false;

            return;
        }

        openProfile(Number(trigger.dataset.staffIndex), trigger);
    });

    dialogClose.addEventListener('click', closeProfile);

    // The dialog element is the full-viewport layer, so a click on the shaded
    // area lands on the dialog itself rather than on the panel inside it.
    staffModal.addEventListener('click', function (event) {
        if (event.target === staffModal) {
            closeProfile();
        }
    });

    // <dialog> already closes on Escape; handling it here as well costs
    // nothing and keeps the intent obvious at the call site.
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && staffModal.open) {
            closeProfile();
        }
    });

    // Whatever closed it - the button, the overlay, Escape, the pointer leaving -
    // focus goes back to the card that opened it.
    staffModal.addEventListener('close', function () {
        // close() queues its event, so a reopen straight afterwards lets this
        // fire for a dialog that is open again. Releasing here would start the
        // drift behind a profile that is still on screen.
        if (staffModal.open) {
            return;
        }

        document.documentElement.classList.remove('staff-modal-open');
        hovered = -1;

        if (opener && typeof opener.focus === 'function') {
            opener.focus();
        }

        opener = null;

        release();
    });
}