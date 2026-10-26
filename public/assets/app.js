(() => {
    const body = document.body;
    const sidebarButton = document.querySelector('[data-sidebar-open]');
    const sidebar = document.querySelector('#sidebar');
    const openSidebar = () => {
        body.classList.add('sidebar-open');
        sidebarButton?.setAttribute('aria-expanded', 'true');
        sidebar?.querySelector('a')?.focus();
    };
    const closeSidebar = () => {
        body.classList.remove('sidebar-open');
        sidebarButton?.setAttribute('aria-expanded', 'false');
    };
    sidebarButton?.addEventListener('click', openSidebar);
    document.querySelectorAll('[data-sidebar-close]').forEach((button) => button.addEventListener('click', () => {
        closeSidebar();
        sidebarButton?.focus();
    }));

    document.querySelectorAll('[data-alert-close]').forEach((button) => {
        button.addEventListener('click', () => button.closest('[data-alert]')?.remove());
    });

    const dialog = document.querySelector('#delete-dialog');
    const dialogName = document.querySelector('#delete-menu-name');
    const confirmButton = document.querySelector('#confirm-delete');
    let pendingForm = null;

    document.querySelectorAll('.delete-form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.confirmed === 'true') return;
            event.preventDefault();
            if (!dialog || typeof dialog.showModal !== 'function') {
                if (window.confirm(`Hapus menu “${form.dataset.menuName}” secara permanen?`)) {
                    form.dataset.confirmed = 'true';
                    form.requestSubmit();
                }
                return;
            }
            pendingForm = form;
            if (dialogName) dialogName.textContent = form.dataset.menuName || 'Menu ini';
            dialog.showModal();
        });
    });

    confirmButton?.addEventListener('click', () => {
        if (!pendingForm) return;
        pendingForm.dataset.confirmed = 'true';
        dialog?.close();
        pendingForm.requestSubmit();
        pendingForm = null;
    });

    dialog?.addEventListener('click', (event) => {
        if (event.target !== dialog) return;
        const rect = dialog.getBoundingClientRect();
        const inside = event.clientX >= rect.left && event.clientX <= rect.right && event.clientY >= rect.top && event.clientY <= rect.bottom;
        if (!inside) dialog.close();
    });
    dialog?.addEventListener('close', () => { pendingForm = null; });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && body.classList.contains('sidebar-open')) {
            closeSidebar();
            sidebarButton?.focus();
        }
    });
})();
