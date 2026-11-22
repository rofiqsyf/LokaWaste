/**
 * Smart QR Code & In-Browser Camera Scanner Manager
 * Handles dynamic QR generation and real-time camera scanning for pickup verification
 */
export default function qrHandover() {
    return {
        scannerOpen: false,
        qrModalOpen: false,
        activeQr: {
            claimId: null,
            code: '',
            title: '',
            portions: 1,
            dataUrl: '',
        },
        manualCode: '',
        scanningLoading: false,
        scanSuccessMessage: null,
        scanErrorMessage: null,
        html5QrCode: null,
        cameraActive: false,

        init() {
            // Render any QR codes that are placed on the page
            this.$nextTick(() => {
                this.renderAllPageQrs();
            });
        },

        renderAllPageQrs() {
            if (!window.QRCode) return;

            const qrHolders = document.querySelectorAll('[data-locabite-qr]');
            qrHolders.forEach(el => {
                const payload = el.getAttribute('data-locabite-qr');
                if (payload) {
                    window.QRCode.toDataURL(payload, {
                        width: 180,
                        margin: 1,
                        color: {
                            dark: '#14573A',
                            light: '#FFFFFF'
                        }
                    }, (err, url) => {
                        if (!err && url) {
                            el.innerHTML = `<img src="${url}" alt="QR Code Serah Terima" class="w-full h-auto rounded-xl border border-emerald-200/80 shadow-2xs">`;
                        }
                    });
                }
            });
        },

        openQrEnlarged(claimId, code, title, portions) {
            const payload = `LOCABITE:CLAIM:${claimId}:${code}`;
            this.activeQr = {
                claimId,
                code,
                title,
                portions,
                dataUrl: ''
            };
            this.qrModalOpen = true;

            if (window.QRCode) {
                window.QRCode.toDataURL(payload, {
                    width: 320,
                    margin: 1.5,
                    color: {
                        dark: '#0F3B24',
                        light: '#FFFFFF'
                    }
                }, (err, url) => {
                    if (!err && url) {
                        this.activeQr.dataUrl = url;
                    }
                });
            }
        },

        closeQrEnlarged() {
            this.qrModalOpen = false;
        },

        openScanner() {
            this.scannerOpen = true;
            this.scanSuccessMessage = null;
            this.scanErrorMessage = null;
            this.manualCode = '';

            this.$nextTick(() => {
                this.startCameraScanner();
            });
        },

        closeScanner() {
            this.stopCameraScanner();
            this.scannerOpen = false;
        },

        async startCameraScanner() {
            if (!window.Html5Qrcode) {
                this.scanErrorMessage = "Komponen scanner kamera belum termuat. Silakan gunakan input kode manual di bawah.";
                return;
            }

            const streamContainer = document.getElementById('qr-camera-stream');
            if (!streamContainer) return;

            try {
                if (this.html5QrCode) {
                    await this.stopCameraScanner();
                }

                this.html5QrCode = new window.Html5Qrcode("qr-camera-stream");
                const config = {
                    fps: 10,
                    qrbox: { width: 240, height: 240 },
                    aspectRatio: 1.0,
                };

                await this.html5QrCode.start(
                    { facingMode: "environment" },
                    config,
                    (decodedText) => {
                        this.onQrCodeScanned(decodedText);
                    },
                    (error) => {
                        // Ignore frame-by-frame parse failures
                    }
                );

                this.cameraActive = true;
            } catch (err) {
                console.warn("Camera start failed:", err);
                this.cameraActive = false;
                this.scanErrorMessage = "Kamera tidak dapat diakses atau izin ditolak. Anda tetap dapat memasukkan kode 6 digit secara manual.";
            }
        },

        async stopCameraScanner() {
            if (this.html5QrCode && this.cameraActive) {
                try {
                    await this.html5QrCode.stop();
                    this.html5QrCode.clear();
                } catch (e) {
                    console.warn("Error stopping scanner:", e);
                }
            }
            this.cameraActive = false;
            this.html5QrCode = null;
        },

        async onQrCodeScanned(decodedText) {
            if (this.scanningLoading) return;
            this.scanningLoading = true;

            // Optional subtle vibration feedback
            if (navigator.vibrate) {
                navigator.vibrate([80, 40, 80]);
            }

            await this.submitVerification(decodedText);
        },

        async submitManualVerification() {
            const code = this.manualCode.trim();
            if (!code) {
                this.scanErrorMessage = "Silakan masukkan kode pengambilan terlebih dahulu.";
                return;
            }
            this.scanningLoading = true;
            await this.submitVerification(code);
        },

        async submitVerification(payload) {
            this.scanSuccessMessage = null;
            this.scanErrorMessage = null;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const res = await fetch('/claims/quick-scan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        qr_payload: payload
                    })
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    this.scanSuccessMessage = data.message || "Serah terima berhasil diverifikasi!";
                    await this.stopCameraScanner();

                    setTimeout(() => {
                        window.location.reload();
                    }, 1800);
                } else {
                    this.scanErrorMessage = data.message || "Kode pengambilan tidak cocok.";
                    this.scanningLoading = false;
                }
            } catch (e) {
                this.scanErrorMessage = "Gagal memverifikasi serah terima. Periksa koneksi internet Anda.";
                this.scanningLoading = false;
            }
        }
    };
}
