export function taxaSearch() {
    return {
        query: '',
        results: [],
        selected: null,
        loading: false,
        dropdownOpen: false,
        debounceTimer: null,

        onInput() {
            this.selected = null;
            clearTimeout(this.debounceTimer);

            if (this.query.length < 2) {
                this.results = [];
                this.dropdownOpen = false;
                return;
            }

            this.debounceTimer = setTimeout(() => this.fetch(), 300);
        },

        async fetch() {
            this.loading = true;
            this.dropdownOpen = false;

            try {
                const res = await fetch(`/taxa/search?q=${encodeURIComponent(this.query)}`);
                this.results = await res.json();
                this.dropdownOpen = this.results.length > 0;
            } catch {
                this.results = [];
            } finally {
                this.loading = false;
            }
        },

        select(taxon) {
            this.selected = taxon;
            this.query = taxon.name;
            this.results = [];
            this.dropdownOpen = false;
        },

        clear() {
            this.query = '';
            this.results = [];
            this.selected = null;
            this.dropdownOpen = false;
        },

        taxonUrl(name) {
            return `/taxa/${encodeURIComponent(name)}`;
        },
    };
}