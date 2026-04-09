<head>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<style>
/* CSS Adapter to allow the new Dashboard Sidebar to render beautifully on Legacy Dashboard Pages */
:root {
    --text-dark: #2A3042;
    --text-gray: #A6A9B6;
    --accent-purple: #623CEA;
    --accent-purple-light: #F0EDFD;
    --bg-white: #FFFFFF;
}

.sidebar {
    width: 260px;
    background: var(--bg-white);
    display: flex;
    flex-direction: column;
    padding: 40px 0;
    height: 100vh;
    border-right: 1px solid #EBECEF;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 10000;
}

.logo-box {
    display: flex;
    align-items: center;
    padding: 0 30px;
    gap: 12px;
    margin-bottom: 50px;
    text-decoration: none;
}

.logo-box img { 
    max-height: 50px; 
    width: auto; 
    object-fit: contain; 
}

.nav-list {
    display: flex;
    flex-direction: column;
    gap: 5px;
    padding: 0 20px;
    flex: 1;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 14px 20px;
    border-radius: 12px;
    color: var(--text-gray);
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.2s ease;
    font-family: 'Inter', sans-serif;
}

.nav-item i { font-size: 18px; width: 20px; text-align: center; }

.nav-item:hover:not(.active) { 
    color: var(--text-dark); 
    background: #F8F9FB; 
}

.nav-item.active { 
    background: var(--accent-purple-light); 
    color: var(--accent-purple); 
    position: relative; 
}

.nav-item.active::before {
    content: ''; 
    position: absolute; 
    left: -20px; 
    top: 50%; 
    transform: translateY(-50%);
    height: 60%; 
    width: 4px; 
    background: var(--accent-purple); 
    border-radius: 0 4px 4px 0;
}
</style>
<?php include 'sidebar.php'; ?>
