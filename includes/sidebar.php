<div class="payroll-sidebar">

    <div class="payroll-logo">
        PAYROLLKU
    </div>

    <nav class="payroll-menu">

        <a href="dashboard.php"
           class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
            Dashboard
        </a>

        <a href="penggajian.php"
           class="<?= basename($_SERVER['PHP_SELF']) == 'penggajian.php' ? 'active' : '' ?>">
            Penggajian
        </a>


    </nav>

    <div class="payroll-bottom">


        <a href="logout.php" class="payroll-logout">
            Logout
        </a>

    </div>

</div>


<style>

.payroll-sidebar {
    position: fixed;
    top: 0;
    left: 0;

    width: 230px;
    height: 100vh;

    background: #ffffff;

    border-right: 1px solid #e5e7eb;

    padding: 28px 15px;

    z-index: 9999;

    display: flex;
    flex-direction: column;

    font-family: Arial, sans-serif;
}

.payroll-logo {
    padding: 5px 12px 35px;

    color: #2563eb;

    font-size: 22px;
    font-weight: bold;
}

.payroll-menu {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.payroll-menu a {
    display: block;

    padding: 12px 13px;

    color: #64748b;

    text-decoration: none;

    font-size: 14px;
    font-weight: 600;

    border-radius: 8px;
}

.payroll-menu a:hover {
    background: #f1f5ff;
    color: #2563eb;
}

.payroll-menu a.active {
    background: #2563eb;
    color: #ffffff;
}

.payroll-bottom {
    margin-top: auto;

    border-top: 1px solid #eeeeee;

    padding-top: 15px;
}

.payroll-user {
    display: flex;
    align-items: center;

    gap: 10px;

    padding: 9px;

    background: #f8fafc;

    border-radius: 8px;

    margin-bottom: 8px;
}

.payroll-avatar {
    width: 34px;
    height: 34px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: #dbeafe;

    color: #2563eb;

    font-weight: bold;
}

.payroll-user b {
    display: block;

    color: #1e293b;

    font-size: 13px;
}

.payroll-user small {
    display: block;

    margin-top: 2px;

    color: #94a3b8;

    font-size: 10px;
}

.payroll-logout {
    display: block;

    padding: 11px 9px;

    color: #ef4444;

    text-decoration: none;

    font-size: 14px;
    font-weight: 600;

    border-radius: 8px;
}

.payroll-logout:hover {
    background: #fef2f2;
}


/* PENTING: POSISI DASHBOARD */

.layout .content {
    margin-left: 230px !important;
    width: calc(100% - 230px) !important;
}

</style>