import './bootstrap';

// Schliesst jeden <dialog> per Klick auf den Backdrop (native <dialog>-Elemente tun das nicht von selbst).
document.addEventListener('click', (event) => {
    if (event.target instanceof HTMLDialogElement && event.target.open) {
        event.target.close();
    }
});
