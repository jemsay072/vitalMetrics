import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Search Filter Global
document.addEventListener('alpine:init', () => {
    Alpine.data('searchFilter', (config) => ({
        search: '',
        view: 'list',
        endpoint: config.endpoint,
        exportEndpoint: config.exportEndpoint || config.endpoint,
        extractFilename: config.extractFilename || 'export.csv',
        extractFields: config.extractFields || [],
        exportLabel: config.exportLabel || 'Extract',
        results: [],
        loading: false,
        currentPage: 1,
        lastPage: 1,
        total: 0,
        perPage: 10,
        perPageOptions : [10, 25, 50],
        perPageOpen: false,

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
                url.searchParams.set('per_page', this.perPage);

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

        changePerPage (value){
            this.perPage = Number(value);
            this.perPageOpen = false;
            this.fetchData(1);
        },

        // buildCsv(rows) {
        //     if (!Array.isArray(this.extractFields) || this.extractFields.length === 0) {
        //         return '';
        //     }

        //     const header = this.extractFields.map(field => `"${field.label}"`).join(',');
        //     const lines = rows.map(row => {
        //         return this.extractFields.map(field => {
        //             const value = row[field.key] ?? '';
        //             const escaped = String(value).replace(/"/g, '""');
        //             return `"${escaped}"`;
        //         }).join(',');
        //     });
        //     return [header, ...lines].join('\n');
        // },

        buildCsv(rows) {
            if (!Array.isArray(this.extractFields) || this.extractFields.length === 0) {
                return '';
            }

            const header = this.extractFields.map(field => `"${field.label}"`).join(',');
            const lines = rows.map(row => {
                return this.extractFields.map(field => {
                    const rawValue = row[field.key];
                    const value = (rawValue === null || rawValue === undefined || rawValue === '')
                        ? (field.fallback ?? '')
                        : rawValue;
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

        extractCurrentData() {
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

        async exportAll(){
            if(!this.results.length) return;

            if(
                !Array.isArray(this.extractFields) ||
                this.extractFields.length === 0
            ){
                console.warn('No extractFields configured for searchFilter.');
                return;
            }

            this.loading = true;

            try{
                const url = new URL(
                    this.exportEndpoint,
                    window.location.origin
                );

                url.searchParams.set('search', this.search);

                const response = await fetch(url.toString());

                if(!response.ok) throw new Error('Failed to export data.');

                const rows = await response.json();

                const csv = this.buildCsv(rows);

                if(!csv) return;

                this.downloadCsv(csv);

            }catch(error){
                console.error('Error exporting data:', error);
            } finally {
                this.loading = false;
            }
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
        loading: false,
        bp: {},
        bpId: null,
        form: {
            systolic: '',
            diastolic: '',
            pulse: '',
        },
        init() {
            this.fetchBp();
        },
        async loadBp(id) {
            this.loading = true;

            try {
                const response = await fetch(`/bp/${id}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to load Blood Pressure record.');
                }

                this.bp = await response.json();

                this.$nextTick(() => {
                    window.dispatchEvent(
                        new CustomEvent('set-reading-time', {
                            detail: this.bp.reading_time
                        })
                    );
                });

            } catch (error) {
                console.error('Error loading Blood Pressure:', error);
            } finally {
                this.loading = false;
            }
        },
        fetchBp() {
            this.loading = true;

            fetch('/bp', {
                headers: {'Accept': 'application/json',}
            })
            .then(response => response.json())
            .then(data => {
                this.list = data;
            })
            .finally(() => {
                this.loading = false;
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
        },
        liveEditTerm() {
            let s = parseInt(this.bp.systolic);
            let d = parseInt(this.bp.diastolic);

            if (!s || !d) return '';

            if (s >= 140 || d >= 90) return 'Hypertension';

            if ((s >= 120 && s < 140) || (d >= 80 && d < 90))
                return 'Prehypertension';

            return 'Normal';
        }
    }));

    //WeightTracker
    Alpine.data('weightComponent', () => ({
        list: '',
        loading: false,
        weight: {},
        weightId: null,
        form: {
            weight: '',
            measurement_date: '',
            notes: ''
        },
        async loadWeight(id) {
            this.weightId = id;
            this.loading = true;

            try{
                const response = await fetch(`/weight/${id}`);
                const data = await response.json();

                this.weight = data;
                this.form = {
                    weight: data.weight ?? '',
                    measurement_date: data.measurement_date ?? '',
                    notes: data.notes ?? ''
                }

                if(data.measurement_date){
                    window.dispatchEvent(new CustomEvent('set-reading-time', {
                        detail: data.measurement_date
                    }));
                }

            }  catch (error) {
                console.error('Error loading weight:', error);
            } finally{
                this.loading = false;
            }
        }
    }));

    // WorkoutTracker Form (Create & Edit)
    Alpine.data('workoutForm', (initialData = {}) => ({
        workoutId: null,
        loading: false,
        activityType: initialData.activity_type || 'Treadmill',
        durationMinutes: initialData.duration_minutes || '',
        distanceKm: initialData.distance_km || '',
        caloriesBurned: initialData.calories_burned || '',
        steps: initialData.steps || '',

        get isStepActivity() {
            return ['Treadmill', 'Running', 'Walking'].includes(this.activityType);
        },

        get isDistanceActivity() {
            return ['Treadmill', 'Running', 'Walking', 'Cycling'].includes(this.activityType);
        },

        loadWorkout(id) {
            this.workoutId = id;
            this.loading = true;

            fetch('/workout-tracker/' + id)
                .then(res => res.json())
                .then(data => {
                    this.activityType = data.activity_type || 'Treadmill';
                    this.durationMinutes = data.duration_minutes || '';
                    this.distanceKm = data.distance_km || '';
                    this.caloriesBurned = data.calories_burned || '';
                    this.steps = data.steps || '';
                    this.loading = false;
                })
                .catch(() => {
                    this.loading = false;
                });
        },

        handleActivityChange() {
            if (!this.isDistanceActivity) {
                this.distanceKm = '';
            }
            if (!this.isStepActivity) {
                this.steps = '';
            } else if (!this.steps && this.distanceKm) {
                this.estimateSteps();
            }
        },

        estimateSteps() {
            if (!this.isStepActivity || !this.distanceKm) return;
            const factor = this.activityType === 'Running' ? 1100 : 1300;
            this.steps = Math.round(parseFloat(this.distanceKm) * factor);
        }
    }));



    // Role Component
    Alpine.data('roleComponent', () => ({
        loading: false,
        role: {},
        roleId: null,
        form: {
            name: '',
            description: '',
            permissions: {
                dashboard: false,
                trackers: false,
                users: false,
                profile: false,
                roles: false,
                patients: false,
                settings: false,
                export: false,
                reports: false,
                view_only: false,
            },
        },
        errors: {},

        // Dynamic select all
        get selectAll (){
            const keys = Object.keys(this.form.permissions);
            if(keys.length === 0) return false;
            return keys.every(key => this.form.permissions[key] === true);
        },
        set selectAll (value){
            Object.keys(this.form.permissions).forEach( key => {
                this.form.permissions[key] = value;
            });
        },
        resetPermissions() {
            Object.keys(this.form.permissions).forEach(key => {
                this.form.permissions[key] = false;
            });
        },

        async loadRole(id) {
            if (!id) {
                console.error('Role ID is required.');
                return;
            }

            this.loading = true;

            try {
                const response = await fetch(`/admin/roles/${id}`,{
                    headers: {
                        'Accept': 'application/json',
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to load role.');
                }

                const data = await response.json();

                this.role = data;

                // Populate role details
                this.form.name = data.name || '';
                this.form.description = data.description || '';

                this.resetPermissions();

                if (Array.isArray(data.permissions)) {
                    const backendPermissions = data.permissions.map(permission =>
                        String(
                            typeof permission === 'string'
                                ? permission
                                : permission.name
                        ).toLowerCase()
                    );

                    Object.keys(this.form.permissions).forEach(key => {
                        this.form.permissions[key] =
                            backendPermissions.includes(key.toLowerCase());
                    });
                }

            } catch (error) {
                console.error('Error loading role:', error);
            } finally {
                this.loading = false;
            }
        },

        async updateRole(event) {
            const formData = new FormData(event.target);

            // Convert checked permission state into an array of keys
            const selectedPermissions = Object.keys(this.form.permissions)
                .filter(key => this.form.permissions[key] === true);

            formData.delete('permissions[]');

            selectedPermissions.forEach(permission => {
                formData.append('permissions[]', permission);
            });

            this.loading = true;

            try {

                const response = await fetch(`/admin/roles/${this.roleId}`, {
                    method: 'PUT',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute('content'),

                    },
                    body: JSON.stringify({
                        _method: 'PUT',
                        name: this.form.name,
                        description: this.form.description,
                        permissions: Object.keys(this.form.permissions)
                            .filter(key => this.form.permissions[key]),
                    }),


                });

                const data = await response.json();

                if (response.status === 422) {
                    this.errors = data.errors ?? {};
                    return;
                }

                if (!response.ok) {
                    throw new Error(
                        data.message || 'Failed to update role.'
                    );
                }

                this.$dispatch('close-modal', 'roles-edit');
                window.location.reload();

            } catch (error) {
                console.error('Error updating role:', error);
            } finally {
                this.loading = false;
            }
        },
    }));

    Alpine.data('readingTime', (initialValue = '', listenForUpdates = false) => ({
        open: false,
        hour: '',
        minute: '',
        period: '',
        timeValue: initialValue || '',
        error: false,

        init() {
            if (this.timeValue) {
                this.setTime(this.timeValue);
            } else {
                this.setCurrentTime();
            }

            if (listenForUpdates) {
                window.addEventListener('set-reading-time', (event) => {
                    this.setTime(event.detail);
                });
            }
        },

        setTime(value) {
            if (!value) return;

            const [h, m] = value.substring(0, 5).split(':');
            let parsedHour = parseInt(h, 10);

            if (Number.isNaN(parsedHour) || !m) return;

            this.period = parsedHour >= 12 ? 'PM' : 'AM';
            parsedHour = parsedHour % 12;

            this.hour = String(
                parsedHour === 0 ? 12 : parsedHour
            ).padStart(2, '0');

            this.minute = m;
            this.timeValue = `${h}:${m}`;
            this.error = false;
        },

        setCurrentTime() {
            const now = new Date();
            let currentHour = now.getHours();

            this.period = currentHour >= 12 ? 'PM' : 'AM';
            currentHour = currentHour % 12;

            this.hour = String(
                currentHour === 0 ? 12 : currentHour
            ).padStart(2, '0');

            this.minute = String(now.getMinutes()).padStart(2, '0');

            this.updateTime();
        },

        selectHour(value) {
            this.hour = value;
            this.error = false;
        },

        selectMinute(value) {
            this.minute = value;
            this.error = false;
        },

        selectPeriod(value) {
            this.period = value;
            this.error = false;
        },

        get displayTime() {
            if (!this.hour || !this.minute || !this.period) {
                return '--:-- --';
            }

            return `${this.hour}:${this.minute} ${this.period}`;
        },

        updateTime() {
            if (!this.hour || !this.minute || !this.period) {
                this.error = true;
                return;
            }

            let convertedHour = parseInt(this.hour, 10);

            if (this.period === 'AM' && convertedHour === 12) {
                convertedHour = 0;
            } else if (this.period === 'PM' && convertedHour !== 12) {
                convertedHour += 12;
            }

            this.timeValue =
                String(convertedHour).padStart(2, '0') + ':' + this.minute;

            this.error = false;
            this.open = false;
        }
    }));
});

Alpine.start();
