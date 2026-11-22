import Alpine from 'alpinejs';
import timeRing from './time-ring';
import dashboardAnalytics from './dashboard-analytics';
import qrHandover from './qr-handover';
import './bootstrap';
import { Chart, registerables } from 'chart.js';
import QRCode from 'qrcode';
import { Html5Qrcode, Html5QrcodeScanner } from 'html5-qrcode';

Chart.register(...registerables);

window.Chart = Chart;
window.QRCode = QRCode;
window.Html5Qrcode = Html5Qrcode;
window.Html5QrcodeScanner = Html5QrcodeScanner;

window.Alpine = Alpine;
Alpine.data('timeRing', timeRing);
Alpine.data('dashboardAnalytics', dashboardAnalytics);
Alpine.data('qrHandover', qrHandover);
Alpine.start();
