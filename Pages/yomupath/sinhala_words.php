<?php
require_once '../../Config/SinhalaWords.php';

$sinhalaWords = new SinhalaWords();
$stats = $sinhalaWords->getStats();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sinhala Words Management System</title>
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
        }

        .breadcrumb {
            margin-bottom: 20px;
            color: #666;
        }

        .breadcrumb a {
            color: var(--primary);
            text-decoration: none;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .card i {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .card h3 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }

        .card p {
            color: #666;
        }

        .card.total {
            border-top: 4px solid var(--primary);
        }

        .card.updated {
            border-top: 4px solid var(--warning);
        }

        .card.new {
            border-top: 4px solid var(--success);
        }

        .card.categories {
            border-top: 4px solid var(--info);
        }

        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .table-header {
            padding: 15px 20px;
            background: var(--light);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
        }

        .table-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-warning {
            background: var(--warning);
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .search-box {
            display: flex;
            gap: 10px;
            position: relative;
        }

        .search-box input {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 250px;
        }

        .suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 4px 4px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .suggestion-item {
            padding: 8px 15px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }

        .suggestion-item:hover {
            background: #f5f5f5;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
            color: #666;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: var(--light);
            font-weight: 600;
            cursor: pointer;
            user-select: none;
            position: relative;
        }

        th:hover {
            background: #e0e0e0;
        }

        th.sortable::after {
            content: '\f0dc';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            right: 8px;
            opacity: 0.5;
        }

        th.sort-asc::after {
            content: '\f0de';
            opacity: 1;
        }

        th.sort-desc::after {
            content: '\f0dd';
            opacity: 1;
        }

        tr:hover {
            background: #f9f9f9;
        }

        .actions {
            display: flex;
            gap: 5px;
        }

        .action-btn {
            padding: 5px 8px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
        }

        .view-btn {
            background: var(--info);
            color: white;
        }

        .edit-btn {
            background: var(--warning);
            color: white;
        }

        .delete-btn {
            background: var(--danger);
            color: white;
        }

        .category-badge {
            background: var(--info);
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: var(--light);
            border-top: 1px solid #ddd;
        }

        .pagination-info {
            color: #666;
        }

        .pagination-controls {
            display: flex;
            gap: 5px;
        }

        .page-btn {
            padding: 5px 10px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            min-width: 35px;
        }

        .page-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            width: 700px;
            max-width: 90%;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            padding: 15px 20px;
            background: var(--primary);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .modal-body {
            padding: 20px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .modal-footer {
            padding: 15px 20px;
            background: var(--light);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        /* Alert Styles */
        .alert {
            padding: 12px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            display: none;
            justify-content: space-between;
            align-items: center;
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

        .alert-close {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
        }

        /* View Modal Styles */
        .view-content {
            padding: 10px 0;
        }

        .view-field {
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }

        .view-field:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .view-field label {
            display: block;
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .view-value {
            background: #f8f9fa;
            padding: 12px 15px;
            border-radius: 4px;
            border-left: 4px solid var(--primary);
            line-height: 1.6;
            word-wrap: break-word;
            white-space: pre-wrap;
            font-size: 1rem;
            color: #333;
        }

        .view-value:empty::before {
            content: '-';
            color: #999;
            font-style: italic;
        }

        /* Responsive Styles */
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
            
            .search-box {
                width: 100%;
            }
            
            .search-box input {
                width: 100%;
            }
            
            .table-header {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
            
            .table-container {
                overflow-x: auto;
            }
            
            table {
                min-width: 1000px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-book"></i>
            <h2>සිංහල ශබ්දකෝෂ දත්ත පද්ධතිය</h2>
        </div>
        <div class="sidebar-menu">
            <ul>
                <li><a href="yomupath.php"><i class="fas fa-home"></i> <span>යොමු පත් අකාරාදිය</span></a></li>
                <li class="active"><a href="sinhala_words.php"><i class="fas fa-language"></i> <span>සිංහල ශබ්දකෝෂ දත්ත යොමුව</span></a></li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>සිංහල ශබ්දකෝෂ දත්ත පද්ධතිය</h1>
            <div class="user-info">
                <i class="fas fa-user-circle"></i> Admin User
            </div>
        </div>

        <div class="breadcrumb">
            <a href="yomupath.php">Dashboard</a> / <span>Sinhala Words</span>
        </div>

        <!-- Alert Messages -->
        <div class="alert alert-success" id="successAlert">
            <div id="successMessage"></div>
            <button class="alert-close">&times;</button>
        </div>
        <div class="alert alert-error" id="errorAlert">
            <div id="errorMessage"></div>
            <button class="alert-close">&times;</button>
        </div>

        <!-- Stats Cards -->
        <div class="stats-cards">
            <div class="card total">
                <i class="fas fa-database"></i>
                <h3 id="totalWords"><?php echo number_format($stats['total']); ?></h3>
                <p>Total Words</p>
            </div>
            <div class="card updated">
                <i class="fas fa-edit"></i>
                <h3 id="updatedToday"><?php echo number_format($stats['updated_today']); ?></h3>
                <p>Updated Today</p>
            </div>
            <div class="card new">
                <i class="fas fa-plus"></i>
                <h3 id="newThisWeek"><?php echo number_format($stats['new_this_week']); ?></h3>
                <p>New This Week</p>
            </div>
            <div class="card categories">
                <i class="fas fa-tags"></i>
                <h3 id="totalCategories"><?php echo number_format($stats['categories']); ?></h3>
                <p>Categories</p>
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-container">
            <div class="table-header">
                <h3>Sinhala Words</h3>
                <div class="table-actions">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Search words...">
                        <div class="suggestions" id="suggestions"></div>
                        <button class="btn btn-primary" id="searchBtn"><i class="fas fa-search"></i> Search</button>
                        <button class="btn btn-warning" id="clearSearchBtn"><i class="fas fa-times"></i> Clear</button>
                    </div>
                    <button class="btn btn-success" id="addWordBtn"><i class="fas fa-plus"></i> Add New Word</button>
                </div>
            </div>

            <div class="loading" id="loadingIndicator">
                <div class="spinner"></div>
                <div>Loading data...</div>
            </div>

            <table id="dataTable">
                <thead>
                    <tr>
                        <th class="sortable" data-sort="id">ID</th>
                        <th class="sortable" data-sort="වචනය">වචනය</th>
                        <th class="sortable" data-sort="අක්ෂර_පරිවර්තනය">අක්ෂර_පරිවර්තනය</th>
                        <th class="sortable" data-sort="Transliteration">Transliteration</th>
                        <th class="sortable" data-sort="ශබ්දකෝෂ_කලාපය">ශබ්දකෝෂ_කලාපය</th>
                        <th class="sortable" data-sort="කර්තෘවරයා">කර්තෘවරයා</th>
                        <th class="sortable" data-sort="Timestamp">Timestamp</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Data will be loaded here -->
                </tbody>
            </table>

            <div class="pagination" id="pagination">
                <div class="pagination-info" id="paginationInfo">
                    <!-- Pagination info will be loaded here -->
                </div>
                <div class="pagination-controls" id="paginationControls">
                    <!-- Pagination controls will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal" id="wordModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add New Word</h3>
                <button class="close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <form id="wordForm">
                    <input type="hidden" id="recordId" value="">
                    <div class="form-group">
                        <label for="word">වචනය (Word) *</label>
                        <input type="text" id="word" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="akshara">අක්ෂර_පරිවර්තනය (Character Conversion)</label>
                        <input type="text" id="akshara" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="transliteration">Transliteration</label>
                        <input type="text" id="transliteration" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="category">ශබ්දකෝෂ_කලාපය (Dictionary Category)</label>
                        <input type="text" id="category" class="form-control" list="categoriesList">
                        <datalist id="categoriesList">
                            <!-- Categories will be loaded here -->
                        </datalist>
                    </div>
                    <div class="form-group">
                        <label for="author">කර්තෘවරයා (Author)</label>
                        <input type="text" id="author" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-warning" id="cancelBtn">Cancel</button>
                <button class="btn btn-success" id="saveBtn">Save Word</button>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal" id="viewModal">
        <div class="modal-content" style="width: 800px;">
            <div class="modal-header">
                <h3 id="viewModalTitle">Word Details</h3>
                <button class="close-btn" onclick="closeViewModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="view-content">
                    <div class="view-field">
                        <label>වචනය (Word):</label>
                        <div class="view-value" id="viewWord"></div>
                    </div>
                    <div class="view-field">
                        <label>අක්ෂර_පරිවර්තනය (Character Conversion):</label>
                        <div class="view-value" id="viewAkshara"></div>
                    </div>
                    <div class="view-field">
                        <label>Transliteration:</label>
                        <div class="view-value" id="viewTransliteration"></div>
                    </div>
                    <div class="view-field">
                        <label>ශබ්දකෝෂ_කලාපය (Dictionary Category):</label>
                        <div class="view-value" id="viewCategory"></div>
                    </div>
                    <div class="view-field">
                        <label>කර්තෘවරයා (Author):</label>
                        <div class="view-value" id="viewAuthor"></div>
                    </div>
                    <div class="view-field">
                        <label>Timestamp:</label>
                        <div class="view-value" id="viewTimestamp"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-warning" onclick="closeViewModal()">Close</button>
                <button class="btn btn-primary" id="editFromViewBtn">Edit This Word</button>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let currentPage = 1;
        let currentLimit = 50;
        let currentSearch = '';
        let currentSortBy = 'id';
        let currentSortOrder = 'ASC';
        let editingRecordId = null;

        // DOM elements
        const searchInput = document.getElementById('searchInput');
        const searchBtn = document.getElementById('searchBtn');
        const clearSearchBtn = document.getElementById('clearSearchBtn');
        const addWordBtn = document.getElementById('addWordBtn');
        const wordModal = document.getElementById('wordModal');
        const closeBtn = document.querySelector('.close-btn');
        const cancelBtn = document.getElementById('cancelBtn');
        const saveBtn = document.getElementById('saveBtn');
        const tableBody = document.getElementById('tableBody');
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationControls = document.getElementById('paginationControls');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const suggestions = document.getElementById('suggestions');
        const successAlert = document.getElementById('successAlert');
        const errorAlert = document.getElementById('errorAlert');

        // Initialize the application
        document.addEventListener('DOMContentLoaded', function() {
            loadData();
            loadCategories();
            setupEventListeners();
        });

        function setupEventListeners() {
            // Search functionality
            searchBtn.addEventListener('click', function() {
                currentSearch = searchInput.value.trim();
                currentPage = 1;
                loadData();
            });

            clearSearchBtn.addEventListener('click', function() {
                searchInput.value = '';
                currentSearch = '';
                currentPage = 1;
                loadData();
            });

            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    currentSearch = searchInput.value.trim();
                    currentPage = 1;
                    loadData();
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

            // Modal functionality
            addWordBtn.addEventListener('click', openAddModal);
            closeBtn.addEventListener('click', closeModal);
            cancelBtn.addEventListener('click', closeModal);
            saveBtn.addEventListener('click', saveRecord);

            // Close modal when clicking outside
            window.addEventListener('click', function(e) {
                if (e.target === wordModal) {
                    closeModal();
                }
                if (e.target === document.getElementById('viewModal')) {
                    closeViewModal();
                }
            });

            // Table sorting
            document.querySelectorAll('.sortable').forEach(header => {
                header.addEventListener('click', function() {
                    const sortBy = this.dataset.sort;
                    if (currentSortBy === sortBy) {
                        currentSortOrder = currentSortOrder === 'ASC' ? 'DESC' : 'ASC';
                    } else {
                        currentSortBy = sortBy;
                        currentSortOrder = 'ASC';
                    }
                    currentPage = 1;
                    loadData();
                });
            });

            // Alert close buttons
            document.querySelectorAll('.alert-close').forEach(btn => {
                btn.addEventListener('click', function() {
                    this.parentElement.style.display = 'none';
                });
            });
        }

        function loadData() {
            showLoading(true);
            
            const params = new URLSearchParams({
                action: 'get_data',
                page: currentPage,
                limit: currentLimit,
                search: currentSearch,
                sortBy: currentSortBy,
                sortOrder: currentSortOrder
            });

            fetch(`words_api.php?${params}`)
                .then(response => response.json())
                .then(data => {
                    showLoading(false);
                    if (data.data) {
                        renderTable(data.data);
                        renderPagination(data);
                        updateSortHeaders();
                    } else {
                        showError('Failed to load data');
                    }
                })
                .catch(error => {
                    showLoading(false);
                    showError('Error loading data: ' + error.message);
                });
        }

        function loadCategories() {
            fetch('words_api.php?action=get_categories')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        const categoriesList = document.getElementById('categoriesList');
                        categoriesList.innerHTML = '';
                        data.data.forEach(category => {
                            const option = document.createElement('option');
                            option.value = category;
                            categoriesList.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading categories:', error);
                });
        }

        function renderTable(data) {
            tableBody.innerHTML = '';
            
            if (data.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 20px;">No records found</td></tr>';
                return;
            }

            data.forEach(record => {
                const row = document.createElement('tr');
                
                // Truncate long text for display
                const truncateText = (text, maxLength = 50) => {
                    if (!text) return '-';
                    if (text.length <= maxLength) return escapeHtml(text);
                    return escapeHtml(text.substring(0, maxLength)) + '...';
                };
                
                row.innerHTML = `
                    <td>${record.id}</td>
                    <td><strong>${escapeHtml(record.වචනය || '-')}</strong></td>
                    <td>${truncateText(record.අක්ෂර_පරිවර්තනය, 30)}</td>
                    <td>${escapeHtml(record.Transliteration || '-')}</td>
                    <td>${record.ශබ්දකෝෂ_කලාපය ? `<span class="category-badge">${escapeHtml(record.ශබ්දකෝෂ_කලාපය)}</span>` : '-'}</td>
                    <td>${escapeHtml(record.කර්තෘවරයා || '-')}</td>
                    <td>${formatDate(record.Timestamp)}</td>
                    <td class="actions">
                        <button class="action-btn view-btn" onclick="viewRecord(${record.id})" title="View Details">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="action-btn edit-btn" onclick="editRecord(${record.id})" title="Edit">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="action-btn delete-btn" onclick="deleteRecord(${record.id})" title="Delete">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }

        function renderPagination(data) {
            const start = ((data.page - 1) * data.limit) + 1;
            const end = Math.min(data.page * data.limit, data.total);
            
            paginationInfo.textContent = `Showing ${start} - ${end} of ${data.total.toLocaleString()} records`;
            
            // Clear existing controls
            paginationControls.innerHTML = '';
            
            // Previous button
            const prevBtn = createPageButton('‹', data.page - 1, data.page <= 1);
            paginationControls.appendChild(prevBtn);
            
            // Page numbers
            const totalPages = data.totalPages;
            const startPage = Math.max(1, data.page - 2);
            const endPage = Math.min(totalPages, data.page + 2);
            
            if (startPage > 1) {
                paginationControls.appendChild(createPageButton('1', 1));
                if (startPage > 2) {
                    paginationControls.appendChild(createPageButton('...', null, true));
                }
            }
            
            for (let i = startPage; i <= endPage; i++) {
                paginationControls.appendChild(createPageButton(i, i, false, i === data.page));
            }
            
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    paginationControls.appendChild(createPageButton('...', null, true));
                }
                paginationControls.appendChild(createPageButton(totalPages, totalPages));
            }
            
            // Next button
            const nextBtn = createPageButton('›', data.page + 1, data.page >= totalPages);
            paginationControls.appendChild(nextBtn);
        }

        function createPageButton(text, page, disabled = false, active = false) {
            const button = document.createElement('button');
            button.className = `page-btn ${active ? 'active' : ''}`;
            button.textContent = text;
            button.disabled = disabled;
            
            if (!disabled && page !== null) {
                button.addEventListener('click', () => {
                    currentPage = page;
                    loadData();
                });
            }
            
            return button;
        }

        function updateSortHeaders() {
            document.querySelectorAll('.sortable').forEach(header => {
                header.classList.remove('sort-asc', 'sort-desc');
                if (header.dataset.sort === currentSortBy) {
                    header.classList.add(currentSortOrder === 'ASC' ? 'sort-asc' : 'sort-desc');
                }
            });
        }

        function loadSuggestions(term) {
            fetch(`words_api.php?action=get_suggestions&term=${encodeURIComponent(term)}`)
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
                                currentSearch = suggestion;
                                currentPage = 1;
                                loadData();
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

        function openAddModal() {
            editingRecordId = null;
            document.getElementById('modalTitle').textContent = 'Add New Word';
            document.getElementById('wordForm').reset();
            document.getElementById('recordId').value = '';
            wordModal.style.display = 'flex';
        }

        function editRecord(id) {
            editingRecordId = id;
            document.getElementById('modalTitle').textContent = 'Edit Word';
            
            fetch(`words_api.php?action=get_record&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        const record = data.data;
                        document.getElementById('recordId').value = record.id;
                        document.getElementById('word').value = record.වචනය || '';
                        document.getElementById('akshara').value = record.අක්ෂර_පරිවර්තනය || '';
                        document.getElementById('transliteration').value = record.Transliteration || '';
                        document.getElementById('category').value = record.ශබ්දකෝෂ_කලාපය || '';
                        document.getElementById('author').value = record.කර්තෘවරයා || '';
                        wordModal.style.display = 'flex';
                    } else {
                        showError('Failed to load record data');
                    }
                })
                .catch(error => {
                    showError('Error loading record: ' + error.message);
                });
        }

        function deleteRecord(id) {
            if (confirm('Are you sure you want to delete this word? This action cannot be undone.')) {
                fetch(`words_api.php?action=delete_record&id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showSuccess('Word deleted successfully');
                            loadData();
                        } else {
                            showError(data.message || 'Failed to delete word');
                        }
                    })
                    .catch(error => {
                        showError('Error deleting word: ' + error.message);
                    });
            }
        }

        function saveRecord() {
            const formData = {
                id: document.getElementById('recordId').value,
                word: document.getElementById('word').value,
                akshara: document.getElementById('akshara').value,
                transliteration: document.getElementById('transliteration').value,
                category: document.getElementById('category').value,
                author: document.getElementById('author').value
            };

            // Validate required fields
            if (!formData.word) {
                showError('Please fill in the required field (වචනය/Word)');
                return;
            }

            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving...';

            fetch('words_api.php?action=save_record', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save Word';
                
                if (data.success) {
                    showSuccess(data.message);
                    closeModal();
                    loadData();
                } else {
                    showError(data.message || 'Failed to save word');
                }
            })
            .catch(error => {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save Word';
                showError('Error saving word: ' + error.message);
            });
        }

        function closeModal() {
            wordModal.style.display = 'none';
            document.getElementById('wordForm').reset();
            editingRecordId = null;
        }

        function viewRecord(id) {
            fetch(`words_api.php?action=get_record&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        const record = data.data;
                        
                        // Populate view modal
                        document.getElementById('viewWord').textContent = record.වචනය || '-';
                        document.getElementById('viewAkshara').textContent = record.අක්ෂර_පරිවර්තනය || '-';
                        document.getElementById('viewTransliteration').textContent = record.Transliteration || '-';
                        document.getElementById('viewCategory').textContent = record.ශබ්දකෝෂ_කලාපය || '-';
                        document.getElementById('viewAuthor').textContent = record.කර්තෘවරයා || '-';
                        document.getElementById('viewTimestamp').textContent = formatDate(record.Timestamp);
                        
                        // Set up edit button
                        document.getElementById('editFromViewBtn').onclick = () => {
                            closeViewModal();
                            editRecord(id);
                        };
                        
                        // Show view modal
                        document.getElementById('viewModal').style.display = 'flex';
                    } else {
                        showError('Failed to load record data');
                    }
                })
                .catch(error => {
                    showError('Error loading record: ' + error.message);
                });
        }

        function closeViewModal() {
            document.getElementById('viewModal').style.display = 'none';
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

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString();
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !suggestions.contains(e.target)) {
                suggestions.style.display = 'none';
            }
        });
    </script>
</body>
</html>
