
(function () {
    'use strict';

    function formatPrice(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    }

    function initializeCalculator(card) {
        const addon = card.querySelector('.ropstam-sac__addon-input');
        const totalElement = card.querySelector('.ropstam-sac__total-price');
        const deliveryElement = card.querySelector('.ropstam-sac__delivery');
        const basePrice = Number(card.dataset.basePrice);
        const baseDays = Number(card.dataset.baseDays);

        if (!totalElement || !Number.isFinite(basePrice) || !Number.isFinite(baseDays)) {
            return;
        }

        function getSelection() {
            const selected = addon && addon.checked;
            const extraPrice = selected ? Number(addon.dataset.addonPrice) : 0;
            const daysSaved = selected ? Number(addon.dataset.daysSaved) : 0;

            return {
                price: basePrice + (
                    Number.isFinite(extraPrice) && extraPrice >= 0 ? extraPrice : 0
                ),
                days: Math.max(
                    0,
                    baseDays - (
                        Number.isFinite(daysSaved) && daysSaved >= 0 ? daysSaved : 0
                    )
                ),
                priority: Boolean(selected)
            };
        }

        function updateTotal() {
            const selection = getSelection();

            totalElement.textContent = formatPrice(selection.price);

            if (deliveryElement) {
                deliveryElement.textContent = selection.days +
                    (selection.days === 1 ? ' day' : ' days');
            }
        }

        if (addon) {
            addon.addEventListener('change', updateTotal);
        }

        card.querySelector('.ropstam-sac__button').addEventListener('click', function () {
            openDemoModal(card, getSelection());
        });

        updateTotal();
    }

    function openDemoModal(card, selection) {
        const root = card.closest('.ropstam-sac');
        const modal = root && root.querySelector('.ropstam-sac__modal');

        if (!modal) {
            return;
        }

        const dialog = modal.querySelector('.ropstam-sac__modal-dialog');
        const closeButtons = modal.querySelectorAll('[data-modal-close]');
        const okButton = modal.querySelector('.ropstam-sac__modal-ok');

        modal.querySelector('.ropstam-sac__modal-service').textContent =
            card.dataset.serviceTitle || 'Selected service';

        modal.querySelector('.ropstam-sac__modal-price').textContent =
            formatPrice(selection.price);

        modal.querySelector('.ropstam-sac__modal-delivery').textContent =
            selection.days + (selection.days === 1 ? ' day' : ' days');

        modal.querySelector('.ropstam-sac__modal-priority').textContent =
            selection.priority ? 'Selected' : 'Not selected';

        const previouslyFocused = document.activeElement;

        function closeModal() {
            modal.hidden = true;
            document.removeEventListener('keydown', handleKeydown);

            if (previouslyFocused && typeof previouslyFocused.focus === 'function') {
                previouslyFocused.focus();
            }
        }

        function handleKeydown(event) {
            if (event.key === 'Escape') {
                closeModal();
            }

            if (event.key === 'Tab') {
                const focusable = [ ...modal.querySelectorAll(
                    'button:not([disabled]), [href], input:not([disabled]), [tabindex="0"]'
                ) ].filter(function (element) {
                    return element.offsetParent !== null;
                });

                if (!focusable.length) {
                    return;
                }

                const first = focusable[0];
                const last = focusable[focusable.length - 1];

                if (event.shiftKey && document.activeElement === first) {
                    event.preventDefault();
                    last.focus();
                } else if (!event.shiftKey && document.activeElement === last) {
                    event.preventDefault();
                    first.focus();
                }
            }
        }

        closeButtons.forEach(function (button) {
            button.onclick = closeModal;
        });

        okButton.onclick = closeModal;

        modal.hidden = false;
        dialog.focus();
        document.addEventListener('keydown', handleKeydown);
    }

    function initializeAllCalculators() {
        document.querySelectorAll('.ropstam-sac__card').forEach(initializeCalculator);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeAllCalculators);
    } else {
        initializeAllCalculators();
    }
})();
