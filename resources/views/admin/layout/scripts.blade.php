<!-- jQuery with SRI -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Datatable -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css" />
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

<!-- Third Party Plugin(OverlayScrollbars) -->
<script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"
    integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ=" crossorigin="anonymous"></script>

<!-- Required Plugin(popperjs for Bootstrap 5) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
</script>

<!-- Required Plugin(Bootstrap 5) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
    integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous">
</script>

<!-- Required Plugin(AdminLTE) -->
<script src="{{ asset('admin/js/adminlte.js') }}"></script>

<!-- OverlayScrollbars Configure -->
<script>
    const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
    const Default = {
        scrollbarTheme: 'os-theme-light',
        scrollbarAutoHide: 'leave',
        scrollbarClickScroll: true,
    };
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
        if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
            OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                scrollbars: {
                    theme: Default.scrollbarTheme,
                    autoHide: Default.scrollbarAutoHide,
                    clickScroll: Default.scrollbarClickScroll,
                },
            });
        }
    });
</script>

<!-- OPTIONAL SCRIPTS -->
<!-- sortablejs -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"
    integrity="sha256-ipiJrswvAR4VAx/th+6zWsdeYmVae0iJuiR+6OqHJHQ=" crossorigin="anonymous"></script>
<script>
    const connectedSortables = document.querySelectorAll('.connectedSortable');
    connectedSortables.forEach((connectedSortable) => {
        let sortable = new Sortable(connectedSortable, {
            group: 'shared',
            handle: '.card-header',
        });
    });

    const cardHeaders = document.querySelectorAll('.connectedSortable .card-header');
    cardHeaders.forEach((cardHeader) => {
        cardHeader.style.cursor = 'move';
    });
</script>

<!-- apexcharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"
    integrity="sha256-+vh8GkaU7C9/wbSLIcwq82tQ2wTf44aOHA8HlBMwRI8=" crossorigin="anonymous"></script>

<!-- jsvectormap -->
<script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/js/jsvectormap.min.js"
    integrity="sha256-/t1nN2956BT869E6H4V1dnt0X5pAQHPytli+1nTZm2Y=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/maps/world.js"
    integrity="sha256-XPpPaZlU8S/HWf7FZLAncLg2SAkP8ScUTII89x9D3lY=" crossorigin="anonymous"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

<!-- Custom Script -->
<script src="{{ url('admin/js/custom.js') }}"></script>

<script>
    $(document).ready(function() {
        $("#roles-table").DataTable();
        $("#users-table").DataTable();
        $("#companies-table").DataTable();
        $("#permissions-table").DataTable();
        $("#customers-table").DataTable();
        $("#trips-table").DataTable();

    });
</script>

<!-- Additional Custom Logic Scripts -->
<script>
    const getDistrictsUrl = "{!! url('admin/get-districts') !!}/";
    const getCitiesUrl = "{!! url('admin/get-cities') !!}/";
    const getTehsilsUrl = "{!! url('admin/get-tehsils') !!}/";
    const getPincodesUrl = "{!! url('admin/get-pincodes') !!}/";

    $(document).ready(function() {
        $('#state').on('change', function() {
            let stateID = $(this).val();
            $('#district').html('<option value="">Loading...</option>');
            if (stateID) {
                $.get(getDistrictsUrl + stateID)
                    .done(function(data) {
                        let options = '<option value="">Select District</option>';
                        data.forEach(d => {
                            options +=
                                `<option value="${d.id}" ${d.id == '{{ old('district_id', $user->district_id ?? '') }}' ? 'selected' : ''}>${d.name}</option>`;
                        });
                        $('#district').html(options).trigger('change');
                    })
                    .fail(function() {
                        alert('Failed to load districts');
                        $('#district').html('<option value="">Select District</option>');
                    });
            } else {
                $('#district').html('<option value="">Select District</option>');
            }
        });

        $('#district').on('change', function() {
            let districtID = $(this).val();
            $('#city').html('<option value="">Loading...</option>');
            if (districtID) {
                $.get(getCitiesUrl + districtID)
                    .done(function(data) {
                        let options = '<option value="">Select City</option>';
                        data.forEach(c => {
                            options +=
                                `<option value="${c.id}" ${c.id == '{{ old('city_id', $user->city_id ?? '') }}' ? 'selected' : ''}>${c.name}</option>`;
                        });
                        $('#city').html(options).trigger('change');
                    })
                    .fail(function() {
                        alert('Failed to load cities');
                        $('#city').html('<option value="">Select City</option>');
                    });
            }
        });

        $('#city').on('change', function() {
            let cityID = $(this).val();
            $('#tehsil').html('<option value="">Loading...</option>');
            $('#pincode').html('<option value="">Loading...</option>');
            if (cityID) {
                // Load Tehsils
                $.get(getTehsilsUrl + cityID)
                    .done(function(data) {
                        let options = '<option value="">Select Tehsil</option>';
                        data.forEach(t => {
                            options +=
                                `<option value="${t.id}" ${t.id == '{{ old('tehsil_id', $user->tehsil_id ?? '') }}' ? 'selected' : ''}>${t.name}</option>`;
                        });
                        $('#tehsil').html(options);
                    })
                    .fail(function() {
                        alert('Failed to load tehsils');
                        $('#tehsil').html('<option value="">Select Tehsil</option>');
                    });

                // Load Pincodes
                $.get(getPincodesUrl + cityID)
                    .done(function(data) {
                        let options = '<option value="">Select Pincode</option>';
                        const selectedPincode = '{{ old('pincode_id', $user->pincode_id ?? '') }}';
                        data.forEach(p => {
                            options +=
                                `<option value="${p.id}" ${p.id == selectedPincode ? 'selected' : ''}>${p.pincode}</option>`;
                        });
                        $('#pincode').html(options);
                    })
                    .fail(function() {
                        alert('Failed to load pincodes');
                        $('#pincode').html('<option value="">Select Pincode</option>');
                    });
            } else {
                $('#tehsil').html('<option value="">Select Tehsil</option>');
                $('#pincode').html('<option value="">Select Pincode</option>');
            }
        });

        // 🧠 Pre-fill logic for edit mode
        @if (isset($user))
            const preSelectedState = "{{ old('state_id', $user->state_id ?? '') }}";
            const preSelectedDistrict = "{{ old('district_id', $user->district_id ?? '') }}";
            const preSelectedCity = "{{ old('city_id', $user->city_id ?? '') }}";
            const preSelectedTehsil = "{{ old('tehsil_id', $user->tehsil_id ?? '') }}";
            const preSelectedPincode = "{{ old('pincode_id', $user->pincode_id ?? '') }}";

            if (preSelectedState) {
                $('#state').val(preSelectedState).trigger('change');
                setTimeout(() => {
                    $('#district').val(preSelectedDistrict).trigger('change');
                    setTimeout(() => {
                        $('#city').val(preSelectedCity).trigger('change');
                        setTimeout(() => {
                            $('#tehsil').val(preSelectedTehsil).trigger('change');
                            setTimeout(() => {
                                $('#pincode').val(preSelectedPincode).trigger(
                                    'change');
                            }, 500);
                        }, 500);
                    }, 500);
                }, 500);
            }
        @endif
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const companySelect = document.getElementById('company_id');
        const executiveSelect = document.getElementById('user_id');

        if (!companySelect || !executiveSelect) return;

        companySelect.addEventListener('change', function() {
            const companyId = this.value;

            executiveSelect.innerHTML = '<option value="">-- Select Executive --</option>';

            if (companyId) {
                fetch(`/admin/companies/${companyId}/executives`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.executives && Array.isArray(data.executives)) {
                            data.executives.forEach(exec => {
                                const option = document.createElement('option');
                                option.value = exec.id;
                                option.textContent = exec.name;
                                executiveSelect.appendChild(option);
                            });
                        } else {
                            console.warn('Unexpected response format:', data);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching executives:', error);
                    });
            }
        });
    });
</script>

<script>
    // Toggle all checkboxes
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.customer-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>

{{-- Google Maps Script --}}
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB_-uOyQimLqBkDW_Vr8d88GX6Qk0lyksI&libraries=places">
</script>
<script>
    function initMap() {
        // ✅ Check for enough trip logs
        if (!tripLogs || tripLogs.length < 2) {
            alert("Not enough trip logs to draw route.");
            return;
        }

        // ✅ Prepare coordinates array from logs
        const pathCoordinates = tripLogs.map(log => ({
            lat: parseFloat(log.latitude),
            lng: parseFloat(log.longitude),
            recorded_at: log.recorded_at ?? ''
        }));

        // ✅ Initialize the map centered on first coordinate
        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 13,
            center: pathCoordinates[0],
        });

        // ✅ Draw the polyline (route)
        const tripPath = new google.maps.Polyline({
            path: pathCoordinates,
            geodesic: true,
            strokeColor: "#007bff",
            strokeOpacity: 1.0,
            strokeWeight: 4,
        });
        tripPath.setMap(map);

        // ✅ Adjust map view to fit entire route
        const bounds = new google.maps.LatLngBounds();
        pathCoordinates.forEach(coord => bounds.extend(coord));
        map.fitBounds(bounds);

        // // ✅ Add markers for ALL log points
        // pathCoordinates.forEach((coord, index) => {
        //     const marker = new google.maps.Marker({
        //         position: {
        //             lat: coord.lat,
        //             lng: coord.lng
        //         },
        //         map: map,
        //         label: (index === 0) ? "A" : (index === pathCoordinates.length - 1 ? "B" : ""),
        //         title: `Log #${index + 1}\n${coord.recorded_at ?? ''}`,

        //         // ✅ Different icon for start (green), end (red), and mid (blue)
        //         icon: {
        //             url: index === 0 ?
        //                 "http://maps.google.com/mapfiles/ms/icons/green-dot.png" :
        //                 index === pathCoordinates.length - 1 ?
        //                 "http://maps.google.com/mapfiles/ms/icons/red-dot.png" :
        //                 "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
        //         }
        //     });

        //     // ✅ Info window on marker click showing log details
        //     const infoWindow = new google.maps.InfoWindow({
        //         content: `<div>
        //             <strong>Log #${index + 1}</strong><br>
        //             Lat: ${coord.lat}<br>
        //             Lng: ${coord.lng}<br>
        //             ${coord.recorded_at}
        //         </div>`
        //     });

        //     marker.addListener("click", () => {
        //         infoWindow.open(map, marker);
        //     });
        // });

        // // ✅ Add only start and end markers
        [pathCoordinates[0], pathCoordinates[pathCoordinates.length - 1]].forEach((coord, index) => {
            const marker = new google.maps.Marker({
                position: {
                    lat: coord.lat,
                    lng: coord.lng
                },
                map: map,
                label: index === 0 ? "A" : "B",
                title: index === 0 ? "Start Point" : "End Point",
                icon: {
                    url: index === 0 ?
                        "http://maps.google.com/mapfiles/ms/icons/green-dot.png" :
                        "http://maps.google.com/mapfiles/ms/icons/red-dot.png"
                }
            });

            const infoWindow = new google.maps.InfoWindow({
                content: `<div>
            <strong>${index === 0 ? 'Start' : 'End'} Point</strong><br>
            Lat: ${coord.lat}<br>
            Lng: ${coord.lng}<br>
            ${coord.recorded_at}
        </div>`
            });

            marker.addListener("click", () => {
                infoWindow.open(map, marker);
            });
        });


        // ✅ Calculate total distance using haversine formula
        let distance = 0;
        for (let i = 1; i < pathCoordinates.length; i++) {
            distance += haversineDistance(pathCoordinates[i - 1], pathCoordinates[i]);
        }

        // ✅ Show calculated distance in designated span
        document.getElementById("distance-display").innerText = distance.toFixed(2) + " km";
    }

    // ✅ Converts degrees to radians
    function toRad(value) {
        return value * Math.PI / 180;
    }

    // ✅ Calculate distance between two coordinates
    function haversineDistance(coord1, coord2) {
        const R = 6371; // Earth radius in km
        const dLat = toRad(coord2.lat - coord1.lat);
        const dLon = toRad(coord2.lng - coord1.lng);
        const lat1 = toRad(coord1.lat);
        const lat2 = toRad(coord2.lat);

        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.sin(dLon / 2) * Math.sin(dLon / 2) * Math.cos(lat1) * Math.cos(lat2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    // ✅ Initialize the map when page loads
    document.addEventListener("DOMContentLoaded", initMap);
</script>
