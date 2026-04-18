import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import { registerThemeStore } from './theme.js';

window.Alpine = Alpine;
registerThemeStore(Alpine);
Livewire.start();