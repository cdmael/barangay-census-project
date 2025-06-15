editPerson.php

<?php 
session_start();
include 'config.php';

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

// Fetch education details
$sqlEducation = "SELECT * FROM education WHERE personID = ? ORDER BY level, yearFinished DESC";
$stmtEducation = $conn->prepare($sqlEducation);
$stmtEducation->bind_param("i", $personID);
$stmtEducation->execute();
$resultEducation = $stmtEducation->get_result();

// Group education records by level
$educationByLevel = [];
while ($row = $resultEducation->fetch_assoc()) {
    $level = strtoupper($row['level']);
    if (!isset($educationByLevel[$level])) {
        $educationByLevel[$level] = $row;  // take first (latest) record per level
    }
}

// Fetch Occupant data
$sqlOccupant = "SELECT * FROM occupant WHERE personID = ?";
$stmtOccupant = $conn->prepare($sqlOccupant);
$stmtOccupant->bind_param("i", $personID);
$stmtOccupant->execute();
$resultOccupant = $stmtOccupant->get_result();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update person details
    $name = $_POST['name'];
    $address = $_POST['address'];
    $birthdate = $_POST['birthdate'];
    $birthPlace = $_POST['birthPlace'];
    $nationality = $_POST['nationality'];
    $religion = $_POST['religion'];
    $sex = $_POST['sex'];
    $height = $_POST['height'];
    $weight = $_POST['weight'];
    $contactNo = $_POST['contactNo'];
    $email = $_POST['email'];
    $maritalStatus = $_POST['maritalStatus'];
    $motherNameMaiden = $_POST['motherNameMaiden'];
    $fatherName = $_POST['fatherName'];

    // Prepare update statement for person
    $sqlUpdate = "UPDATE person SET name=?, address=?, birthDate=?, birthPlace=?, nationality=?, religion=?, sex=?, height=?, weight=?, contactNo=?, email=?, maritalStatus=?, motherNameMaiden=?, fatherName=? WHERE personID=?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("ssssssssssssssi", $name, $address, $birthdate, $birthPlace, $nationality, $religion, $sex, $height, $weight, $contactNo, $email, $maritalStatus, $motherNameMaiden, $fatherName, $personID);
    
    if ($stmtUpdate->execute()) {
        echo "Person record updated successfully.";
    } else {
        echo "Error updating person record: " . $stmtUpdate->error;
    }

    // Update education details
    if (isset($_POST['education'])) {
        foreach ($_POST['education'] as $education) {
            $schoolName = $education['schoolName'];
            $schoolAddress = $education['schoolAddress'];
            $course = $education['course'];
            $level = $education['level'];
            
            // Validate yearFinished
            $yearFinished = !empty($education['yearFinished']) && is_numeric($education['yearFinished']) && $education['yearFinished'] >= 1900 && $education['yearFinished'] <= date('Y') 
                ? $education['yearFinished'] 
                : null;
            
            $acadRecognition = $education['acadRecognition'];
            $educationID = $education['educationID'] ?? null;
            
            if (!empty($educationID)) {
                $sqlUpdateEducation = "UPDATE education SET schoolName=?, schoolAddress=?, course=?, level=?, yearFinished=?, acadRecognition=? WHERE educationID=?";
                $stmtUpdateEducation = $conn->prepare($sqlUpdateEducation);
                $stmtUpdateEducation->bind_param("ssssssi", $schoolName, $schoolAddress, $course, $level, $yearFinished, $acadRecognition, $educationID);
                $stmtUpdateEducation->execute();
            } else {
                $sqlInsertEducation = "INSERT INTO education (personID, schoolName, schoolAddress, course, level, yearFinished, acadRecognition) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmtInsertEducation = $conn->prepare($sqlInsertEducation);
                $stmtInsertEducation->bind_param("issssss", $personID, $schoolName, $schoolAddress, $course, $level, $yearFinished, $acadRecognition);
                $stmtInsertEducation->execute();
            }
        }
    }

    // Handle occupant deletions
    if (isset($_POST['deletedOccupants'])) {
        $deletedOccupants = array_filter(explode(',', $_POST['deletedOccupants']), 'is_numeric');
        if (!empty($deletedOccupants)) {
            $placeholders = implode(',', array_fill(0, count($deletedOccupants), '?'));
            $sqlDeleteOccupant = "DELETE FROM occupant WHERE personID = ? AND occupantID IN ($placeholders)";
            $stmtDeleteOccupant = $conn->prepare($sqlDeleteOccupant);
            $params = array_merge([$personID], $deletedOccupants);
            $types = str_repeat('i', count($params));
            $stmtDeleteOccupant->bind_param($types, ...$params);
            $stmtDeleteOccupant->execute();
            if ($stmtDeleteOccupant->affected_rows > 0) {
                echo "Occupant(s) deleted successfully.";
            } else {
                echo "No occupants deleted or error occurred: " . $stmtDeleteOccupant->error;
            }
        }
    }

    // Update or insert occupant details
    if (isset($_POST['occupants'])) {
        foreach ($_POST['occupants'] as $occupantID => $occupant) {
            // Only process if at least the name is provided
            if (!empty($occupant['occupantName'])) {
                $occupantName = $conn->real_escape_string($occupant['occupantName'] ?? '');
                $occupantPos = $conn->real_escape_string($occupant['familyPos'] ?? '');
                $occupantBdate = $conn->real_escape_string($occupant['occupantBDate'] ?? null);
                $occupantAge = intval($occupant['occupantAge'] ?? 0);
                $occupantCvlStatus = $conn->real_escape_string($occupant['occupantCvlStatus'] ?? '');

                if (is_numeric($occupantID)) {
                    // Update existing occupant
                    $sqlUpdateOccupant = "UPDATE occupant SET occupantName=?, familyPos=?, occupantBDate=?, occupantAge=?, occupantCvlStatus=? WHERE occupantID=?";
                    $stmtUpdateOccupant = $conn->prepare($sqlUpdateOccupant);
                    $stmtUpdateOccupant->bind_param("sssssi", $occupantName, $occupantPos, $occupantBdate, $occupantAge, $occupantCvlStatus, $occupantID);
                    $stmtUpdateOccupant->execute();
                } else {
                    // Insert new occupant
                    $sqlInsertOccupant = "INSERT INTO occupant (personID, occupantName, familyPos, occupantBDate, occupantAge, occupantCvlStatus) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmtInsertOccupant = $conn->prepare($sqlInsertOccupant);
                    $stmtInsertOccupant->bind_param("issssi", $personID, $occupantName, $occupantPos, $occupantBdate, $occupantAge, $occupantCvlStatus);
                    $stmtInsertOccupant->execute();
                }
            }
        }
    } // Closing brace added here

    echo "<script>
            alert('Update successful!');
            window.location.href = 'viewPerson.php?personID=$personID';
          </script>";
    exit;
} // Closing brace for the POST request block
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>iTala - Edit Person Data</title>
  <link rel="stylesheet" href="style/edit_style.css">
</head>
<body>

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

</head>
<body class="edit-person-page">
  <div class="page-container">

  <!-- Controls -->
  <section class="controls">
    <div class="controls-bar">
      <p class="breadcrumb">
        <a href="Dashboard.php">View Data</a> /
        <a href="viewPerson.php?personID=<?= urlencode($person['personID']) ?>">
          <?= htmlspecialchars($person['name']) ?>
        </a> /
        <a href="#">Edit</a>
      </p>
      <div class="buttons">
        <button type="submit" form="edit-form" class="button-link">Save</button>
      </div>
    </div>
  </section>


    <!-- Form -->
    <div class="form-wrapper">
      <form id="edit-form" class="two-column-form" method="POST">
        <input type="hidden" name="deletedOccupants" id="deletedOccupants" value="">
        <h2 class="column-title">Personal Details</h2><br>
        <div class="form-columns">
          <!-- Left Column -->
          <div class="left-column">
            <div class="form-group"><label>Name</label><input name="name" type="text" value="<?= htmlspecialchars($person['name']) ?>"></div>
            <div class="form-group"><label>Address</label><input name="address" type="text" value="<?= htmlspecialchars($person['address']) ?>"></div>
            <div class="form-group"><label>Birthdate</label><input name="birthdate" type="date" value="<?= htmlspecialchars($person['birthDate']) ?>"></div>
            <div class="form-group"><label>Birth Place</label><input name="birthPlace" type="text" value="<?= htmlspecialchars($person['birthPlace']) ?>"></div>
            <div class="form-group"><label>Nationality</label><input name="nationality" type="text" value="<?= htmlspecialchars($person['nationality']) ?>"></div>
            <div class="form-group"><label>Religion</label><input name="religion" type="text" value="<?= htmlspecialchars($person['religion']) ?>"></div>
            <div class="form-group"><label>Sex</label><input name="sex" type="text" value="<?= htmlspecialchars($person['sex']) ?>"></div>
          </div>
          <!-- Right Column -->
          <div class="right-column">
            <div class="form-group"><label>Height</label><input name="height" type="text" value="<?= htmlspecialchars($person['height']) ?>"></div>
            <div class="form-group"><label>Weight</label><input name="weight" type="text" value="<?= htmlspecialchars($person['weight']) ?>"></div>
            <div class="form-group"><label>Contact No.</label><input name="contactNo" type="text" value="<?= htmlspecialchars($person['contactNo']) ?>"></div>
            <div class="form-group"><label>Email</label><input name="email" type="email" value="<?= htmlspecialchars($person['email']) ?>"></div>
            <div class="form-group">
                  <label>Marital Status</label>
                  <select name="maritalStatus" required>
                      <option value="" disabled <?= empty($person['maritalStatus']) ? 'selected' : '' ?>>Select Marital Status</option>
                      <option value="Single" <?= $person['maritalStatus'] === 'Single' ? 'selected' : '' ?>>Single</option>
                      <option value="Married" <?= $person['maritalStatus'] === 'Married' ? 'selected' : '' ?>>Married</option>
                      <option value="Separated" <?= $person['maritalStatus'] === 'Separated' ? 'selected' : '' ?>>Separated</option>
                      <option value="Divorced" <?= $person['maritalStatus'] === 'Divorced' ? 'selected' : '' ?>>Divorced</option>
                      <option value="Widowed" <?= $person['maritalStatus'] === 'Widowed' ? 'selected' : '' ?>>Widowed</option>
                  </select>
              </div>
            <div class="form-group"><label>Mother's Maiden Name</label><input name="motherNameMaiden" type="text" value="<?= htmlspecialchars($person['motherNameMaiden']) ?>"></div>
            <div class="form-group"><label>Father's Name</label><input name="fatherName" type="text" value="<?= htmlspecialchars($person['fatherName']) ?>"></div>
          </div>
        </div>

        <br><br>

        <!-- Educational Attainment -->
        <h2 class="column-title">Educational Attainment</h2><br>
        <div class="form-columns" style="display:grid; grid-template-columns:repeat(2, 1fr); gap:2rem; max-width: 1200px; margin:0 auto;">
          <?php 
          $levels = ['ELEMENTARY', 'HIGHSCHOOL', 'COLLEGE', 'VOCATIONAL'];
          foreach ($levels as $levelName): 
            $ed = $educationByLevel[$levelName] ?? null;
          ?>
            <div style="background:#fff; border-radius:0.75rem; box-shadow:0 1px 4px rgba(0,0,0,0.05); padding:1rem;">
              <h3 style="font-weight:600; font-size:1.25rem; margin-bottom:1rem; color:#111827;"><?= ucfirst(strtolower($levelName)) ?></h3>
              <input type="hidden" name="education[<?= $ed['educationID'] ?? $levelName ?>][level]" value="<?= htmlspecialchars($levelName) ?>">
              <?php if (isset($ed['educationID'])): ?>
                <input type="hidden" name="education[<?= $ed['educationID'] ?>][educationID]" value="<?= $ed['educationID'] ?>">
              <?php endif; ?>

              <div class="form-group" style="margin-bottom:1rem;">
                <label>School Name</label>
                <input type="text" name="education[<?= $ed['educationID'] ?? $levelName ?>][schoolName]" value="<?= htmlspecialchars($ed['schoolName'] ?? '') ?>">
              </div>

              <div class="form-group" style="margin-bottom:1rem;">
                <label>School Address</label>
                <input type="text" name="education[<?= $ed['educationID'] ?? $levelName ?>][schoolAddress]" value="<?= htmlspecialchars($ed['schoolAddress'] ?? '') ?>">
              </div>
              
              <div class="form-group" style="margin-bottom:1rem;">
                <label>Course</label>
                <input type="text" name="education[<?= $ed['educationID'] ?? $levelName ?>][course]" value="<?= htmlspecialchars($ed['course'] ?? '') ?>">
              </div>
              
              <div class="form-group" style="margin-bottom:1rem;">
                <label>Year Finished</label>
                <input type="number" min="1900" max="<?= date('Y') ?>" name="education[<?= $ed['educationID'] ?? $levelName ?>][yearFinished]" value="<?= htmlspecialchars(($ed['yearFinished'] ?? '') !== '0000' ? ($ed['yearFinished'] ?? '') : '') ?>">
              </div>
              
              <div class="form-group" style="margin-bottom:1rem;">
                <label>Academic Recognition</label>
                <input type="text" name="education[<?= $ed['educationID'] ?? $levelName ?>][acadRecognition]" value="<?= htmlspecialchars($ed['acadRecognition'] ?? '') ?>">
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <br><br>

        <!-- Other House Occupants -->
        
      <h2 class="column-title">Other House Occupants</h2><br>
      <div id="occupants-wrapper">
        <?php 
      if ($resultOccupant->num_rows > 0) {
            while ($occupant = $resultOccupant->fetch_assoc()): ?>
            <div class="occupant-section" data-occupant-id="<?= htmlspecialchars($occupant['occupantID']) ?>">
              <div class="occupant-row">
                <label>Name:</label>
                <input type="text" name="occupants[<?= $occupant['occupantID'] ?>][occupantName]" value="<?= htmlspecialchars($occupant['occupantName']) ?>">
              </div>
              <div class="occupant-row">
                <label>Position in the family:</label>
                <input type="text" name="occupants[<?= $occupant['occupantID'] ?>][familyPos]" value="<?= htmlspecialchars($occupant['familyPos'] ?? '') ?>">
              </div>
              <div class="occupant-row">
                <label>Birthdate:</label>
                <input type="date" class="birthdate-input" name="occupants[<?= $occupant['occupantID'] ?>][occupantBDate]" value="<?= htmlspecialchars($occupant['occupantBDate'] ?? '') ?>">
              </div>
              <div class="occupant-row">
                <label>Age:</label>
                <input type="number" class="age-input" name="occupants[<?= $occupant['occupantID'] ?>][occupantAge]" <?= ($occupant['occupantAge'] ?? '') !== '' ? 'value="' . htmlspecialchars($occupant['occupantAge']) . '"' : ''; ?>>
              </div>
              <div class="occupant-row">
                <label>Marital Status:</label>
                <select name="occupants[<?= $occupant['occupantID'] ?>][occupantCvlStatus]">
                  <option value="" disabled <?= empty($occupant['occupantCvlStatus']) ? 'selected' : '' ?>>Marital Status</option>
                  <option <?= ($occupant['occupantCvlStatus'] ?? '') === 'Single' ? 'selected' : '' ?>>Single</option>
                  <option <?= ($occupant['occupantCvlStatus'] ?? '') === 'Married' ? 'selected' : '' ?>>Married</option>
                  <option <?= ($occupant['occupantCvlStatus'] ?? '') === 'Separated' ? 'selected' : '' ?>>Separated</option>
                  <option <?= ($occupant['occupantCvlStatus'] ?? '') === 'Divorced' ? 'selected' : '' ?>>Divorced</option>
                  <option <?= ($occupant['occupantCvlStatus'] ?? '') === 'Widowed' ? 'selected' : '' ?>>Widowed</option>
                </select>
              </div>
              <button type="button" class="remove-occupant-btn">− Remove</button>
              <hr>
            </div>
            <?php endwhile;
        } else {
            // Empty template for adding new occupants
            echo '<div class="occupant-section" data-occupant-id="">
                    <div class="occupant-row">
                      <label>Name:</label>
                      <input type="text" name="occupants[new][occupantName]" value="">
                    </div>
                    <div class="occupant-row">
                      <label>Position in family:</label>
                      <input type="text" name="occupants[new][familyPos]" value="">
                    </div>
                    <div class="occupant-row">
                      <label>Birthdate:</label>
                      <input type="date" class="birthdate-input" name="occupants[new][occupantBDate]" value="">
                    </div>
                    <div class="occupant-row">
                      <label>Age:</label>
                      <input type="number" class="age-input" name="occupants[new][occupantAge]" value="">
                    </div>
                    <div class="occupant-row">
                      <label>Marital Status:</label>
                      <select name="occupants[new][occupantCvlStatus]">
                        <option value="" disabled selected>Marital Status</option>
                        <option>Single</option>
                        <option>Married</option>
                        <option>Separated</option>
                        <option>Divorced</option>
                        <option>Widowed</option>
                      </select>
                    </div>
                    <button type="button" class="remove-occupant-btn">− Remove</button>
                    <hr>
                  </div>';
        }
        ?>
      </div>

<button type="button" id="add-occupant-btn">+ Add More Occupant/s</button>

      </form>
    </div>

    <!-- JavaScript -->
    <script>
          // Initialize array to track deleted occupant IDs
          let deletedOccupants = [];

          // Function to calculate age from birthdate
          function calculateAge(birthdate) {
            if (!birthdate) return '';
            const birthDate = new Date(birthdate);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
              age--;
            }
            return age >= 0 ? age : '';
          }

          // Function to attach birthdate change listeners to all birthdate inputs
          function attachBirthdateListeners() {
            document.querySelectorAll('.birthdate-input').forEach(input => {
              input.removeEventListener('change', handleBirthdateChange); // Prevent duplicate listeners
              input.addEventListener('change', handleBirthdateChange);
            });
          }

          // Handle birthdate change
          function handleBirthdateChange(event) {
            const birthdate = event.target.value;
            const ageInput = event.target.closest('.occupant-section').querySelector('.age-input');
            if (ageInput) {
              ageInput.value = calculateAge(birthdate);
            }
          }

          // Add new occupant
          document.getElementById("add-occupant-btn").addEventListener("click", function() {
            const wrapper = document.getElementById("occupants-wrapper");
            const sections = wrapper.querySelectorAll(".occupant-section");
            const lastSection = sections[sections.length - 1];
            const newSection = lastSection.cloneNode(true);
            
            // Generate a unique identifier for the new occupant
            const newId = 'new_' + Date.now();
            
            // Update all input names and clear values
            newSection.querySelectorAll("input").forEach(input => {
              input.value = "";
              input.name = input.name.replace(/\[(new|new_\d+|[\d]+)\]/, `[${newId}]`);
            });
            
            // Update all select names and reset their values
            newSection.querySelectorAll("select").forEach(select => {
              select.selectedIndex = 0;
              select.name = select.name.replace(/\[(new|new_\d+|[\d]+)\]/, `[${newId}]`);
            });
            
            // Clear occupant ID
            newSection.setAttribute('data-occupant-id', '');
            
            wrapper.appendChild(newSection);
            
            // Re-attach birthdate listeners to all inputs, including the new one
            attachBirthdateListeners();
          });

          // Remove occupant
          document.addEventListener("click", function(e) {
            if (e.target && e.target.classList.contains("remove-occupant-btn")) {
              const section = e.target.closest(".occupant-section");
              const occupantId = section.getAttribute('data-occupant-id');
              if (occupantId && occupantId !== '') {
                deletedOccupants.push(occupantId);
                document.getElementById('deletedOccupants').value = deletedOccupants.join(',');
              }
              section.remove();
            }
          });

          // Validate yearFinished inputs
          document.getElementById("edit-form").addEventListener("submit", function(e) {
            const yearInputs = document.querySelectorAll('input[name$="[yearFinished]"]');
            let isValid = true;
            yearInputs.forEach(input => {
              const value = input.value.trim();
              if (value && (isNaN(value) || value < 1900 || value > <?= date('Y') ?>)) {
                isValid = false;
                alert("Please enter a valid year between 1900 and <?= date('Y') ?> for " + input.closest('div').querySelector('h3').textContent);
                input.focus();
              }
            });
            if (!isValid) {
              e.preventDefault();
            }
          });

          // Attach birthdate listeners on page load
          document.addEventListener('DOMContentLoaded', attachBirthdateListeners);
</script>
  </div>
</body>
</html
