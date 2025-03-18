<div class="search-bar mx-4">
    <div class="search-form d-flex align-items-center">
        <div class="dropdown">
            <button class="text-dark me-3 dropdown-toggle" type="button" id="searchCategoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                All Categories 
            </button>
            <ul class="dropdown-menu" aria-labelledby="searchCategoryDropdown">
                <li><a class="dropdown-item search-category" data-category="all" href="#">All Categories</a></li>
                <li><a class="dropdown-item search-category" data-category="barang" href="#">Barang</a></li>
                <li><a class="dropdown-item search-category" data-category="departemen" href="#">Departemen</a></li>
                <li><a class="dropdown-item search-category" data-category="lokasi" href="#">Lokasi</a></li>
                <li><a class="dropdown-item search-category" data-category="ip" href="#">IP Address</a></li>
                <li><a class="dropdown-item search-category" data-category="menuaktif" href="#">Menu Aktif</a></li>
                <li><a class="dropdown-item search-category" data-category="tipebarang" href="#">Tipe Barang</a></li>
            </ul>
        </div>
        <input type="text" id="globalSearchInput" placeholder="Search..." title="Enter search keyword" autocomplete="off">
        <button id="globalSearchButton" title="Search"><i class="bi bi-search"></i></button>
        <input type="hidden" id="searchCategory" value="all">
    </div>
    
    <div class="search-results-dropdown" id="searchResultsDropdown" style="display: none;">
        <div class="search-results-content" id="searchResultsContent"></div>
        <div class="search-results-footer">
            <a href="#" id="viewAllResults">View all results</a>
        </div>
    </div>
</div>

<style>
.search-bar {
    position: relative;
}

.search-form {
    position: relative;
    width: 100%;
}

.search-results-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    max-height: 400px;
    overflow-y: auto;
}

.search-results-content {
    padding: 10px;
}

.search-result-item {
    padding: 8px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
}

.search-result-item:hover {
    background-color: #f5f5f5;
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-type {
    font-size: 12px;
    color: #666;
    background-color: #f0f0f0;
    padding: 2px 5px;
    border-radius: 3px;
    margin-right: 5px;
}

.search-result-title {
    font-weight: bold;
}

.search-result-description {
    font-size: 12px;
    color: #666;
    margin-top: 3px;
}

.search-results-footer {
    padding: 10px;
    text-align: center;
    border-top: 1px solid #eee;
    background-color: #f9f9f9;
}

.search-results-footer a {
    color: #007bff;
    text-decoration: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('globalSearchInput');
    const searchButton = document.getElementById('globalSearchButton');
    const searchResultsDropdown = document.getElementById('searchResultsDropdown');
    const searchResultsContent = document.getElementById('searchResultsContent');
    const viewAllResults = document.getElementById('viewAllResults');
    const searchCategory = document.getElementById('searchCategory');
    const searchCategoryDropdown = document.getElementById('searchCategoryDropdown');
    const searchCategoryItems = document.querySelectorAll('.search-category');
    
    // Set selected category
    searchCategoryItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const category = this.getAttribute('data-category');
            searchCategory.value = category;
            searchCategoryDropdown.textContent = this.textContent;
            
            // If there's text in the search input, perform search with new category
            if (searchInput.value.trim() !== '') {
                performSearch();
            }
        });
    });
    
    // Perform search on input (with debounce)
    let debounceTimer;
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(performSearch, 300);
    });
    
    // Perform search on button click
    searchButton.addEventListener('click', function() {
        if (searchInput.value.trim() !== '') {
            window.location.href = '{{ route("search") }}?query=' + encodeURIComponent(searchInput.value) + '&category=' + searchCategory.value;
        }
    });
    
    // Handle keyboard navigation
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            if (searchInput.value.trim() !== '') {
                window.location.href = '{{ route("search") }}?query=' + encodeURIComponent(searchInput.value) + '&category=' + searchCategory.value;
            }
        }
    });
    
    // View all results link
    viewAllResults.addEventListener('click', function(e) {
        e.preventDefault();
        if (searchInput.value.trim() !== '') {
            window.location.href = '{{ route("search") }}?query=' + encodeURIComponent(searchInput.value) + '&category=' + searchCategory.value;
        }
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResultsDropdown.contains(e.target) && !searchButton.contains(e.target)) {
            searchResultsDropdown.style.display = 'none';
        }
    });
    
    // Perform AJAX search
    function performSearch() {
        const query = searchInput.value.trim();
        
        if (query === '') {
            searchResultsDropdown.style.display = 'none';
            return;
        }

        fetch('{{ route("search") }}?query=' + encodeURIComponent(query) + '&category=' + searchCategory.value, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            searchResultsContent.innerHTML = '';

            if (data.length === 0) {
                searchResultsContent.innerHTML = '<div class="p-3 text-center">No results found</div>';
            } else {
                // Ambil hanya 5 hasil pertama
                const resultsToShow = data.slice(0, 5);

                resultsToShow.forEach(result => {
                    const resultItem = document.createElement('div');
                    resultItem.className = 'search-result-item';
                    resultItem.innerHTML = `
                        <div>
                            <span class="search-result-type">${result.type}</span>
                            <span class="search-result-title">${result.title}</span>
                        </div>
                        <div class="search-result-description">${result.description}</div>
                    `;

                    resultItem.addEventListener('click', function() {
                        let url = '{{ url("") }}';

                        if (result.category === 'barang') {
                            if (result.type.includes('Komputer')) {
                                url += '/komputer?search=' + result.route_params.serial;
                            } else if (result.type.includes('Tablet')) {
                                url += '/tablet?search=' + result.route_params.serial;
                            } else if (result.type.includes('Switch')) {
                                url += '/switch?search=' + result.route_params.serial;
                            }
                        } else if (result.category === 'departemen') {
                            // Perbaikan: Tambahkan parameter `search` dengan ID departemen
                            url += '/departemen?search=' + result.route_params.departemen;
                        } else if (result.category === 'lokasi') {
                            url += '/lokasi?search=' + result.route_params.lokasi;
                        } else if (result.category === 'ip') {
                            url += '/ip-address/' + result.route_params.idIpHost + '/detail?search=' + result.route_params.ipAddress;
                        } else if (result.category === 'tipebarang') {
                            url += '/tipe-barang?search=' + result.route_params.tipeBarang;
                        } else if (result.category === 'menuaktif' || result.category === 'maintenance') {
                            if (result.type.includes('Komputer') || result.description.includes('Jenis: Komputer')) {
                                url += '/komputer/aktif';
                            } else if (result.type.includes('Tablet') || result.description.includes('Jenis: Tablet')) {
                                url += '/tablet/aktif';
                            } else if (result.type.includes('Switch') || result.description.includes('Jenis: Switch')) {
                                url += '/switch/aktif';
                            }
                        }

                        window.location.href = url;
                    });

                    searchResultsContent.appendChild(resultItem);
                });
            }

            searchResultsDropdown.style.display = 'block';
        })
        .catch(error => {
            console.error('Search error:', error);
        });
    }

});
</script>