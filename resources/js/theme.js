export function registerThemeStore(Alpine) {
    Alpine.store('theme', {
        current: localStorage.getItem('eonmap-theme')
            ?? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),

        init() {
            this.apply(this.current);
        },

        toggle() {
            this.current = this.current === 'dark' ? 'light' : 'dark';
            localStorage.setItem('eonmap-theme', this.current);
            this.apply(this.current);
        },

        apply(theme) {
            document.documentElement.setAttribute('data-theme', theme);
        },
    });
}
