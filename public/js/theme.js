(function () {
    'use strict';

    var storageKey = 'helpdesk-theme';
    var root = document.documentElement;

    function applyTheme(theme) {
        var selectedTheme = theme === 'light' ? 'light' : 'dark';
        root.dataset.theme = selectedTheme;
        root.dataset.bsTheme = selectedTheme;
        window.dispatchEvent(new CustomEvent('helpdesk:theme-changed', { detail: { theme: selectedTheme } }));

        document.querySelectorAll('[data-theme-toggle]').forEach(function (button) {
            var isDark = selectedTheme === 'dark';
            var label = isDark ? 'Switch to light theme' : 'Switch to dark theme';
            button.setAttribute('aria-label', label);
            button.setAttribute('title', label);
            button.setAttribute('aria-pressed', String(isDark));
            button.querySelector('[data-theme-label]').textContent = isDark ? 'Light theme' : 'Dark theme';
            button.querySelector('[data-theme-icon]').className = isDark ? 'bi bi-sun' : 'bi bi-moon-stars';
        });
    }

    var initialTheme = 'dark';
    try {
        initialTheme = localStorage.getItem(storageKey);
    } catch (error) {
        // Theme selection still works when browser storage is unavailable.
    }
    applyTheme(initialTheme);

    document.addEventListener('DOMContentLoaded', function () {
        applyTheme(root.dataset.theme);
        document.querySelectorAll('[data-theme-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                var nextTheme = root.dataset.theme === 'dark' ? 'light' : 'dark';
                applyTheme(nextTheme);
                try {
                    localStorage.setItem(storageKey, nextTheme);
                } catch (error) {
                    // The page still updates without persistent storage.
                }
            });
        });
    });

    window.addEventListener('storage', function (event) {
        if (event.key === storageKey || event.key === null) {
            applyTheme(event.newValue);
        }
    });
}());
