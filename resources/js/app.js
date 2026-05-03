import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Search Filter Global
document.addEventListener('alpine:init', () => {
    Alpine.data('searchFilter', (config) => ({
        search: '',
        endpoint: config.endpoint,
        extractFilename: config.extractFilename || 'export.csv',
        extractFields: config.extractFields || [],
        exportLabel: config.exportLabel || 'Extract',
        results: [],
        loading: false,
        currentPage: 1,
        lastPage: 1,
        total: 0,
        perPage: 10,

        init(){
            this.fetchData();
        },

        async fetchData(page = 1){
            this.loading = true;
            this.currentPage = page;

            try {
                const url = new URL(this.endpoint, window.location.origin);
                url.searchParams.set('search', this.search);
                url.searchParams.set('page', this.currentPage);

                const response = await fetch(url.toString());
                const data = await response.json();

                this.results = data.data;
                this.currentPage = data.current_page;
                this.lastPage = data.last_page;
                this.total = data.total;
                this.perPage = data.per_page;
            } catch (error) {
                console.error('Error fetching data:', error);
            } finally {
                this.loading = false;
            }
        },

        buildCsv(rows) {
            if (!Array.isArray(this.extractFields) || this.extractFields.length === 0) {
                return '';
            }

            const header = this.extractFields.map(field => `"${field.label}"`).join(',');
            const lines = rows.map(row => {
                return this.extractFields.map(field => {
                    const value = row[field.key] ?? '';
                    const escaped = String(value).replace(/"/g, '""');
                    return `"${escaped}"`;
                }).join(',');
            });
            return [header, ...lines].join('\n');
        },

        downloadCsv(csv) {
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', this.extractFilename);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        },

        extractData() {
            if (!this.results.length) {
                return;
            }

            if (!Array.isArray(this.extractFields) || this.extractFields.length === 0) {
                console.warn('No extractFields configured for searchFilter.');
                return;
            }

            const csv = this.buildCsv(this.results);
            if (!csv) {
                return;
            }

            this.downloadCsv(csv);
        },

        goToPage(page) {
            if (page >= 1 && page <= this.lastPage) {
                this.fetchData(page);
            }
        },

        nextPage() {
            if (this.currentPage < this.lastPage) {
                this.fetchData(this.currentPage + 1);
            }
        },

        prevPage() {
            if (this.currentPage > 1) {
                this.fetchData(this.currentPage - 1);
            }
        }
    }));

    // Sidebar management
    Alpine.data('sidebarManager', () => ({
        sidebarOpen: false,

        init() {
            // Listen for close-sidebar events
            document.addEventListener('close-sidebar', () => {
                this.sidebarOpen = false;
            });
        }
    }));

    // BpComponent
    Alpine.data('bpComponent', () => ({
        list: [],
        form: {
            systolic: '',
            diastolic: '',
            pulse: '',
        },
        init() {
            this.fetchBp();
        },
        fetchBp() {
            fetch('/bp', {
                headers: {'Accept': 'application/json',}
            })
            .then(response => response.json())
            .then(data => {
                this.list = data;
            });
        },
        saveBp() {
            fetch('/bp/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(this.form)
            })
            .then(res => res.json())
            .then(() => {
                this.fetchBp();
                this.resetForm();
            })
        },
        resetForm() {
            this.form = { systolic: '', diastolic: '', pulse: '' };
        },
        getColor(term) {
            switch(term) {
                case 'Hypertension': return 'text-red-600 bg-red-50 p-2 rounded';
                case 'Prehypertension': return 'text-yellow-600 bg-yellow-50 p-2 rounded';
                case 'Normal': return 'text-green-600 bg-green-50 p-2 rounded';
                default: return 'text-gray-500';
            }
        },
        liveTerm() {
            // parseInt ensures we are comparing numbers, not strings
            let s = parseInt(this.form.systolic);
            let d = parseInt(this.form.diastolic);

            if (!s || !d) return '';

            if (s >= 140 || d >= 90) return 'Hypertension';
            if ((s >= 120 && s < 140) || (d >= 80 && d < 90)) return 'Prehypertension';
            if (s < 120 && d < 80) return 'Normal';
            return 'Unknown';
        }
    }));
});

Alpine.start();
