import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import { registerThemeStore } from './theme.js';
import './map.js';
import './browse.js';
import './occurrence.js';
import './taxon.js';

window.Alpine = Alpine;
registerThemeStore(Alpine);
Livewire.start();
