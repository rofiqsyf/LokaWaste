/**
 * Dashboard Analytics Engine
 * Real-time dynamic date-filtered metrics and interactive Chart.js visualizations
 */
export default function dashboardAnalytics(initialData = {}) {
    return {
        filter: '30d', // '7d', '30d', 'month', 'all', 'custom'
        startDate: '',
        endDate: '',
        customStart: '',
        customEnd: '',
        showCustomInputs: false,
        rawListings: initialData.listings || [],
        rawClaims: initialData.claims || [],
        esgRates: {
            co2: Number(initialData.esgRates?.co2 ?? 0.85),
            water: Number(initialData.esgRates?.water ?? 320),
            value: Number(initialData.esgRates?.value ?? 15000),
        },
        
        // Computed dynamic metrics
        metrics: {
            portionsShared: 0,
            portionsTaken: 0,
            totalPortions: 0,
            co2Avoided: 0,
            waterSaved: 0,
            economicValue: 0,
            completionRate: 0,
            totalListings: 0,
            totalClaims: 0,
        },

        trendChart: null,
        categoryChart: null,

        init() {
            this.setFilter('30d', false);
            this.$nextTick(() => {
                this.initCharts();
            });
        },

        setFilter(type, refreshCharts = true) {
            this.filter = type;
            this.showCustomInputs = (type === 'custom');

            const today = new Date();
            const formatYMD = (d) => {
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };

            this.endDate = formatYMD(today);

            if (type === '7d') {
                const start = new Date(today);
                start.setDate(today.getDate() - 6);
                this.startDate = formatYMD(start);
            } else if (type === '30d') {
                const start = new Date(today);
                start.setDate(today.getDate() - 29);
                this.startDate = formatYMD(start);
            } else if (type === 'month') {
                const start = new Date(today.getFullYear(), today.getMonth(), 1);
                this.startDate = formatYMD(start);
            } else if (type === 'all') {
                this.startDate = '2020-01-01';
            } else if (type === 'custom') {
                if (!this.customStart) {
                    const start = new Date(today);
                    start.setDate(today.getDate() - 14);
                    this.customStart = formatYMD(start);
                    this.customEnd = this.endDate;
                }
                this.startDate = this.customStart;
                this.endDate = this.customEnd;
            }

            this.calculateMetrics();
            if (refreshCharts) {
                this.updateCharts();
            }
        },

        applyCustomDates() {
            if (!this.customStart || !this.customEnd) return;
            if (this.customStart > this.customEnd) {
                const temp = this.customStart;
                this.customStart = this.customEnd;
                this.customEnd = temp;
            }
            this.startDate = this.customStart;
            this.endDate = this.customEnd;
            this.calculateMetrics();
            this.updateCharts();
        },

        calculateMetrics() {
            const start = this.startDate;
            const end = this.endDate;

            // Filter in-memory raw records by date range
            const listings = this.rawListings.filter(l => {
                const d = l.date || (l.created_at ? l.created_at.substring(0, 10) : '');
                return d >= start && d <= end;
            });

            const claims = this.rawClaims.filter(c => {
                const d = c.date || (c.created_at ? c.created_at.substring(0, 10) : '');
                return d >= start && d <= end;
            });

            const portionsShared = listings.reduce((sum, l) => sum + (Number(l.portions_total) || 0), 0);
            const portionsTaken = claims
                .filter(c => c.status === 'picked_up')
                .reduce((sum, c) => sum + (Number(c.portions) || 0), 0);

            const totalPortions = portionsShared + portionsTaken;
            const co2Avoided = Number((totalPortions * this.esgRates.co2).toFixed(1));
            const waterSaved = Math.round(totalPortions * this.esgRates.water);
            const economicValue = Math.round(totalPortions * this.esgRates.value);

            const totalClaimRequests = claims.length;
            const completedClaims = claims.filter(c => c.status === 'picked_up').length;
            const completionRate = totalClaimRequests > 0
                ? Math.round((completedClaims / totalClaimRequests) * 100)
                : 100;

            this.metrics = {
                portionsShared,
                portionsTaken,
                totalPortions,
                co2Avoided,
                waterSaved,
                economicValue,
                completionRate,
                totalListings: listings.length,
                totalClaims: claims.length,
            };
        },

        initCharts() {
            if (!window.Chart) return;

            const trendCanvas = document.getElementById('analytics-trend-chart');
            const categoryCanvas = document.getElementById('analytics-category-chart');

            if (!trendCanvas || !categoryCanvas) return;

            // Trend Chart
            const trendCtx = trendCanvas.getContext('2d');
            const gradientEmerald = trendCtx.createLinearGradient(0, 0, 0, 300);
            gradientEmerald.addColorStop(0, 'rgba(30, 122, 76, 0.35)');
            gradientEmerald.addColorStop(1, 'rgba(30, 122, 76, 0.02)');

            const gradientBlue = trendCtx.createLinearGradient(0, 0, 0, 300);
            gradientBlue.addColorStop(0, 'rgba(42, 111, 151, 0.35)');
            gradientBlue.addColorStop(1, 'rgba(42, 111, 151, 0.02)');

            const chartData = this.buildTrendData();

            this.trendChart = new window.Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: 'Porsi Dibagikan',
                            data: chartData.shared,
                            borderColor: '#1E7A4C',
                            backgroundColor: gradientEmerald,
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2.5,
                            pointBackgroundColor: '#1E7A4C',
                            pointHoverRadius: 6,
                            pointRadius: chartData.labels.length > 20 ? 0 : 3,
                        },
                        {
                            label: 'Porsi Diselamatkan',
                            data: chartData.taken,
                            borderColor: '#2A6F97',
                            backgroundColor: gradientBlue,
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2.5,
                            pointBackgroundColor: '#2A6F97',
                            pointHoverRadius: 6,
                            pointRadius: chartData.labels.length > 20 ? 0 : 3,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            align: 'end',
                            labels: {
                                boxWidth: 12,
                                boxHeight: 12,
                                usePointStyle: true,
                                font: {
                                    family: "'Atkinson Hyperlegible', sans-serif",
                                    size: 11,
                                    weight: 700
                                },
                                color: '#16251D'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(22, 37, 29, 0.95)',
                            titleFont: { family: "'Atkinson Hyperlegible', sans-serif", size: 12, weight: 700 },
                            bodyFont: { family: "'Atkinson Hyperlegible', sans-serif", size: 11 },
                            padding: 10,
                            cornerRadius: 12,
                            boxPadding: 4,
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { family: "'Atkinson Hyperlegible', sans-serif", size: 10 },
                                color: '#4E6157',
                                maxTicksLimit: 8
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(213, 224, 217, 0.5)' },
                            ticks: {
                                font: { family: "'Atkinson Hyperlegible', sans-serif", size: 10 },
                                color: '#4E6157',
                                precision: 0
                            }
                        }
                    }
                }
            });

            // Category Donut Chart
            const catCtx = categoryCanvas.getContext('2d');
            const catData = this.buildCategoryData();

            this.categoryChart = new window.Chart(catCtx, {
                type: 'doughnut',
                data: {
                    labels: catData.labels,
                    datasets: [{
                        data: catData.values,
                        backgroundColor: [
                            '#1E7A4C',
                            '#2A6F97',
                            '#B45309',
                            '#059669',
                            '#6366F1',
                            '#E11D48',
                            '#0D9488',
                            '#74877B'
                        ],
                        borderWidth: 2,
                        borderColor: '#FFFFFF',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                boxHeight: 10,
                                usePointStyle: true,
                                font: {
                                    family: "'Atkinson Hyperlegible', sans-serif",
                                    size: 10,
                                    weight: 600
                                },
                                color: '#16251D',
                                padding: 12
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(22, 37, 29, 0.95)',
                            cornerRadius: 10,
                            padding: 8,
                            bodyFont: { family: "'Atkinson Hyperlegible', sans-serif", size: 11 },
                            callbacks: {
                                label: function(context) {
                                    const val = context.raw || 0;
                                    return ` ${context.label}: ${val} porsi`;
                                }
                            }
                        }
                    }
                }
            });
        },

        buildTrendData() {
            const start = new Date(this.startDate);
            const end = new Date(this.endDate);
            const labels = [];
            const sharedMap = {};
            const takenMap = {};

            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

            // Populate daily buckets
            for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
                const year = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                const key = `${year}-${m}-${day}`;
                const label = `${d.getDate()} ${monthNames[d.getMonth()]}`;
                labels.push(label);
                sharedMap[key] = 0;
                takenMap[key] = 0;
            }

            // Fill with real listings
            this.rawListings.forEach(l => {
                const k = l.date || (l.created_at ? l.created_at.substring(0, 10) : '');
                if (k && sharedMap[k] !== undefined) {
                    sharedMap[k] += Number(l.portions_total) || 0;
                }
            });

            // Fill with real claims
            this.rawClaims.forEach(c => {
                if (c.status === 'picked_up') {
                    const k = c.date || (c.created_at ? c.created_at.substring(0, 10) : '');
                    if (k && takenMap[k] !== undefined) {
                        takenMap[k] += Number(c.portions) || 0;
                    }
                }
            });

            const shared = Object.values(sharedMap);
            const taken = Object.values(takenMap);

            return { labels, shared, taken };
        },

        buildCategoryData() {
            const start = this.startDate;
            const end = this.endDate;
            const counts = {};

            const listings = this.rawListings.filter(l => {
                const d = l.date || (l.created_at ? l.created_at.substring(0, 10) : '');
                return d >= start && d <= end;
            });

            listings.forEach(l => {
                const cat = l.category || 'Lainnya';
                counts[cat] = (counts[cat] || 0) + (Number(l.portions_total) || 1);
            });

            const labels = Object.keys(counts);
            const values = Object.values(counts);

            if (labels.length === 0) {
                return {
                    labels: ['Belum ada data'],
                    values: [1]
                };
            }

            return { labels, values };
        },

        updateCharts() {
            if (this.trendChart) {
                const trend = this.buildTrendData();
                this.trendChart.data.labels = trend.labels;
                this.trendChart.data.datasets[0].data = trend.shared;
                this.trendChart.data.datasets[1].data = trend.taken;
                this.trendChart.data.datasets[0].pointRadius = trend.labels.length > 20 ? 0 : 3;
                this.trendChart.data.datasets[1].pointRadius = trend.labels.length > 20 ? 0 : 3;
                this.trendChart.update('active');
            }

            if (this.categoryChart) {
                const cat = this.buildCategoryData();
                this.categoryChart.data.labels = cat.labels;
                this.categoryChart.data.datasets[0].data = cat.values;
                this.categoryChart.update('active');
            }
        }
    };
}
