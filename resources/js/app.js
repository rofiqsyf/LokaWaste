import Alpine from 'alpinejs';
import timeRing from './time-ring';
import './bootstrap';

window.Alpine = Alpine;
Alpine.data('timeRing', timeRing);
Alpine.start();
