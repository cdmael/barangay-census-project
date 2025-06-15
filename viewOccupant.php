<?php 
    session_start();
    include 'config.php';

// Initialize search variables
  $searchPersonID = isset($_POST['searchPersonID']) && $_POST['searchPersonID'] !== '' ? trim($_POST['searchPersonID']) : null;
  $searchOccupantID = isset($_POST['searchOccupantID']) && $_POST['searchOccupantID'] !== '' ? trim($_POST['searchOccupantID']) : null;



    // Query occupants with associated person name for context
  $sql = "SELECT o.*, p.name AS personName 
            FROM occupant o 
            LEFT JOIN person p ON o.personID = p.personID
            WHERE 1=1";

     if ($searchPersonID) {
    $sql .= " AND o.personID = ?";
     }
    if ($searchOccupantID) {
    $sql .= " AND o.occupantID = ?";
    }       

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    if ($searchPersonID && $searchOccupantID) {
    $stmt->bind_param("ii", $searchPersonID, $searchOccupantID);
    } elseif ($searchPersonID) {
        $stmt->bind_param("i", $searchPersonID);
    } elseif ($searchOccupantID) {
        $stmt->bind_param("i", $searchOccupantID);
    }


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
<body class="occupant-page">

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
      <a href="Dashboard.php" class="button-link">Person Data</a>
      <a href="viewOccupant.php" class="button-link active">Occupant Data</a>
    </div>

     <form method="POST" action="viewOccupant.php">
      <div class="search-group">
        <input type="text" name="searchPersonID" placeholder="Search Person ID" value="<?= htmlspecialchars($searchPersonID) ?>">
        <input type="text" name="searchOccupantID" placeholder="Search Occupant ID" value="<?= htmlspecialchars($searchOccupantID) ?>">
        <button type="submit">Search</button>
      </div>
    </form>
  </section>

  <table id="occupantTable">
    <thead>
      <tr>
        <th>Person ID</th>
        <th>Occupant ID</th>
        <th>Name</th>
        <th>Birthdate</th>
        <th>Age</th>
        <th>Marital Status</th>
        <th>Position in the Family</th>
      </tr>
    </thead>
    <tbody>
      <!-- SAMPLE ROWS ONLY: remove/replace these when integrating with backend -->
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><a href="viewPerson.php?personID=<?= htmlspecialchars($row['personID']) ?>"><?= htmlspecialchars($row['personID']) ?></a></td>
            <td><a href="viewPerson.php?personID=<?= htmlspecialchars($row['personID']) ?>"><?= htmlspecialchars($row['occupantID']) ?></a></td>
            <td><?= htmlspecialchars($row['occupantName']) ?></td>
            <td><?= htmlspecialchars($row['occupantBDate']) ?></td>
            <td><?= htmlspecialchars(calculateAge($row['occupantBDate'])) ?></td>
            <td><?= htmlspecialchars($row['occupantCvlStatus']) ?></td>
            <td><?= htmlspecialchars($row['familyPos']) ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="7">No occupant records found.</td>
        </tr>
      <?php endif; ?>
      <!-- will more simulated rows here if needed -->
    </tbody>
  </table>

</body>
</html>
