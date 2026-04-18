import { TabulatorFull as Tabulator } from 'tabulator-tables';
import 'tabulator-tables/dist/css/tabulator.min.css';

function occurrenceBrowser() {
    return {
        table: null,

        init() {
            this.table = new Tabulator('#eonmap-browser-table', {
                layout: 'fitColumns',
                data: [],
                movableColumns: true,
                responsiveLayout: 'hide',
                columns: [
                    {
                        title: 'Taxon',
                        field: 'accepted_name',
                        sorter: 'string',
                        formatter: (cell) => {
                            const row = cell.getRow().getData();
                            return `<a href="/occurrences/${row.occurrence_no}" style="color:var(--color-accent)">${cell.getValue() ?? ''}</a>`;
                        },
                        headerClick: (e, col) => {
                            this.$wire.call('setSort', col.getField());
                        },
                    },
                    {
                        title: 'Rank',
                        field: 'accepted_rank',
                        sorter: 'string',
                        headerClick: (e, col) => {
                            this.$wire.call('setSort', col.getField());
                        },
                    },
                    {
                        title: 'Age (Early)',
                        field: 'early_interval',
                    },
                    {
                        title: 'Max Ma',
                        field: 'max_ma',
                        sorter: 'number',
                        headerClick: (e, col) => {
                            this.$wire.call('setSort', col.getField());
                        },
                    },
                    {
                        title: 'Min Ma',
                        field: 'min_ma',
                        sorter: 'number',
                        headerClick: (e, col) => {
                            this.$wire.call('setSort', col.getField());
                        },
                    },
                    {
                        title: 'Country',
                        field: 'country',
                        sorter: 'string',
                        headerClick: (e, col) => {
                            this.$wire.call('setSort', col.getField());
                        },
                    },
                    {
                        title: 'State',
                        field: 'state',
                    },
                    {
                        title: 'Formation',
                        field: 'formation',
                    },
                    {
                        title: 'Environment',
                        field: 'environment',
                        sorter: 'string',
                        headerClick: (e, col) => {
                            this.$wire.call('setSort', col.getField());
                        },
                    },
                ],
            });
        },

        /**
         * Called when the browser-data-loaded browser event fires.
         * Replaces the table's dataset.
         *
         * @param {Array} occurrences
         */
        setTableData(occurrences) {
            if (this.table) {
                this.table.setData(occurrences ?? []);
            }
        },
    };
}

document.addEventListener('alpine:init', () => {
    window.Alpine.data('occurrenceBrowser', occurrenceBrowser);
});