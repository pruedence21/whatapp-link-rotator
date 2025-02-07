<?php
// Include database connection
require_once 'db.php';
$db = connectDB();

// Fetch WhatsApp numbers from the database
$whatsappNumbers = [];
$stmt = $db->query('SELECT whatsappnumber FROM WA');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $whatsappNumbers[] = $row['whatsappnumber'];
}

// Read the counts from a file
$countsFile = "counts.json"; // Changed to .json for structured data
if (file_exists($countsFile)) {
    $counts = json_decode(file_get_contents($countsFile), true);
} else {
    $counts = [];
    foreach ($whatsappNumbers as $number) {
        $counts[$number] = 0;
    }
}
// Make sure $counts has all numbers from $whatsappNumbers
foreach ($whatsappNumbers as $number) {
    if (!isset($counts[$number])) {
        $counts[$number] = 0;
    }
}

// Find the number that has been used the least
$minCount = min($counts);
$minIndex = array_search($minCount, $counts);
$index = array_search($minIndex, array_keys($counts));
$leastUsedNumber = array_keys($counts)[$index];

// Redirect the user to that number with a pre-filled message
$message = urlencode("Kak saya ingin produk ini");

// Increment the count for that number
$counts[$leastUsedNumber]++;

// Write the counts back to the file
file_put_contents($countsFile, json_encode($counts));

// Get the current date
$date = date('Y-m-d');

// Store the redirect information in the database
try {
    $stmt = $db->prepare("INSERT INTO redirects (phone_number, redirect_date, count) VALUES (:phone_number, :redirect_date, :count)");
    $stmt->execute([':phone_number' => $leastUsedNumber, ':redirect_date' => $date, ':count' => $counts[$leastUsedNumber]]);
     if ($stmt->rowCount() > 0) {
    } else {
    }
} catch (PDOException $e) {
    echo "Error inserting data: " . $e->getMessage();
}

$redirectURL = 'https://wa.me/' . $leastUsedNumber . '?text=' . $message;

?>

<!DOCTYPE html>
<html>
<head>
    <title>Loading...</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(to right, #4facfe, #00f2fe); /* Gradient background */
            color: #fff; /* White text for better contrast */
        }
        .loading-screen {
            text-align: center;
            background-color: rgba(255, 255, 255, 0.1); /* Semi-transparent white background */
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }
        .loading-text {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3); /* Text shadow for emphasis */
        }
        .spinner {
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 5px solid #fff;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    <script>
        var redirectURL = "<?php echo $redirectURL; ?>";
        setTimeout(function() {
            window.location.href = redirectURL;
        }, 1000); // 1000 milliseconds = 1 seconds
    </script>
</head>
<body>
    <div class="loading-screen">
        <p class="loading-text">Menuju Ke CS...</p>
        <div class="spinner"></div>
    </div>
</body>
</html>
<?php
// No longer write to a text file, the counts are stored in counts.json
// If you need to debug, you can uncomment the next line to dump the response
// echo $response;
?>