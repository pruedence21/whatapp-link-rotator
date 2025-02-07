<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['admin'])) {
    header('Location: login.php'); // Redirect to login page
    exit();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Rotator WhatsApp</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #007bff;
        }
        h2 {
            color: #007bff;
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
    <h1>Dashboard Rotator WhatsApp</h1>
    <a href="logout.php">Logout</a>

    <?php
    require_once 'db.php';
    $db = connectDB();

    // Fetch total redirects per phone number
    $totalRedirects = [];
    $stmt = $db->query('SELECT WA.name, COUNT(*) AS total FROM redirects INNER JOIN WA ON redirects.phone_number = WA.whatsappnumber GROUP BY WA.name');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $totalRedirects[$row['name']] = $row['total'];
    }

    echo "<h2>Total Pengalihan per Nomor</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Nama</th><th>Total Pengalihan</th></tr>";
    foreach ($totalRedirects as $name => $total) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($name) . "</td>";
        echo "<td>" . htmlspecialchars($total) . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Daily Report
    echo "<h2>Laporan Harian</h2>";
    $dailyReport = $db->query("SELECT redirects.redirect_date, WA.name, COUNT(*) AS daily_count FROM redirects INNER JOIN WA ON redirects.phone_number = WA.whatsappnumber GROUP BY redirects.redirect_date, WA.name ORDER BY redirects.redirect_date DESC");
    echo "<table border='1'>";
    echo "<tr><th>Tanggal</th><th>Nama</th><th>Jumlah Harian</th></tr>";
    while ($row = $dailyReport->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['redirect_date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['daily_count']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Monthly Report
    echo "<h2>Laporan Bulanan</h2>";
    $monthlyReport = $db->query("SELECT DATE_FORMAT(redirects.redirect_date, '%Y-%m') AS month, WA.name, COUNT(*) AS monthly_count FROM redirects INNER JOIN WA ON redirects.phone_number = WA.whatsappnumber GROUP BY month, WA.name ORDER BY month DESC");
    echo "<table border='1'>";
    echo "<tr><th>Bulan</th><th>Nama</th><th>Jumlah Bulanan</th></tr>";
    while ($row = $monthlyReport->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['month']) . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['monthly_count']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Pagination for "Semua Pengalihan"
    $resultsPerPage = 10;
    $totalResultsQuery = $db->query("SELECT COUNT(*) AS total FROM redirects");
    $totalResults = $totalResultsQuery->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalResults / $resultsPerPage);

    // Get current page number
    if (isset($_GET['page']) && is_numeric($_GET['page'])) {
        $currentPage = (int)$_GET['page'];
    } else {
        $currentPage = 1;
    }

    // Calculate the starting index
    $startIndex = ($currentPage - 1) * $resultsPerPage;

    echo "<h2>Semua Pengalihan</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Nama</th><th>Tanggal</th><th>Jumlah</th></tr>";
    $stmt = $db->query("SELECT redirects.redirect_date, WA.name, redirects.count FROM redirects INNER JOIN WA ON redirects.phone_number = WA.whatsappnumber ORDER BY redirects.redirect_date DESC LIMIT $startIndex, $resultsPerPage");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['redirect_date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['count']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Pagination links
    echo "<div class='pagination'>";
    for ($page = 1; $page <= $totalPages; $page++) {
        echo "<a href='?page=$page'>" . $page . "</a> ";
    }
    echo "</div>";
    ?>

    <?php
    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['add_number'])) {
            $newNumber = $_POST['new_number'];
            $newName = $_POST['new_name'];
            try {
                $stmt = $db->prepare("INSERT INTO WA (whatsappnumber, name) VALUES (:whatsappnumber, :name)");
                $stmt->execute([':whatsappnumber' => $newNumber, ':name' => $newName]);
                echo "<p style='color: green;'>Nomor WhatsApp berhasil ditambahkan.</p>";
            } catch (PDOException $e) {
                echo "<p style='color: red;'>Gagal menambahkan nomor WhatsApp: " . $e->getMessage() . "</p>";
            }
        } elseif (isset($_POST['delete_number'])) {
            $numberToDelete = $_POST['delete_number'];
            try {
                $stmt = $db->prepare("DELETE FROM WA WHERE whatsappnumber = :whatsappnumber");
                $stmt->execute([':whatsappnumber' => $numberToDelete]);
                echo "<p style='color: green;'>Nomor WhatsApp berhasil dihapus.</p>";
            } catch (PDOException $e) {
                echo "<p style='color: red;'>Gagal menghapus nomor WhatsApp: " . $e->getMessage() . "</p>";
            }
        }
    }

    // Fetch WhatsApp numbers from the database
    $whatsappNumbers = [];
    $stmt = $db->query('SELECT whatsappnumber, name FROM WA');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $whatsappNumbers[] = $row;
    }
    ?>

    <h2>Manage Nomor WhatsApp</h2>
    <form method="post">
        <label for="new_number">Tambah Nomor Baru:</label>
        <input type="text" id="new_number" name="new_number" required>
        <label for="new_name">Nama:</label>
        <input type="text" id="new_name" name="new_name" required>
        <button type="submit" name="add_number">Tambah</button>
    </form>

    <h2>Daftar Nomor WhatsApp</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Nomor WhatsApp</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($whatsappNumbers as $number): ?>
                <tr>
                    <td><?php echo htmlspecialchars($number['name']); ?></td>
                    <td><?php echo htmlspecialchars($number['whatsappnumber']); ?></td>
                    <td>
                        <form method="post" style="display: inline;">
                            <button type="submit" name="delete_number" value="<?php echo htmlspecialchars($number['whatsappnumber']); ?>" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
