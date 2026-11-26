export default (expiresAt, serverNow, totalMinutes, tzLabel) => ({
    offset: new Date(serverNow) - Date.now(), // koreksi selisih jam perangkat
    remainingMs: 0,
    timer: null,

    init() {
        this.tick();
        this.timer = setInterval(() => this.tick(), 60000);
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) this.tick();
        });
    },

    destroy() {
        if (this.timer) clearInterval(this.timer);
    },

    tick() {
        const was = this.remainingMs;
        this.remainingMs = Math.max(0, new Date(expiresAt) - (Date.now() + this.offset));
        if (was > 0 && this.remainingMs === 0) {
            this.$dispatch('listing:expired');
        }
    },

    get minutes() {
        return Math.ceil(this.remainingMs / 60000);
    },

    get expired() {
        return this.remainingMs === 0;
    },

    get percent() {
        return this.expired ? 0 : Math.max(6, Math.min(100, (this.minutes / (totalMinutes || 1)) * 100));
    },

    get color() {
        if (this.expired) return 'var(--color-ink-soft)';
        if (this.minutes < 60) return 'var(--color-time-urgent)';
        if (this.minutes <= 180) return 'var(--color-time-warn)';
        return 'var(--color-time-ok)';
    },

    get hours() {
        return Math.floor(Math.max(0, Math.round(Number(this.minutes) || 0)) / 60);
    },

    get mins() {
        return Math.max(0, Math.round(Number(this.minutes) || 0)) % 60;
    },

    get short() {
        if (this.expired) return 'Habis';
        const mins = Math.max(0, Math.round(Number(this.minutes) || 0));
        const h = Math.floor(mins / 60);
        const m = mins % 60;
        return h >= 1 ? (m ? `${h}j ${m}m` : `${h}j`) : `${m}m`;
    },

    get label() {
        if (this.expired) return 'Waktu ambil sudah berakhir';
        const mins = Math.max(0, Math.round(Number(this.minutes) || 0));
        const h = Math.floor(mins / 60);
        const m = mins % 60;
        return `Sisa ${h ? h + ' jam ' : ''}${m ? m + ' menit' : ''}, batas pukul ${this.deadline} ${tzLabel || 'WIB'}`.trim();
    },

    get deadline() {
        try {
            return new Date(expiresAt).toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                timeZone: this.$el.dataset.tz || 'Asia/Jakarta',
            });
        } catch (e) {
            return new Date(expiresAt).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }
    },
});
