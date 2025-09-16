<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Aggregator</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 2rem 0;
        }

        .header h1 {
            color: white;
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 15px;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.3);
            letter-spacing: -1px;
        }

        .header p {
            color: rgba(255,255,255,0.9);
            font-size: 1.2rem;
            font-weight: 300;
        }

        .filters-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
            border: 1px solid rgba(255,255,255,0.3);
        }

        .filters-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 20px;
            align-items: end;
        }

        @media (max-width: 768px) {
            .filters-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .header h1 {
                font-size: 2rem;
            }
        }

        .form-group label {
            font-weight: 700;
            margin-bottom: 10px;
            color: #374151;
            font-size: 0.95rem;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            width: 100%;
            padding: 15px 18px;
            border: 2px solid #e5e7eb;
            border-radius: 15px;
            font-size: 15px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            font-weight: 500;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .search-input {
            position: relative;
        }

        .search-input i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1.1rem;
        }

        .search-input input {
            padding-left: 50px;
        }

        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 15px;
            font-weight: 700;
            cursor: pointer;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 15px;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }

        .articles-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            overflow: hidden;
        }

        .articles-header {
            padding: 25px 30px;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-bottom: 1px solid #e5e7eb;
            font-weight: 700;
            color: #374151;
            font-size: 1.1rem;
        }

        .loading {
            text-align: center;
            padding: 60px;
            color: #6b7280;
        }

        .loading i {
            font-size: 3rem;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
            display: block;
            color: #667eea;
        }

        .loading p {
            font-size: 1.1rem;
            font-weight: 600;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .articles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 25px;
            padding: 30px;
        }

        @media (max-width: 768px) {
            .articles-grid {
                grid-template-columns: 1fr;
                padding: 20px;
            }
        }

        .article-card {
            background: white;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #f1f5f9;
            position: relative;
        }

        .article-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        }

        .article-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            transition: transform 0.4s ease;
        }

        .article-card:hover .article-image {
            transform: scale(1.05);
        }

        .article-content {
            padding: 25px;
        }

        .article-source {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 6px 15px;
            border-radius: 25px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .article-title {
            font-size: 1.2rem;
            font-weight: 800;
            line-height: 1.3;
            margin-bottom: 15px;
            color: #1f2937;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .article-title:hover {
            color: #667eea;
        }

        .article-description {
            color: #6b7280;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .article-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 20px;
            border-top: 2px solid #f1f5f9;
            font-size: 0.85rem;
            color: #9ca3af;
            font-weight: 600;
        }

        .article-meta i {
            margin-right: 5px;
            color: #667eea;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 15px;
            padding: 30px;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        .pagination button {
            min-width: 45px;
            height: 45px;
            border: none;
            border-radius: 12px;
            background: white;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .pagination button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .pagination button.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .error-message {
            text-align: center;
            padding: 60px;
            color: #ef4444;
        }

        .error-message i {
            font-size: 4rem;
            margin-bottom: 20px;
            display: block;
        }

        .error-message h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .error-message p {
            font-size: 1.1rem;
            margin-bottom: 20px;
            color: #6b7280;
        }

        .no-articles {
            text-align: center;
            padding: 80px 20px;
            color: #6b7280;
        }

        .no-articles i {
            font-size: 5rem;
            margin-bottom: 25px;
            display: block;
            color: #d1d5db;
        }

        .no-articles h3 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .no-articles p {
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-newspaper"></i> News Aggregator</h1>
            <p>Stay updated with the latest news</p>
        </div>

        <!-- Filters -->
        <div class="filters-card">
            <div class="filters-grid">
                <div class="form-group">
                    <label for="search">Search Articles</label>
                    <div class="search-input">
                        <i class="fas fa-search"></i>
                        <input type="text" id="search" class="form-control" placeholder="Search articles...">
                    </div>
                </div>

                <div class="form-group">
                    <label for="source">Source</label>
                    <select id="source" class="form-control">
                        <option value="">All Sources</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="sortBy">Sort By</label>
                    <select id="sortBy" class="form-control">
                        <option value="published_at">Published Date</option>
                        <option value="title">Title</option>
                        <option value="author">Author</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>&nbsp;</label>
                    <button id="searchBtn" class="btn">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </div>
        </div>

        <!-- Articles Container -->
        <div class="articles-container">
            <div class="articles-header">
                <span id="resultsCount">Loading articles...</span>
            </div>

            <!-- Loading -->
            <div id="loading" class="loading" style="display: none;">
                <i class="fas fa-spinner"></i>
                <p>Loading articles...</p>
            </div>

            <!-- Error -->
            <div id="error" class="error-message" style="display: none;">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Error loading articles</h3>
                <p id="errorMessage"></p>
                <button onclick="loadArticles(1)" class="btn" style="margin-top: 16px;">
                    <i class="fas fa-redo"></i> Retry
                </button>
            </div>

            <!-- Articles Grid -->
            <div id="articlesGrid" class="articles-grid"></div>

            <!-- Pagination -->
            <div id="pagination" class="pagination" style="display: none;"></div>
        </div>
    </div>

    <script>
        // Simple Configuration
        const API_URL = '{{ url("/api") }}'; // Laravel URL helper
        let currentToken = null;
        let currentPage = 1;

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            getToken();
            loadSources();

            // Event listeners
            document.getElementById('searchBtn').addEventListener('click', function() {
                loadArticles(1);
            });

            // Enter key search
            document.getElementById('search').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    loadArticles(1);
                }
            });
        });

        // Get token
        async function getToken() {
            try {
                const response = await fetch(`${API_URL}/auth/token`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                });
                const data = await response.json();

                if (data.success) {
                    currentToken = data.data.access_token;
                    loadArticles(1); // Load articles after getting token
                }
            } catch (error) {
                console.error('Token error:', error);
                showError('Failed to connect to API');
            }
        }

        // Load sources
        async function loadSources() {
            try {
                const response = await fetch('{{ route("api.sources") }}');
                const data = await response.json();

                if (data.success) {
                    const sourceSelect = document.getElementById('source');
                    data.data.forEach(source => {
                        const option = document.createElement('option');
                        option.value = source.id;
                        option.textContent = source.name;
                        sourceSelect.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Sources error:', error);
            }
        }

        // Load articles
        async function loadArticles(page = 1) {
            currentPage = page;
            showLoading();

            try {
                // Build URL with parameters
                const params = new URLSearchParams();
                const search = document.getElementById('search').value.trim();
                const source = document.getElementById('source').value;
                const sortBy = document.getElementById('sortBy').value;

                if (search) params.append('search', search);
                if (source) params.append('source[]', source);
                params.append('sort_by', sortBy);
                params.append('sort_order', 'desc');
                params.append('per_page', '12');
                params.append('page', page.toString());

                const response = await fetch(`${API_URL}/v1/articles?${params}`, {
                    headers: {
                        'Authorization': `Bearer ${currentToken}`,
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();
                console.log('API Response:', data); // Debug log

                // Handle different response structures
                let articles = null;
                let pagination = null;

                if (data.success) {
                    // Check various possible response structures
                    if (data.articles) {
                        articles = data.articles;
                        pagination = data.pagination;
                    } else if (data.data && data.data.articles) {
                        articles = data.data.articles;
                        pagination = data.data.pagination;
                    } else if (data.data && Array.isArray(data.data)) {
                        articles = data.data;
                    } else if (Array.isArray(data)) {
                        articles = data;
                    }

                    if (articles) {
                        displayArticles(articles);
                        if (pagination) {
                            updatePagination(pagination);
                            updateStats(pagination);
                        } else {
                            // Handle case where there's no pagination info
                            document.getElementById('resultsCount').textContent = `${articles.length} articles found`;
                            document.getElementById('pagination').style.display = 'none';
                        }
                    } else {
                        showError('No articles data found in response');
                    }
                } else {
                    showError(data.message || 'API returned error');
                }

            } catch (error) {
                console.error('Articles error:', error);
                showError('Network error: ' + error.message);
            } finally {
                hideLoading();
            }
        }

        // Display articles
        function displayArticles(articles) {
            const grid = document.getElementById('articlesGrid');

            if (!articles || articles.length === 0) {
                grid.innerHTML = `
                    <div class="no-articles" style="grid-column: 1 / -1;">
                        <i class="fas fa-search"></i>
                        <h3>No articles found</h3>
                        <p>Try adjusting your search terms or filters</p>
                    </div>
                `;
                return;
            }

            grid.innerHTML = articles.map(article => `
                <div class="article-card">
                    ${article.image_url ?
                        `<img src="${article.image_url}" alt="${article.title}" class="article-image" onerror="this.style.display='none'">`
                        : `<div class="article-image" style="display: flex; align-items: center; justify-content: center; color: #9ca3af; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
                            <i class="fas fa-image" style="font-size: 2.5rem; opacity: 0.5;"></i>
                           </div>`
                    }
                    <div class="article-content">
                        <div class="article-source">${article.source ? article.source.name : 'Unknown Source'}</div>
                        <h2 class="article-title" onclick="window.open('${article.url}', '_blank')">${article.title || 'Untitled'}</h2>
                        <p class="article-description">${article.description || 'No description available.'}</p>
                        <div class="article-meta">
                            <span><i class="fas fa-user"></i> ${article.author || 'Unknown Author'}</span>
                            <span><i class="fas fa-clock"></i> ${formatDate(article.published_at)}</span>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Update pagination
        function updatePagination(pagination) {
            const paginationDiv = document.getElementById('pagination');

            if (!pagination || pagination.last_page <= 1) {
                paginationDiv.style.display = 'none';
                return;
            }

            paginationDiv.style.display = 'flex';

            let html = '';

            // Previous
            if (pagination.current_page > 1) {
                html += `<button onclick="loadArticles(${pagination.current_page - 1})">❮</button>`;
            }

            // Pages
            for (let i = Math.max(1, pagination.current_page - 2); i <= Math.min(pagination.last_page, pagination.current_page + 2); i++) {
                html += `<button onclick="loadArticles(${i})" ${i === pagination.current_page ? 'class="active"' : ''}>${i}</button>`;
            }

            // Next
            if (pagination.current_page < pagination.last_page) {
                html += `<button onclick="loadArticles(${pagination.current_page + 1})">❯</button>`;
            }

            paginationDiv.innerHTML = html;
        }

        // Update stats
        function updateStats(pagination) {
            const statsElement = document.getElementById('resultsCount');
            if (pagination) {
                statsElement.textContent = `Showing ${pagination.from || 0}-${pagination.to || 0} of ${pagination.total || 0} articles`;
            }
        }

        // Helper functions
        function formatDate(dateString) {
            if (!dateString) return 'Unknown';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }

        function showLoading() {
            document.getElementById('loading').style.display = 'block';
            document.getElementById('articlesGrid').style.display = 'none';
            document.getElementById('error').style.display = 'none';
        }

        function hideLoading() {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('articlesGrid').style.display = 'grid';
        }

        function showError(message) {
            document.getElementById('error').style.display = 'block';
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('articlesGrid').style.display = 'none';
        }
    </script>
</body>
</html>
