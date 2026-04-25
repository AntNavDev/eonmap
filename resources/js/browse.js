import { TabulatorFull as Tabulator } from 'tabulator-tables';
import 'tabulator-tables/dist/css/tabulator.min.css';

function occurrenceBrowser() {
    return {
        table: null,

        init() {
            this.table = new Tabulator('#eonmap-browser-table', {
                layout: 'fitDataFill',
                data: [],
                movableColumns: true,
                columns: [
                    {
                        title: 'Taxon',
                        field: 'accepted_name',
                        sorter: 'string',
                        formatter: (cell) => {
                            const row = cell.getRow().getData();
                            return `<a href="/occurrences/${row.occurrence_no}" style="color:var(--color-accent)">${cell.getValue() ?? ''}</a>`;
                        },
                    },
                    { title: 'Rank',        field: 'accepted_rank',  sorter: 'string' },
                    { title: 'Age (Early)', field: 'early_interval' },
                    { title: 'Max Age (Mya)', field: 'max_ma',       sorter: 'number' },
                    { title: 'Min Age (Mya)', field: 'min_ma',       sorter: 'number' },
                    { title: 'Country',     field: 'country',        sorter: 'string' },
                    { title: 'State',       field: 'state' },
                    { title: 'Formation',   field: 'formation' },
                    { title: 'Environment', field: 'environment',    sorter: 'string' },
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