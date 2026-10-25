const staffSection = document.querySelector('[data-staff-data]');

if (staffSection) {
    const people = JSON.parse(staffSection.textContent);
    const staffRoot = staffSection.closest('#tenaga-kependidikan');
    const staffList = staffRoot.querySelector('[data-staff-rail]');
    const preview = staffRoot.querySelector('[data-staff-preview]');
    const previewPhoto = preview.querySelector('[data-staff-preview-photo]');
    const previewName = preview.querySelector('[data-staff-preview-name]');
    const previewPosition = preview.querySelector('[data-staff-preview-position]');
    const previewDescription = preview.querySelector('[data-staff-preview-description]');
    const dialog = staffRoot.querySelector('[data-staff-modal]');
    const dialogPanel = dialog.querySelector('[data-staff-panel]');
    const dialogPhoto = dialog.querySelector('[data-staff-modal-photo]');
    const dialogName = dialog.querySelector('[data-staff-modal-name]');
    const dialogPosition = dialog.querySelector('[data-staff-modal-position]');
    const dialogDescription = dialog.querySelector('[data-staff-modal-description]');
    const dialogExpertise = dialog.querySelector('[data-staff-modal-expertise]');
    const dialogCount = dialog.querySelector('[data-staff-count]');
    const closeButton = dialog.querySelector('[data-staff-close]');
    const previousButton = dialog.querySelector('[data-staff-prev]');
    const nextButton = dialog.querySelector('[data-staff-next]');
    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    let previewIndex = -1;
    let currentIndex = -1;
    let opener = null;
    let cursorX = 0;
    let cursorY = 0;
    let targetX = 0;
    let targetY = 0;
    let pointerAngle = 0;
    let frame = 0;
    let dragStartY = null;

    function updateImage(target, person) {
        target.replaceChildren();
        target.style.setProperty('--tk-accent', person.accent);

        if (person.photo) {
            const image = document.createElement('img');
            image.src = person.photo;
            image.alt = 'Foto ' + person.name;
            image.loading = 'lazy';
            target.appendChild(image);

            return;
        }

        const initials = document.createElement('span');
        initials.setAttribute('aria-hidden', 'true');
        initials.textContent = person.initials;
        target.appendChild(initials);
    }

    function showPreview(index, row) {
        const person = people[index];

        if (!person || dialog.open) {
            return;
        }

        previewIndex = index;
        staffList.dataset.previewIndex = String(index);
        staffList.querySelectorAll('[data-staff-row]').forEach(function (item) {
            item.toggleAttribute('data-active', item === row);
        });
        updateImage(previewPhoto, person);
        previewName.textContent = person.name;
        previewPosition.textContent = person.position;
        previewDescription.textContent = person.description;
        preview.style.setProperty('--tk-accent', person.accent);
        preview.dataset.visible = 'true';
    }

    function hidePreview() {
        previewIndex = -1;
        delete staffList.dataset.previewIndex;
        staffList.querySelectorAll('[data-active]').forEach(function (row) {
            row.removeAttribute('data-active');
        });
        delete preview.dataset.visible;
    }

    function placePreview() {
        frame = 0;

        if (previewIndex < 0) {
            return;
        }

        const box = preview.getBoundingClientRect();
        const margin = 12;
        const x = Math.max(margin, Math.min(targetX, window.innerWidth - box.width - margin));
        const y = Math.max(margin, Math.min(targetY, window.innerHeight - box.height - margin));

        if (reducedMotion) {
            cursorX = x;
            cursorY = y;
        } else {
            cursorX += (x - cursorX) * 0.28;
            cursorY += (y - cursorY) * 0.28;
        }

        preview.style.transform = `translate3d(${Math.round(cursorX)}px, ${Math.round(cursorY)}px, 0) rotate(${pointerAngle}deg)`;

        if (Math.abs(cursorX - x) > 0.5 || Math.abs(cursorY - y) > 0.5) {
            frame = window.requestAnimationFrame(placePreview);
        }
    }

    function schedulePreview() {
        if (!frame) {
            frame = window.requestAnimationFrame(placePreview);
        }
    }

    function placeBesideRow(row) {
        const box = row.getBoundingClientRect();
        const previewBox = preview.getBoundingClientRect();
        const margin = 12;
        let x = box.right + 18;
        let y = box.top + (box.height - previewBox.height) / 2;

        if (x + previewBox.width > window.innerWidth - margin) {
            x = box.left - previewBox.width - 18;
        }

        targetX = Math.max(margin, Math.min(x, window.innerWidth - previewBox.width - margin));
        targetY = Math.max(margin, Math.min(y, window.innerHeight - previewBox.height - margin));
        cursorX = targetX;
        cursorY = targetY;
        schedulePreview();
    }

    function fillProfile(index) {
        const person = people[index];

        if (!person) {
            return false;
        }

        currentIndex = index;
        dialogPhoto.style.setProperty('--tk-accent', person.accent);
        updateImage(dialogPhoto, person);
        dialogName.textContent = person.name;
        dialogPosition.textContent = person.position;
        dialogPosition.style.setProperty('--tk-accent', person.accent);
        dialogDescription.textContent = person.description;
        dialogExpertise.replaceChildren();
        person.expertise.forEach(function (skill) {
            const item = document.createElement('li');
            item.textContent = skill;
            dialogExpertise.appendChild(item);
        });
        dialogCount.textContent = `${String(index + 1).padStart(2, '0')} / ${String(people.length).padStart(2, '0')}`;

        return true;
    }

    function openProfile(index, trigger) {
        if (!fillProfile(index)) {
            return;
        }

        hidePreview();
        opener = trigger;
        document.documentElement.classList.add('tk-modal-open');
        dialog.showModal();
        closeButton.focus();
    }

    function closeProfile() {
        if (dialog.open) {
            dialog.close();
        }
    }

    function moveProfile(step) {
        const nextIndex = (currentIndex + step + people.length) % people.length;
        fillProfile(nextIndex);
    }

    if (finePointer) {
        staffList.addEventListener('pointerover', function (event) {
            if (event.pointerType !== 'mouse' && event.pointerType !== 'pen') {
                return;
            }

            const row = event.target.closest('[data-staff-row]');

            if (!row || !staffList.contains(row)) {
                return;
            }

            const trigger = row.querySelector('[data-staff-open]');
            const index = Number(trigger.dataset.staffIndex);

            if (previewIndex !== index) {
                showPreview(index, row);
            }
        });

        staffList.addEventListener('pointermove', function (event) {
            if (previewIndex < 0 || (event.pointerType !== 'mouse' && event.pointerType !== 'pen')) {
                return;
            }

            const deltaX = event.clientX - cursorX;
            cursorX = event.clientX;
            cursorY = event.clientY;
            targetX = event.clientX + 20;
            targetY = event.clientY + 18;
            pointerAngle = reducedMotion ? 0 : Math.max(-1.5, Math.min(deltaX * 0.04, 1.5));
            schedulePreview();
        });

        staffList.addEventListener('pointerout', function (event) {
            if (event.relatedTarget && staffList.contains(event.relatedTarget)) {
                return;
            }

            hidePreview();
        });
    }

    staffList.addEventListener('focusin', function (event) {
        const trigger = event.target.closest('[data-staff-open]');

        if (!trigger || !trigger.matches(':focus-visible')) {
            return;
        }

        const row = trigger.closest('[data-staff-row]');
        showPreview(Number(trigger.dataset.staffIndex), row);
        placeBesideRow(row);
    });

    staffList.addEventListener('focusout', function (event) {
        if (event.relatedTarget && staffList.contains(event.relatedTarget)) {
            return;
        }

        hidePreview();
    });

    staffList.addEventListener('click', function (event) {
        const trigger = event.target.closest('[data-staff-open]');

        if (trigger) {
            openProfile(Number(trigger.dataset.staffIndex), trigger);
        }
    });

    previousButton.addEventListener('click', function () {
        moveProfile(-1);
    });

    nextButton.addEventListener('click', function () {
        moveProfile(1);
    });

    closeButton.addEventListener('click', closeProfile);

    dialog.addEventListener('click', function (event) {
        if (event.target === dialog) {
            closeProfile();
        }
    });

    dialog.addEventListener('close', function () {
        document.documentElement.classList.remove('tk-modal-open');
        dragStartY = null;

        if (opener && opener.isConnected) {
            opener.focus();
        }

        opener = null;
    });

    document.addEventListener('keydown', function (event) {
        if (!dialog.open) {
            return;
        }

        if (event.key === 'Escape') {
            event.preventDefault();
            closeProfile();
        } else if (event.key === 'ArrowLeft') {
            event.preventDefault();
            moveProfile(-1);
        } else if (event.key === 'ArrowRight') {
            event.preventDefault();
            moveProfile(1);
        }
    });

    dialogPanel.addEventListener('pointerdown', function (event) {
        if (event.pointerType === 'touch' && event.target.closest('.tk-modal-portrait')) {
            dragStartY = event.clientY;
        }
    });

    dialogPanel.addEventListener('pointerup', function (event) {
        if (dragStartY === null) {
            return;
        }

        const swipeDistance = event.clientY - dragStartY;
        dragStartY = null;

        if (swipeDistance > 80) {
            closeProfile();
        }
    });

    dialogPanel.addEventListener('pointercancel', function () {
        dragStartY = null;
    });

    window.addEventListener('resize', schedulePreview);
}
