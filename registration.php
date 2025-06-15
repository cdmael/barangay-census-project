<?php 
session_start();  // enables session management 
include 'config.php';  // connection to MySQL with $conn (census)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Escape and assign all personal details
    $name = $conn->real_escape_string($_POST['name']);
    $address = $conn->real_escape_string($_POST['address']);
    $birthDate = $conn->real_escape_string($_POST['birthDate']);
    $birthPlace = $conn->real_escape_string($_POST['birthPlace']);
    $nationality = $conn->real_escape_string($_POST['nationality']);
    $religion = $conn->real_escape_string($_POST['religion']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $height = $conn->real_escape_string($_POST['height']);
    $weight = $conn->real_escape_string($_POST['weight']);
    $contactNo = $conn->real_escape_string($_POST['contactNo']);
    $email = $conn->real_escape_string($_POST['email']);
    $maritalStatus = $conn->real_escape_string($_POST['maritalStatus']);
    $motherNameMaiden = $conn->real_escape_string($_POST['motherNameMaiden']);
    $fatherName = $conn->real_escape_string($_POST['fatherName']);

    // Inserts all personal details into the person table yay
    $sql = "INSERT INTO person 
        (name, address, birthDate, birthPlace, nationality, religion, sex, height, weight, contactNo, email, maritalStatus, motherNameMaiden, fatherName)
        VALUES 
        ('$name', '$address', '$birthDate', '$birthPlace', '$nationality', '$religion', '$gender', '$height', '$weight', '$contactNo', '$email', '$maritalStatus', '$motherNameMaiden', '$fatherName')";
    
    if ($conn->query($sql) === TRUE) {
        $personID = $conn->insert_id;

        // Insert data into education table
        if (isset($_POST['education']) && is_array($_POST['education'])) {
            foreach ($_POST['education'] as $level => $edu) {
                $schoolName = $conn->real_escape_string($edu['schoolName'] ?? '');
                $schoolAddress = $conn->real_escape_string($edu['schoolAddress'] ?? '');
                $course = $conn->real_escape_string($edu['course'] ?? '');
                $yearFinished = $conn->real_escape_string($edu['yearFinished'] ?? null);
                $acadRecognition = $conn->real_escape_string($edu['acadRecognition'] ?? '');
                $levelEscaped = $conn->real_escape_string($level);

                // Only insert if schoolName or yearFinished is not empty (to avoid blank entries)
                if (!empty($schoolName) || !empty($yearFinished)) {
                    $sqlEdu = "INSERT INTO education (personID, schoolName, schoolAddress, course, level, yearFinished, acadRecognition) 
                               VALUES ('$personID', '$schoolName', '$schoolAddress', '$course', '$levelEscaped', ". 
                               ($yearFinished ? "'$yearFinished'" : "NULL") .", '$acadRecognition')";
                    $conn->query($sqlEdu);
                }
            }
        }

        // Insert data into occupants table
        if (isset($_POST['occupants']) && is_array($_POST['occupants'])) {
            foreach ($_POST['occupants'] as $occupant) {
                if (!empty($occupant['name'])) {  // Only process if name is provided
                    $occupantName = $conn->real_escape_string($occupant['name'] ?? '');
                    $occupantBDate = $conn->real_escape_string($occupant['birthDate'] ?? null);
                    $occupantAge = intval($occupant['age'] ?? 0);
                    $occupantCvlStatus = $conn->real_escape_string($occupant['cvlStatus'] ?? '');
                    $familyPos = $conn->real_escape_string($occupant['familyPos'] ?? '');

                    $sqlOcc = "INSERT INTO occupant (personID, occupantName, occupantBDate, occupantAge, occupantCvlStatus, familyPos) 
                               VALUES ('$personID', '$occupantName', ". ($occupantBDate ? "'$occupantBDate'" : "NULL") .", '$occupantAge', '$occupantCvlStatus', '$familyPos')";
                    $conn->query($sqlOcc);
                }
            }
        }

        echo "<script>
            alert('Registration successful!');
            window.location.href = 'home.php';
        </script>";
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}
?>

  <!DOCTYPE html>
  <html lang="en">
  <head>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta charset="UTF-8">
    <title>Registration Form</title>
    <link rel="stylesheet" href="style/registration_style.css">
  </head>
  <body>

    <header>
      <div class="logo">
        <img src="style/logo.png" alt="Logo">
        <span>iTala</span>
      </div>

      <nav>
        <a href="" class="active-link">Registration</a>
        <a href="dashboard.php">View Data</a>
        <a href="Index.php">Logout</a>
      </nav>
    </header>

    <div class="container">
      <form method="POST" action="">

      <div class="section">
        <h2>Personal Details</h2>

        <!-- NAME: full-width single input -->
        <div class="form-line">
          <label for="name">Name:</label>
          <input type="text" name="name" id="name" placeholder="First Name, Last Name" required>
        </div>

        <!-- ADDRESS: full-width -->
        <div class="form-line">
          <label for="address">Present Address:</label>
          <input type="text" name="address" id="address" placeholder="House No., Purok/Street, Barangay, Municipality/City, Province" required>
        </div>

        <div class="form-group">
        <div style="flex: 1">
          <label for="birthDate">Date of Birth:</label>
          <input type="date" name="birthDate" required>
        </div>

        <div style="flex: 1">
          <label for="birthPlace">Place of Birth:</label>
          <input type="text" name="birthPlace" required>
        </div>
      </div>

    <!-- NATIONALITY / RELIGION -->
        <div class="form-group">
        <div style="flex: 1">
          <label for="nationality">Nationality:</label>
          <input type="text" name="nationality" required>
        </div>

        <div style="flex: 1">
          <label for="religion">Religion:</label>
          <input type="text" name="religion" required>
        </div>
      </div>

        <div class="form-group">
        <div style="flex: 1">
          <label for="gender">Sex:</label>
          <select name="gender" required>
            <option value="" disabled selected>Sex</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
          </select>
        </div>

        <div style="flex: 1">
          <label for="height">Height:</label>
          <input type="text" id="height" name="height" placeholder="in centimeters"required>
        </div>

          <div style="flex: 1">
            <label for="weight">Weight:</label>
            <input type="text" id="weight" name="weight" placeholder="in kilograms"required>
          </div>
        </div>

        <div class="form-group">
        <div style="flex: 1">
          <label for="email">Email:</label>
          <input type="text" name="email" required>
        </div>

        <div style="flex: 1">
          <label for="contactNo">Contact Number:</label>
          <input type="text" name="contactNo" required>
        </div>
      </div>
        
        <div class="form-line">
          <label for="maritalStatus">Marital Status:</label>
          <select name="maritalStatus" required>
            <option value="" disabled selected>Marital Status</option>
            <option value="Single">Single</option>
            <option value="Married">Married</option>
            <option value="Separated">Separated</option>
            <option value="Divorced">Divorced</option>
            <option value="Widowed">Widowed</option>
          </select>
        </div> 

          <div class="form-group">
          <div style="flex: 1">
            <label for="motherNameMaiden">Mother's Maiden Name:</label>
            <input type="text" name="motherNameMaiden" placeholder="First Name, Last Name">
        </div>

          <div style="flex: 1">
            <label for="fatherName">Father's Name:</label>
            <input type="text" name="fatherName" placeholder="First Name, Last Name">
          </div>
        </div>
  </div>

        <div class="section">
          <h2>Educational Attainment</h2>
          <h3>Leave blank if not applicable</h3>
          <?php
            $levels = ['Elementary', 'Highschool', 'College', 'Vocational'];
            foreach ($levels as $level) {
              echo "<div class='card'>";
              echo "<strong>$level</strong>";

              echo "<div class='form-group'>
                      <div style='flex: 1'>
                        <label for='schoolName_$level'>Name of School</label>
                        <input type='text' id='schoolName_$level' name='education[$level][schoolName]'>
                      </div>

                      <div style='flex: 1'>
                        <label for='yearFinished_$level'>Year Finished</label>
                        <input type='text' id='yearFinished_$level' name='education[$level][yearFinished]' placeholder='(yyyy)'>
                      </div>
                    </div>";

              echo "<div class='form-line'>
                      <label for='schoolAddress_$level'>School Address</label>
                      <input type='text' id='schoolAddress_$level' name='education[$level][schoolAddress]'>
                    </div>";

              if ($level === 'College' || $level === 'Vocational') {
                echo "<div class='form-line'>
                        <label for='course_$level'>Course</label>
                        <input type='text' id='course_$level' name='education[$level][course]'>
                      </div>";
              }

              echo "<div class='form-line'>
                      <label for='acadRecognition_$level'>Academic Recognition</label>
                      <input type='text' id='acadRecognition_$level' name='education[$level][acadRecognition]'>
                    </div>";

              echo "</div>"; // close .card
            }
          ?>
        </div>

        <div class="section" id="occupants-section">
          <h2>House Occupants</h2>
          <h3>Leave blank if not applicable</h3>

          <div class="occupant card">

            <div class="form-line">
              <label for="occupants[0][name]">Name:</label>
              <input type="text" name="occupants[0][name]" placeholder="First Name, Last Name">
            </div>

                <div class="form-group">
                  <div style="flex: 1">
                    <label for="occupants[0][birthDate]">Date of Birth:</label>
                    <input type="date" class="birthdate-input" name="occupants[0][birthDate]">
                  </div>

                  <div style="flex: 1">
                    <label for="occupants[0][age]">Age:</label>
                    <input type="text" class="age-input" name="occupants[0][age]">
                  </div>
                </div>

              <div class="form-group">
                <div style="flex: 1">
                    <label for="occupants[0][cvlStatus]">Marital Status:</label>
                    <select name="occupants[0][cvlStatus]">
                      <option value="" disabled selected>Marital Status</option>
                      <option>Single</option>
                      <option>Married</option>
                      <option>Separated</option>
                      <option>Divorced</option>
                      <option>Widowed</option>
                    </select>
                  </div>
                
                <div style="flex: 1">
                  <label for="occupants[0][familyPos]">Position in the Family:</label>
                  <input type="text" name="occupants[0][familyPos]">
                </div>
              </div>

            <button type="button" class="remove-btn" onclick="removeOccupant(this)">Remove</button>
          </div>

          <div class="button-wrapper">
          <button type="button" class="btn" onclick="addOccupant()">Add Occupant</button>
        </div>
          </div> 

                <!-- 🟢 Submit Button -->
        <div class="form-footer" style="display: flex; justify-content: flex-end;">
          <button type="submit" class="btn btn-submit">Submit</button>
        </div>

  <script>
    let occupantIndex = 1; // Start from 1 since 0 is already there

        
    function addOccupant() {
      const section = document.getElementById('occupants-section');
      const occupant = document.createElement('div');
      occupant.className = 'occupant card';

      occupant.innerHTML = `
        <div class="form-line">
          <label for="occupants[${occupantIndex}][name]">Name:</label>
          <input type="text" name="occupants[${occupantIndex}][name]" placeholder="First Name, Last Name">
        </div>

        <div class="form-group">
          <div style="flex: 1">
            <label for="occupants[${occupantIndex}][birthDate]">Date of Birth:</label>
            <input type="date" class="birthdate-input" name="occupants[${occupantIndex}][birthDate]">
          </div>

          <div style="flex: 1">
            <label for="occupants[${occupantIndex}][age]">Age:</label>
            <input type="text" class="age-input" name="occupants[${occupantIndex}][age]">
          </div>
        </div>

        <div class="form-group">
          <div style="flex: 1">
            <label for="occupants[${occupantIndex}][cvlStatus]">Marital Status:</label>
              <select name="occupants[${occupantIndex}][cvlStatus]">
                <option value="" disabled selected>Marital Status</option>
                <option>Single</option>
                <option>Married</option>
                <option>Separated</option>
                <option>Divorced</option>
                <option>Widowed</option>
                </select>
              </div>
              
              <div style="flex: 1">
                <label for="occupants[${occupantIndex}][familyPos]">Position in the Family:</label>
                <input type="text" name="occupants[${occupantIndex}][familyPos]">
              </div>
            </div>

        <button type="button" class="remove-btn" onclick="removeOccupant(this)">Remove</button>
      `;

      section.querySelector('.button-wrapper').before(occupant);
      occupantIndex++;
      attachBirthdateListeners();
    }

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
      const ageInput = event.target.closest('.occupant').querySelector('.age-input');
      if (ageInput) {
        ageInput.value = calculateAge(birthdate);
      }
    }

    // Remove occupant
    function removeOccupant(button) {
      const card = button.closest('.occupant');
      if (card) card.remove();
    }

    // Attach birthdate listeners on page load
    document.addEventListener('DOMContentLoaded', attachBirthdateListeners);
  </script>
</body>
</html>