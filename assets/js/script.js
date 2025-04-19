// Mobile Navigation Toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileToggle = document.getElementById('mobile-toggle');
    const navLinks = document.getElementById('nav-links');
    
    if (mobileToggle && navLinks) {
        mobileToggle.addEventListener('click', function() {
            navLinks.classList.toggle('active');
        });
    }
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (navLinks && navLinks.classList.contains('active') && !event.target.closest('.navbar')) {
            navLinks.classList.remove('active');
        }
    });
    
    // Table search functionality
    const searchBtn = document.getElementById('search-btn');
    if (searchBtn) {
        searchBtn.addEventListener('click', function() {
            const searchTerm = document.getElementById('table-search').value.toLowerCase();
            const table = document.getElementById('items-table');
            const rows = table.getElementsByTagName('tr');
            
            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                let found = false;
                
                for (let j = 0; j < cells.length; j++) {
                    const cellText = cells[j].innerText.toLowerCase();
                    if (cellText.includes(searchTerm)) {
                        found = true;
                        break;
                    }
                }
                
                row.style.display = found ? '' : 'none';
            }
        });
    }
    
    // Filter functionality
    const applyFiltersBtn = document.getElementById('apply-filters');
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            // Implementation depends on the page (admin or dashboard)
            if (document.getElementById('items-table')) {
                // Admin table filtering
                filterAdminTable();
            } else {
                // Dashboard grid filtering
                filterDashboardGrid();
            }
        });
    }
    
    const resetFiltersBtn = document.getElementById('reset-filters');
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function() {
            // Reset all filter inputs
            const filterInputs = document.querySelectorAll('.filter-group select, .filter-group input');
            filterInputs.forEach(input => {
                input.value = '';
            });
            
            // Reset display
            if (document.getElementById('items-table')) {
                const rows = document.querySelectorAll('#items-table tbody tr');
                rows.forEach(row => {
                    row.style.display = '';
                });
            } else {
                const items = document.querySelectorAll('.item-card');
                items.forEach(item => {
                    item.style.display = '';
                });
            }
        });
    }
    
    function filterAdminTable() {
        const statusFilter = document.getElementById('status-filter')?.value.toLowerCase() || '';
        const categoryFilter = document.getElementById('category-filter')?.value.toLowerCase() || '';
        const dateFilter = document.getElementById('date-filter')?.value || '';
        
        const rows = document.querySelectorAll('#items-table tbody tr');
        
        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            if (cells.length === 0) return;
            
            const status = cells[2].innerText.toLowerCase();
            const category = cells[3].innerText.toLowerCase();
            const date = cells[5].innerText;
            
            let showRow = true;
            
            if (statusFilter && !status.includes(statusFilter)) {
                showRow = false;
            }
            
            if (categoryFilter && !category.includes(categoryFilter)) {
                showRow = false;
            }
            
            if (dateFilter) {
                const filterDate = new Date(dateFilter).toLocaleDateString();
                const rowDate = new Date(date).toLocaleDateString();
                if (filterDate !== rowDate) {
                    showRow = false;
                }
            }
            
            row.style.display = showRow ? '' : 'none';
        });
    }
    
    function filterDashboardGrid() {
        const categoryFilter = document.getElementById('category-filter')?.value.toLowerCase() || '';
        const dateFilter = document.getElementById('date-filter')?.value || '';
        
        const items = document.querySelectorAll('.item-card');
        
        items.forEach(item => {
            const category = item.querySelector('.item-category')?.innerText.toLowerCase() || '';
            const date = item.querySelector('.item-date')?.innerText || '';
            
            let showItem = true;
            
            if (categoryFilter && !category.includes(categoryFilter)) {
                showItem = false;
            }
            
            if (dateFilter) {
                const filterDate = new Date(dateFilter).toLocaleDateString();
                const itemDate = new Date(date).toLocaleDateString();
                if (filterDate !== itemDate) {
                    showItem = false;
                }
            }
            
            item.style.display = showItem ? '' : 'none';
        });
    }
});