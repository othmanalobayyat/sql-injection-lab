<?php
// Change this for each student
$studentName = "Student Name";

// ===== SQL Injection Lab - Level 1 =====
// Intentionally vulnerable - Educational use only

$host = "db";
$user = "lab_user";
$pass = "lab_pass";
$db   = "sqli_lab";

// Show MySQL errors on the page instead of throwing a PHP fatal error
mysqli_report(MYSQLI_REPORT_OFF);

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed");
}

$query = "";
$error = "";
$result = null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Vulnerable query (no sanitization)
    $query = "SELECT id, name, price FROM products WHERE id = $id";
    $result = $conn->query($query);

    if ($result === false) {
        $error = $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Injection Lab - Palestine Ahliya University</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
            margin: 0;
            background: #f8f6f4;
            color: #333;
            line-height: 1.6;
        }
        .top-bar {
            background: #f5f0e8;
            padding: 8px 30px;
            font-size: 13px;
            color: #5a3e36;
            text-align: right;
        }
        .header {
            background: #6b1e1e;
            color: white;
            padding: 18px 40px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        .logo {
            height: 75px;
            width: auto;
            background: white;
            padding: 6px;
            border-radius: 4px;
        }
        .header-text h1 { margin: 0; font-size: 22px; font-weight: 600; }
        .header-text p { margin: 3px 0 0; font-size: 14px; opacity: 0.92; }
        .container {
            max-width: 960px;
            margin: 30px auto;
            background: white;
            padding: 35px;
            border-radius: 6px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.06);
        }
        .student-box {
            background: #fdf6f0;
            border-left: 4px solid #6b1e1e;
            padding: 12px 18px;
            margin-bottom: 25px;
            font-weight: 600;
            color: #6b1e1e;
        }
        .warning {
            background: #fff8e1;
            border-left: 4px solid #d4a017;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
        }
        .section { margin: 30px 0; }
        .section h2 {
            color: #6b1e1e;
            border-bottom: 2px solid #e8d5d5;
            padding-bottom: 8px;
            font-size: 20px;
            margin-top: 0;
        }
        form { display: flex; gap: 10px; margin: 15px 0; }
        input[type="text"] {
            flex: 1;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            padding: 12px 28px;
            background: #6b1e1e;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover { background: #5a1818; }
        .query-box {
            background: #1e1e1e;
            color: #e0e0e0;
            padding: 15px;
            border-radius: 5px;
            font-family: Consolas, monospace;
            overflow-x: auto;
            margin: 15px 0;
            font-size: 14px;
        }
        .error {
            background: #ffebee;
            color: #b71c1c;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #b71c1c;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #f8f4f4; }
        .footer {
            text-align: center;
            padding: 25px;
            color: #666;
            font-size: 13px;
            margin-top: 40px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>

<div class="top-bar">
    Palestine Ahliya University | College of Engineering and Information Technology
</div>

<div class="header">
    <img src="/logo.png" alt="Palestine Ahliya University Logo" class="logo" onerror="this.style.display='none'">
    <div class="header-text">
        <h1>Palestine Ahliya University</h1>
        <p>College of Engineering and Information Technology</p>
        <p>Information Systems Security and Confidentiality — Lab 1: SQL Injection</p>
    </div>
</div>

<div class="container">

    <div class="student-box">
        Student: <?php echo htmlspecialchars($studentName); ?>
    </div>

    <div class="warning">
        <strong>Educational Lab Only:</strong> This page is intentionally vulnerable for teaching purposes. Do not deploy it outside the lab environment.
    </div>

    <div class="section">
        <h2>Lab Objective</h2>
        <p>This laboratory demonstrates a classic SQL Injection vulnerability through a simple product search page. Students are expected to:</p>
        <ul>
            <li>Scan the page using OWASP ZAP</li>
            <li>Identify the SQL Injection vulnerability</li>
            <li>Understand how the vulnerability works and how it can be exploited</li>
        </ul>
    </div>

    <div class="section">
        <h2>Product Search</h2>
        <form method="GET">
            <input type="text" name="id" placeholder="Enter Product ID" required>
            <button type="submit">Search</button>
        </form>
    </div>

    <?php if (isset($_GET['id'])): ?>
        <div class="section">
            <h2>Executed Query</h2>
            <div class="query-box">
                <?php echo htmlspecialchars($query); ?>
            </div>

            <?php if ($error): ?>
                <div class="error">
                    <strong>MySQL Error:</strong><br>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($result && $result->num_rows > 0): ?>
                <h3>Results:</h3>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                    </tr>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['price']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>No results found.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>

<div class="footer">
    Palestine Ahliya University — College of Engineering and Information Technology<br>
    Information Systems Security and Confidentiality Laboratory
</div>

</body>
</html>