import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import { registerThemeStore } from './theme.js';
import { taxaSearch } from './taxa-search.js';
import './map.js';
import './browse.js';
import './occurrence.js';
import './taxon.js';

Alpine.data('taxaSearch', taxaSearch);

window.Alpine = Alpine;
registerThemeStore(Alpine);
Livewire.start();
