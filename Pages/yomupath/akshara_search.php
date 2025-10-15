<?php
require_once '../../Config/auth.php';
require_once '../../Config/AksharaVinyasaData.php';

$aksharaData = new AksharaVinyasaData();
$stats = $aksharaData->getStats();
$user = auth_current_user();
$isAdmin = auth_is_admin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>අක්ෂර වින්‍යාස ශබ්දකෝෂය - Search</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --primary: #3498db;
            --secondary: #2c3e50;
            --light: #ecf0f1;
            --dark: #34495e;
            --success: #2ecc71;
            --warning: #f39c12;
            --danger: #e74c3c;
            --info: #17a2b8;
            --sidebar-width: 250px;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: #f5f7fa;
            color: #333;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--secondary);
            color: white;
            height: 100vh;
            position: fixed;
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 20px;
            background: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-header h2 {
            font-size: 1.5rem;
        }

        .sidebar-menu {
            padding: 15px 0;
        }

        .sidebar-menu ul {
            list-style: none;
        }

        .sidebar-menu li {
            padding: 12px 20px;
            transition: all 0.3s;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-menu li:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-menu li.active {
            background: var(--primary);
            border-left: 4px solid white;
        }

        .sidebar-menu a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: all 0.3s;
        }


        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }

        .header h1 {
            color: var(--secondary);
            font-size: 1.8rem;
        }

        .user-info {
            color: #666;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .search-section-content {
            padding: 20px;
        }

        .search-container {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .search-box {
            position: relative;
            flex: 1;
            min-width: 300px;
            max-width: 500px;
        }

        .search-input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 50px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .search-input::placeholder {
            color: #999;
        }

        .suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #e0e0e0;
            border-top: none;
            border-radius: 0 0 15px 15px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .suggestion-item {
            padding: 12px 20px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s;
        }

        .suggestion-item:hover {
            background: #f8f9fa;
        }

        .suggestion-item:last-child {
            border-bottom: none;
        }

        .btn {
            padding: 15px 25px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            min-width: 120px;
            justify-content: center;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }

        .btn-warning {
            background: var(--warning);
            color: white;
        }

        .btn-warning:hover {
            background: #e67e22;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(243, 156, 18, 0.3);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-success:hover {
            background: #27ae60;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .stats-bar {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .stat-item {
            text-align: center;
            color: #666;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary);
        }

        .stat-label {
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .results-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .results-section-content {
            padding: 20px;
        }

        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
        }

        .results-count {
            font-size: 1.1rem;
            color: #666;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
            display: none;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .results-grid {
            display: grid;
            gap: 20px;
        }

        .result-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 25px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .result-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .word-title {
            font-size: 1.4rem;
            font-weight: bold;
            color: var(--secondary);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .category-badge {
            background: var(--info);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .meaning-section {
            margin: 15px 0;
        }

        .meaning-label {
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .meaning-text {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid var(--primary);
            line-height: 1.6;
        }

        .etymology-section {
            margin-top: 15px;
        }

        .etymology-label {
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .etymology-text {
            background: #f0f8ff;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid var(--info);
            line-height: 1.6;
            color: #555;
        }

        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .no-results i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        .no-results h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #999;
        }

        .no-results p {
            font-size: 1.1rem;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar-header h2, .sidebar-menu span {
                display: none;
            }
            
            .sidebar-menu li {
                justify-content: center;
            }
            
            .main-content {
                margin-left: 70px;
            }
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .search-container {
                flex-direction: column;
                align-items: center;
            }

            .search-box {
                min-width: 100%;
                max-width: 100%;
            }

            .stats-bar {
                gap: 20px;
            }

            .results-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 1.5rem;
            }

            .search-input {
                padding: 12px 15px;
                font-size: 1rem;
            }

            .btn {
                padding: 12px 20px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../../assets/logo.png" alt="Logo" style="width:28px;height:28px;display:block;" />
            <h2>සිංහල ශබ්දකෝෂ දත්ත පද්ධතිය</h2>
        </div>
        <div class="sidebar-menu">
            <ul>
                <li><a href="yomupath.php"><i class="fas fa-home"></i> <span>යොමු පත් අකාරාදිය</span></a></li>
                <li><a href="sinhala_words.php"><i class="fas fa-language"></i> <span>සිංහල ශබ්දකෝෂ දත්ත යොමුව</span></a></li>
                <li><a href="akshara_vinyasa.php"><i class="fas fa-spell-check"></i> <span>අක්ෂර වින්‍යාස ශබ්දකෝෂය</span></a></li>
                <li class="active"><i class="fas fa-search"></i> <span>අක්ෂර වින්‍යාස සෙවීම</span></li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>අක්ෂර වින්‍යාස සෙවීම - Akshara Vinyasa Search</h1>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <?php if ($user) { echo htmlspecialchars($user['name'] . ' (' . $user['email'] . ')'); } else { echo 'Guest'; } ?>
                <?php if ($user) { ?>
                    | <a href="#" id="logoutLink" style="color: #e74c3c; text-decoration: none;">Logout</a>
                <?php } else { ?>
                    | <a href="login.php" style="color: #3498db; text-decoration: none;">Login</a>
                    | <a href="register.php" style="color: #2ecc71; text-decoration: none;">Register</a>
                <?php } ?>
            </div>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <div class="search-section-content">
            <!-- Alert Messages -->
            <div class="alert alert-success" id="successAlert">
                <i class="fas fa-check-circle"></i>
                <div id="successMessage"></div>
            </div>
            <div class="alert alert-error" id="errorAlert">
                <i class="fas fa-exclamation-circle"></i>
                <div id="errorMessage"></div>
            </div>

            <!-- Stats Bar -->
            <div class="stats-bar">
                <div class="stat-item">
                    <div class="stat-number"><?php echo number_format($stats['total']); ?></div>
                    <div class="stat-label">Total Words</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo number_format($stats['categories']); ?></div>
                    <div class="stat-label">Categories</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo number_format($stats['recent']); ?></div>
                    <div class="stat-label">Recent Entries</div>
                </div>
            </div>

            <!-- Search Container -->
            <div class="search-container">
                <div class="search-box">
                    <input type="text" id="searchInput" class="search-input" placeholder="Search for words, meanings, or categories...">
                    <div class="suggestions" id="suggestions"></div>
                </div>
                <button class="btn btn-primary" id="searchBtn">
                    <i class="fas fa-search"></i>
                    Search
                </button>
                <button class="btn btn-warning" id="resetBtn">
                    <i class="fas fa-undo"></i>
                    Reset
                </button>
                <button class="btn btn-success" id="printBtn" style="display: none;">
                    <i class="fas fa-file-pdf"></i>
                    Print PDF
                </button>
            </div>
            </div>
        </div>

        <!-- Results Section -->
        <div class="results-section">
            <div class="results-section-content">
            <div class="loading" id="loadingIndicator">
                <div class="spinner"></div>
                <div>Searching dictionary...</div>
            </div>

            <div id="resultsContainer" style="display: none;">
                <div class="results-header">
                    <div class="results-count" id="resultsCount"></div>
                </div>
                <div class="results-grid" id="resultsGrid">
                    <!-- Results will be loaded here -->
                </div>
            </div>

            <div class="no-results" id="noResults" style="display: none;">
                <i class="fas fa-search"></i>
                <h3>No Results Found</h3>
                <p>Try searching with different keywords or check your spelling.</p>
            </div>
            </div>
        </div>
    </div>

    <script>
        const IS_ADMIN = <?php echo $isAdmin ? 'true' : 'false'; ?>;
        // Global variables
        let currentSearch = '';
        let searchResults = [];

        // DOM elements
        const searchInput = document.getElementById('searchInput');
        const searchBtn = document.getElementById('searchBtn');
        const resetBtn = document.getElementById('resetBtn');
        const printBtn = document.getElementById('printBtn');
        const suggestions = document.getElementById('suggestions');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const resultsContainer = document.getElementById('resultsContainer');
        const resultsGrid = document.getElementById('resultsGrid');
        const resultsCount = document.getElementById('resultsCount');
        const noResults = document.getElementById('noResults');
        const successAlert = document.getElementById('successAlert');
        const errorAlert = document.getElementById('errorAlert');

        // Initialize the application
        document.addEventListener('DOMContentLoaded', function() {
            setupEventListeners();
        });

        function setupEventListeners() {
            // Search functionality
            searchBtn.addEventListener('click', performSearch);
            resetBtn.addEventListener('click', resetSearch);
            printBtn.addEventListener('click', printToPDF);

            // Enter key search
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });

            // Search suggestions
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const term = this.value.trim();
                
                if (term.length > 2) {
                    searchTimeout = setTimeout(() => {
                        loadSuggestions(term);
                    }, 300);
                } else {
                    suggestions.style.display = 'none';
                }
            });

            // Hide suggestions when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !suggestions.contains(e.target)) {
                    suggestions.style.display = 'none';
                }
            });
            // Logout
            const logoutLink = document.getElementById('logoutLink');
            if (logoutLink) {
                logoutLink.addEventListener('click', async function(e) {
                    e.preventDefault();
                    try {
                        await fetch('auth_api.php?action=logout');
                        window.location.reload();
                    } catch (err) {
                        window.location.reload();
                    }
                });
            }
        }

        function performSearch() {
            currentSearch = searchInput.value.trim();
            
            if (!currentSearch) {
                showError('Please enter a search term');
                return;
            }

            showLoading(true);
            hideAllResults();

            const params = new URLSearchParams({
                action: 'search_words',
                term: currentSearch
            });

            fetch(`akshara_search_api.php?${params}`)
                .then(response => response.json())
                .then(data => {
                    showLoading(false);
                    if (data.success) {
                        searchResults = data.data;
                        displayResults(searchResults);
                        showSuccess(`Found ${searchResults.length} result(s) for "${currentSearch}"`);
                    } else {
                        showError(data.message || 'Search failed');
                        showNoResults();
                    }
                })
                .catch(error => {
                    showLoading(false);
                    showError('Error performing search: ' + error.message);
                    showNoResults();
                });
        }

        function resetSearch() {
            searchInput.value = '';
            currentSearch = '';
            searchResults = [];
            suggestions.style.display = 'none';
            hideAllResults();
            printBtn.style.display = 'none';
            hideAlerts();
        }

        function loadSuggestions(term) {
            fetch(`akshara_search_api.php?action=get_suggestions&term=${encodeURIComponent(term)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        suggestions.innerHTML = '';
                        data.data.forEach(suggestion => {
                            const item = document.createElement('div');
                            item.className = 'suggestion-item';
                            item.textContent = suggestion;
                            item.addEventListener('click', () => {
                                searchInput.value = suggestion;
                                suggestions.style.display = 'none';
                                performSearch();
                            });
                            suggestions.appendChild(item);
                        });
                        suggestions.style.display = 'block';
                    } else {
                        suggestions.style.display = 'none';
                    }
                })
                .catch(() => {
                    suggestions.style.display = 'none';
                });
        }

        function displayResults(results) {
            if (results.length === 0) {
                showNoResults();
                return;
            }

            resultsCount.textContent = `Found ${results.length} result(s) for "${currentSearch}"`;
            resultsGrid.innerHTML = '';

            results.forEach(result => {
                const card = document.createElement('div');
                card.className = 'result-card';
                card.innerHTML = `
                    <div class="word-title">
                        ${escapeHtml(result.වචනය)}
                        ${result.ප්‍රවර්ගය ? `<span class="category-badge">${escapeHtml(result.ප්‍රවර්ගය)}</span>` : ''}
                    </div>
                    <div class="meaning-section">
                        <div class="meaning-label">අර්ථය (Meaning):</div>
                        <div class="meaning-text">${escapeHtml(result.අර්ථය || 'No meaning available')}</div>
                    </div>
                    ${result.නිරුක්තිය ? `
                    <div class="etymology-section">
                        <div class="etymology-label">නිරුක්තිය (Etymology):</div>
                        <div class="etymology-text">${escapeHtml(result.නිරුක්තිය)}</div>
                    </div>
                    ` : ''}
                `;
                resultsGrid.appendChild(card);
            });

            resultsContainer.style.display = 'block';
            printBtn.style.display = 'flex';
        }

        function showNoResults() {
            noResults.style.display = 'block';
            resultsContainer.style.display = 'none';
            printBtn.style.display = 'none';
        }

        function hideAllResults() {
            resultsContainer.style.display = 'none';
            noResults.style.display = 'none';
        }

        function showLoading(show) {
            loadingIndicator.style.display = show ? 'block' : 'none';
        }

        function showSuccess(message) {
            document.getElementById('successMessage').textContent = message;
            successAlert.style.display = 'flex';
            setTimeout(() => {
                successAlert.style.display = 'none';
            }, 5000);
        }

        function showError(message) {
            document.getElementById('errorMessage').textContent = message;
            errorAlert.style.display = 'flex';
            setTimeout(() => {
                errorAlert.style.display = 'none';
            }, 5000);
        }

        function hideAlerts() {
            successAlert.style.display = 'none';
            errorAlert.style.display = 'none';
        }

        function printToPDF() {
            if (searchResults.length === 0) {
                showError('No results to print');
                return;
            }

            // Create a new window for printing
            const printWindow = window.open('', '_blank');
            const printContent = generatePrintContent();
            
            printWindow.document.write(printContent);
            printWindow.document.close();
            printWindow.print();
        }

        function generatePrintContent() {
            const currentDate = new Date().toLocaleDateString();
            let content = `
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>අක්ෂර වින්‍යාස ශබ්දකෝෂය - Search Results</title>
                    <style>
                        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 20px; color: #333; }
                        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
                        .header h1 { color: #2c3e50; margin-bottom: 10px; }
                        .search-info { margin-bottom: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px; }
                        .result-item { margin-bottom: 25px; padding: 20px; border: 1px solid #ddd; border-radius: 8px; page-break-inside: avoid; }
                        .word-title { font-size: 1.3em; font-weight: bold; color: #2c3e50; margin-bottom: 10px; }
                        .category { background: #17a2b8; color: white; padding: 3px 8px; border-radius: 12px; font-size: 0.8em; margin-left: 10px; }
                        .meaning-label, .etymology-label { font-weight: bold; color: #2c3e50; margin-bottom: 5px; }
                        .meaning-text, .etymology-text { background: #f8f9fa; padding: 10px; border-radius: 5px; margin-bottom: 10px; line-height: 1.5; }
                        .etymology-text { background: #f0f8ff; }
                        @media print { body { margin: 0; } .result-item { page-break-inside: avoid; } }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>අක්ෂර වින්‍යාස ශබ්දකෝෂය</h1>
                        <p>Akshara Vinyasa Dictionary - Search Results</p>
                        <p>Generated on: ${currentDate}</p>
                    </div>
                    <div class="search-info">
                        <strong>Search Term:</strong> "${currentSearch}"<br>
                        <strong>Results Found:</strong> ${searchResults.length} entries
                    </div>
            `;

            searchResults.forEach((result, index) => {
                content += `
                    <div class="result-item">
                        <div class="word-title">
                            ${index + 1}. ${escapeHtml(result.වචනය)}
                            ${result.ප්‍රවර්ගය ? `<span class="category">${escapeHtml(result.ප්‍රවර්ගය)}</span>` : ''}
                        </div>
                        <div class="meaning-label">අර්ථය (Meaning):</div>
                        <div class="meaning-text">${escapeHtml(result.අර්ථය || 'No meaning available')}</div>
                        ${result.නිරුක්තිය ? `
                        <div class="etymology-label">නිරුක්තිය (Etymology):</div>
                        <div class="etymology-text">${escapeHtml(result.නිරුක්තිය)}</div>
                        ` : ''}
                    </div>
                `;
            });

            content += `
                </body>
                </html>
            `;

            return content;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>
