<?php
session_start();
include 'config.php';


// Safely get personID from URL
$personID = isset($_GET['personID']) ? intval($_GET['personID']) : 0;

if ($personID <= 0) {
    echo "Invalid or missing person ID.";
    exit;
}

// Fetch person details
$sqlPerson = "SELECT * FROM person WHERE personID = ?";
$stmtPerson = $conn->prepare($sqlPerson);
$stmtPerson->bind_param("i", $personID);
$stmtPerson->execute();
$resultPerson = $stmtPerson->get_result();

if ($resultPerson->num_rows === 0) {
    echo "Person with ID $personID not found.";
    exit;
}

$person = $resultPerson->fetch_assoc();

// Function to calculate age from birthdate
function calculateAge($birthdate) {
    $birthDate = new DateTime($birthdate);
    $today = new DateTime('today');
    $age = $birthDate->diff($today)->y;
    return $age;
}

// Fetch education details
$sqlEducation = "SELECT * FROM education WHERE personID = ?";
$stmtEducation = $conn->prepare($sqlEducation);
$stmtEducation->bind_param("i", $personID);
$stmtEducation->execute();
$resultEducation = $stmtEducation->get_result();

// Fetch occupant details
$sqlOccupant = "SELECT * FROM occupant WHERE personID = ?";
$stmtOccupant = $conn->prepare($sqlOccupant);
$stmtOccupant->bind_param("i", $personID);
$stmtOccupant->execute();
$resultOccupant = $stmtOccupant->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>iTala - Person Data</title>
  <link rel="stylesheet" href="style/viewPerson_style.css">
</head>
<body class="view-person-page">

  <header>
    <div class="logo">
      <img src="style/logo.png" alt="Logo">
      <span>iTala</span>
    </div>

    <nav>
      <a href="Registration.php">Registration</a>
      <a href="Dashboard.php">View Data</a>
      <a href="Index.php">Logout</a>
    </nav>
  </header>

  <!-- Control Buttons -->
  <section class="controls">
    <div class="controls-bar">
      <p class="breadcrumb">
        <a href="Dashboard.php">View Data</a> /
        <a href="#"><?= htmlspecialchars($person['name']) ?></a>
      </p>
      <div class="buttons">
        <a href="editPerson.php?personID=<?= htmlspecialchars($person['personID']) ?>" class="button-link">Edit</a>
        <a href="deletePerson.php?personID=<?= $person['personID'] ?>" class="button-link-delete"
          onclick="return confirm('Are you sure you want to delete this person?');">
          Delete
        </a>
      </div>
    </div>
  </section>

  <div class="person-data-section">
    <!-- Left Column -->
    <div class="left-column">
      <h2 class="full-name"><?= htmlspecialchars($person['name']) ?></h2>

      <p class="section-title">Personal Details:</p>
      <p><strong>Address:</strong> <?= htmlspecialchars($person['address']) ?></p>
      <p><strong>Birthdate:</strong> <?= htmlspecialchars($person['birthDate']) ?></p>
      <p><strong>Age:</strong> <?= calculateAge($person['birthDate']) ?></p>

      <p><strong>Birth Place:</strong> <?= htmlspecialchars($person['birthPlace']) ?></p>
      <p><strong>Nationality:</strong> <?= htmlspecialchars($person['nationality']) ?></p>
      <p><strong>Religion:</strong> <?= htmlspecialchars($person['religion']) ?></p>
      <p><strong>Sex:</strong> <?= htmlspecialchars($person['sex']) ?></p>
      <p><strong>Height:</strong> <?= htmlspecialchars($person['height']) ?></p>
      <p><strong>Weight:</strong> <?= htmlspecialchars($person['weight']) ?></p>
      <p><strong>Contact No.:</strong> <?= htmlspecialchars($person['contactNo']) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($person['email']) ?></p>
      <p><strong>Marital Status:</strong> <?= htmlspecialchars($person['maritalStatus']) ?></p>
      <p><strong>Mother Maiden Name:</strong> <?= htmlspecialchars($person['motherNameMaiden']) ?></p>
      <p><strong>Father Name:</strong> <?= htmlspecialchars($person['fatherName']) ?></p>

      <br>
      <p class="section-title">Educational Attainment:</p>
      <?php if ($resultEducation->num_rows > 0): ?>
        <?php while ($row = $resultEducation->fetch_assoc()): ?>
          <p><strong>Level:</strong> <?= htmlspecialchars($row['level']) ?></p>
          <p><strong>School Name:</strong> <?= htmlspecialchars($row['schoolName']) ?></p>
          <p><strong>School Address:</strong> <?= htmlspecialchars($row['schoolAddress']) ?></p>
          <p><strong>Course:</strong> <?= htmlspecialchars($row['course']) ?></p>
          <p><strong>Year Finished:</strong> <?= htmlspecialchars($row['yearFinished']) ?></p>
          <p><strong>Academic Recognition:</strong> <?= htmlspecialchars($row['acadRecognition']) ?></p>
          <br>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No education records found for this person.</p>
      <?php endif; ?>
    </div>

    <!-- Right Column -->
    <div class="right-column">
      <br>
      <p class="section-title">Occupants:</p>
      <?php if ($resultOccupant->num_rows > 0): ?>
        <?php while ($row = $resultOccupant->fetch_assoc()): ?>
          <div class="occupant-entry">
            <p><strong>Name:</strong> <?= htmlspecialchars($row['occupantName']) ?></p>
            <p><strong>Birthdate:</strong> <?= htmlspecialchars($row['occupantBDate']) ?></p>
            <p><strong>Age:</strong> <?= htmlspecialchars($row['occupantAge']) ?></p>
            <p><strong>Civil Status:</strong> <?= htmlspecialchars($row['occupantCvlStatus']) ?></p>
            <p><strong>Family Position:</strong> <?= htmlspecialchars($row['familyPos']) ?></p>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No occupant records found for this person.</p>
      <?php endif; ?>
    </div>
  </div>

</body>
</html>
