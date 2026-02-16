<!-- Footer -->
<footer id="kontak" class="footer-agro py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-leaf text-accent"></i>
                    <span class="font-display fs-5 fw-bold text-white">AgroBandung</span>
                </div>
                <p class="text-white-50 small">
                    Platform pemesanan tiket wisata agro terbaik di Bandung.
                </p>
            </div>
            <div class="col-md-4">
                <h5 class="font-display fw-semibold mb-3">Kontak</h5>
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex align-items-center gap-2 text-white-50 small">
                        <i class="bi bi-telephone"></i>
                        <span>+62 812 3456 7890</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white-50 small">
                        <i class="bi bi-envelope"></i>
                        <span>info@agrobandung.id</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 text-white-50 small">
                        <i class="bi bi-geo-alt"></i>
                        <span>Bandung, Jawa Barat</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <h5 class="font-display fw-semibold mb-3">Jam Operasional</h5>
                <div class="text-white-50 small">
                    <p class="mb-1">Senin - Jumat: 08:00 - 18:00</p>
                    <p class="mb-0">Sabtu - Minggu: 07:00 - 19:00</p>
                </div>
            </div>
        </div>
        <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">
        <p class="text-center text-white-50 small mb-0">
            © 2026 AgroBandung. All rights reserved.
        </p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var pills = document.querySelectorAll('.region-pill');
        var cards = document.querySelectorAll('.destination-card');
        var searchInput = document.getElementById('searchInput');
        var searchClear = document.getElementById('searchClear');
        var noResults = document.getElementById('noResults');
        var activeFilter = 'semua';

        function filterCards() {
            var query = searchInput.value.toLowerCase().trim();
            var visibleCount = 0;

            // Show/hide clear button
            if (query.length > 0) {
                searchClear.classList.remove('d-none');
            } else {
                searchClear.classList.add('d-none');
            }

            cards.forEach(function(card) {
                var region = card.dataset.region;
                var name = card.dataset.name || '';
                var cardText = card.textContent.toLowerCase();
                var matchRegion = (activeFilter === 'semua' || region === activeFilter);
                var matchSearch = (!query || cardText.indexOf(query) !== -1);

                if (matchRegion && matchSearch) {
                    card.style.display = '';
                    card.style.animation = 'none';
                    card.offsetHeight;
                    card.style.animation = 'regionFadeIn 0.4s ease forwards';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Show/hide no results message
            if (visibleCount === 0) {
                noResults.classList.remove('d-none');
            } else {
                noResults.classList.add('d-none');
            }
        }

        // Region filter click
        pills.forEach(function(pill) {
            pill.addEventListener('click', function() {
                activeFilter = this.dataset.filter;
                pills.forEach(function(p) {
                    p.classList.remove('active');
                    p.setAttribute('aria-selected', 'false');
                });
                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');
                filterCards();
            });
        });

        // Search input
        var debounceTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(filterCards, 200);
        });

        // Clear search
        searchClear.addEventListener('click', function() {
            searchInput.value = '';
            searchClear.classList.add('d-none');
            filterCards();
            searchInput.focus();
        });
    });
</script>
</body>

</html>
