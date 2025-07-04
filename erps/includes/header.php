<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --dark-bg: #1a1d29;
            --card-bg: rgba(255, 255, 255, 0.1);
            --text-primary: #ffffff;
            --text-secondary: #b8c5d1;
            --border-color: rgba(255, 255, 255, 0.1);
            --shadow-light: 0 8px 32px rgba(0, 0, 0, 0.1);
            --shadow-heavy: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--dark-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Animated background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 20%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(120, 219, 255, 0.3) 0%, transparent 50%);
            z-index: -1;
            animation: backgroundShift 20s ease-in-out infinite;
        }

        @keyframes backgroundShift {
            0%, 100% { transform: rotate(0deg) scale(1); }
            33% { transform: rotate(1deg) scale(1.02); }
            66% { transform: rotate(-1deg) scale(0.98); }
        }

        /* Modern Navbar */
        .navbar {
            background: rgba(26, 29, 41, 0.95) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--shadow-light);
            padding: 1rem 0;
            position: relative;
            z-index: 1000;
        }

        .navbar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--primary-gradient);
            opacity: 0.1;
            z-index: -1;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
            position: relative;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }

        .navbar-brand::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--accent-gradient);
            opacity: 0;
            border-radius: 8px;
            z-index: -1;
            transition: opacity 0.3s ease;
        }

        .navbar-brand:hover::before {
            opacity: 0.1;
        }

        .navbar-brand:hover {
            transform: translateY(-2px);
        }

        .navbar-nav {
            gap: 0.5rem;
        }

        .nav-link {
            color: var(--text-secondary) !important;
            font-weight: 500;
            padding: 0.75rem 1.25rem !important;
            border-radius: 12px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            text-decoration: none;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--primary-gradient);
            transition: left 0.3s ease;
            z-index: -1;
            opacity: 0.8;
        }

        .nav-link:hover::before,
        .nav-link.active::before {
            left: 0;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--text-primary) !important;
            transform: translateY(-2px);
            box-shadow: var(--shadow-light);
        }

        .nav-link i {
            margin-right: 0.5rem;
            transition: transform 0.3s ease;
        }

        .nav-link:hover i {
            transform: scale(1.1);
        }

        /* Dropdown Styling */
        .dropdown-menu {
            background: rgba(26, 29, 41, 0.95) !important;
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color) !important;
            border-radius: 16px !important;
            box-shadow: var(--shadow-heavy) !important;
            padding: 1rem !important;
            margin-top: 0.5rem !important;
            animation: dropdownFade 0.3s ease;
        }

        @keyframes dropdownFade {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-item {
            color: var(--text-secondary) !important;
            padding: 0.75rem 1rem !important;
            border-radius: 12px !important;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-bottom: 0.25rem !important;
        }

        .dropdown-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--secondary-gradient);
            transition: left 0.3s ease;
            z-index: -1;
            opacity: 0.8;
        }

        .dropdown-item:hover::before {
            left: 0;
        }

        .dropdown-item:hover {
            color: var(--text-primary) !important;
            transform: translateX(5px);
            background: transparent !important;
        }

        .dropdown-item:last-child {
            margin-bottom: 0;
        }

        /* Container Styling */
        .container {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-heavy);
            padding: 2.5rem;
            margin-top: 2rem;
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(transparent, rgba(255, 255, 255, 0.1), transparent);
            animation: rotate 10s linear infinite;
            z-index: -1;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Welcome Section */
        .welcome-section {
            text-align: center;
            padding: 3rem 0;
        }

        .welcome-title {
            font-size: 3rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            animation: titleGlow 2s ease-in-out infinite alternate;
        }

        @keyframes titleGlow {
            from { filter: brightness(1); }
            to { filter: brightness(1.2); }
        }

        .welcome-subtitle {
            font-size: 1.2rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .action-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--primary-gradient);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .action-card:hover::before {
            opacity: 0.1;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-heavy);
        }

        .action-card i {
            font-size: 2rem;
            margin-bottom: 1rem;
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .action-card h5 {
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .action-card p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar-nav {
                margin-top: 1rem;
            }
            
            .welcome-title {
                font-size: 2rem;
            }
            
            .container {
                padding: 1.5rem;
                margin-top: 1rem;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--dark-bg);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-gradient);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-gradient);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="../dashboard.php">
                <i class="fas fa-chart-line"></i> ERP System
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../customers/listCustomer.php">
                            <i class="fas fa-users"></i>Customers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../items/listItem.php">
                            <i class="fas fa-box"></i>Items
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="reportsDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-chart-bar"></i>Reports
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="reportsDropdown">
                            <li><a class="dropdown-item" href="../reports/invoiceReport.php">
                                <i class="fas fa-file-invoice"></i> Invoice Report
                            </a></li>
                            <li><a class="dropdown-item" href="../reports/invoiceItemReport.php">
                                <i class="fas fa-list-alt"></i> Invoice Item Report
                            </a></li>
                            <li><a class="dropdown-item" href="../reports/itemReport.php">
                                <i class="fas fa-inventory"></i> Item Report
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Ensure dropdown functionality works
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all dropdowns
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });
            
            // Add click event for dropdown toggle
            document.getElementById('reportsDropdown').addEventListener('click', function(e) {
                e.preventDefault();
                var dropdown = bootstrap.Dropdown.getInstance(this) || new bootstrap.Dropdown(this);
                dropdown.toggle();
            });
        });
    </script>
</body>
</html>