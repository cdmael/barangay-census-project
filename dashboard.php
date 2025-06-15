<?php
session_start();  // enables session management 
include 'config.php';  // connection to MySQL with $conn (census)
 
 $searchPersonID = isset($_POST['searchPersonID']) && $_POST['searchPersonID'] !== '' ? trim($_POST['searchPersonID']) : null;

// get query from person table
$sql = "SELECT * FROM person WHERE 1=1"; // base query

  

 if ($searchPersonID) {
    $sql .= " AND personID = ?";
     }



$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Prepare failed: " . htmlspecialchars($conn->error));
}

if ($searchPersonID) {
  $stmt->bind_param("i", $searchPersonID);
}




    // execute part
    $stmt->execute();
    $result = $stmt->get_result();


//$result = $conn->query($sql);


function calculateAge($birthdate) {
    $birthDate = new DateTime($birthdate);
    $today = new DateTime('today');
    $age = $birthDate->diff($today)->y;
    return $age;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>iTala - Dashboard</title>
  <link rel="stylesheet" href="style/dashboard_style.css">
</head>
<body>

  <header>
    <div class="logo">
      <img src="style/logo.png" alt="Logo">
      <span>iTala</span>
    </div>

    <nav>
      <a href="Registration.php">Registration</a>
      <a href="" class="active-link">View Data</a>
      <a href="Index.php">Logout</a>
    </nav>
  </header>
  
  <section class="controls">
    <div class="button-group">
      <a href="Dashboard.php" class="button-link active">Person Data</a>
      <a href="viewOccupant.php" class="button-link">Occupant Data</a>
    </div>

    <form method="POST" action="dashboard.php">
      <div class="search-group">
        <input type="text" name="searchPersonID" placeholder="Search Person ID" value="<?= htmlspecialchars($searchPersonID) ?>">
        <button type="submit">Search</button>
      </div>
    </form>
  </section>

  <table>
    <thead>
      <tr>

        <th>Person ID</th>
        <th>Name</th>
        <th>Address</th>
        <th>Birthdate</th>
        <th>Age</th>
        <th>Birth Place</th>
        <th>Nationality</th>
        <th>Religion</th>
        <th>Sex</th>
        <th>Height</th>
        <th>Weight</th>
        <th>Contact No.</th>
        <th>Email</th>
        <th>Marital Status</th>
        <th>Mother Name</th>
        <th>Father Name</th>
      </tr>
      
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
              <td><a href="viewPerson.php?personID=<?= htmlspecialchars($row['personID']) ?>"><?= htmlspecialchars($row['personID']) ?></a></td>
              <td class="scrollable-cell"><?= htmlspecialchars($row['name']) ?></td>
              <td class="scrollable-cell"><?= htmlspecialchars($row['address']) ?></td>
              <td><?= htmlspecialchars($row['birthDate']) ?></td>
              <td><?= calculateAge($row['birthDate']) ?></td>
              <td><?= htmlspecialchars($row['birthPlace']) ?></td>
              <td><?= htmlspecialchars($row['nationality']) ?></td>
              <td><?= htmlspecialchars($row['religion']) ?></td>
              <td><?= htmlspecialchars($row['sex']) ?></td>
              <td><?= htmlspecialchars($row['height']) ?></td>
              <td><?= htmlspecialchars($row['weight']) ?></td>
              <td><?= htmlspecialchars($row['contactNo']) ?></td>
              <td class="scrollable-cell"><?= htmlspecialchars($row['email']) ?></td>
              <td><?= htmlspecialchars($row['maritalStatus']) ?></td>
              <td class="scrollable-cell"><?= htmlspecialchars($row['motherNameMaiden']) ?></td>
              <td class="scrollable-cell"><?= htmlspecialchars($row['fatherName']) ?></td>
          </tr>
        <?php endwhile; ?>

    </tbody>
  </table>

</body>
</html>
