<?php
    session_start();
    require_once 'database.php';
    $error = "";
    $success = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //whats needed to insert here ↓
        //personal information
        $lastName = $_POST['lastName'];
        $firstName = $_POST['firstName'];
        $middleInitial = $_POST['middleInitial'];
        $nameExtension = $_POST['nameExtension'];
        $sex = $_POST['sex'];
        $phone = $_POST['phone'];
        $birthDate = $_POST['birthDate'];

        //address information
        $street = $_POST['street'];
        $barangay = $_POST['barangay'];
        $city = $_POST['city'];
        $province = $_POST['province'];
        $zip_code = $_POST['zipCode'];

        //account information
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Check if email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $error = "Email already exists.";
        } elseif ($password !== $confirm_password) {
            $error = "Passwords do not match.";
        } else {
            $conn->beginTransaction();

            //sql starts here ↓
            try {
                //address insertion
                $stmt = $conn->prepare("
                    INSERT INTO address (
                        street, 
                        barangay, 
                        city, 
                        province, 
                        zipCode
                    ) VALUES (
                        :street, 
                        :barangay, 
                        :city, 
                        :province, 
                        :zipCode
                    )
                ");
                $stmt->execute([
                    ':street'   => $street,
                    ':barangay'=> $barangay,
                    ':city'     => $city,
                    ':province' => $province,
                    ':zipCode' => $zipCode
                ]);
                
                //reference key
                $addressId = $conn->lastInsertId();

                //password hashing
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                //user insertion
                $stmt = $conn->prepare("
                    INSERT INTO users (
                        firstName, 
                        middleInitial, 
                        lastName, 
                        nameExtension,
                        sex, 
                        phone, 
                        birthDate, 
                        email, 
                        password, 
                        role,
                        addressId
                    ) VALUES (
                        :firstName,
                        :middleInitial, 
                        :lastName, 
                        :nameExtension,
                        :sex, 
                        :phone, 
                        :birthDate, 
                        :email, 
                        :password, 
                        'user',
                        :addressId
                    )
                ");
                $stmt->execute([
                    ':firstName'     => $firstName,
                    ':middleInitial' => $middleInitial,
                    ':lastName'      => $lastName,
                    ':nameExtension' => $nameExtension,
                    ':sex'           => $sex,
                    ':phone'         => $phone,
                    ':birthDate'     => $birthDate,
                    ':email'         => $email,
                    ':password'      => $hashed_password,
                    ':addressId'     => $addressId
                ]);

                $conn->commit();
                $success = "Registration successful. You can now log in.";
            } catch (Exception $e) {
                $conn->rollBack();
                $error = "Registration failed: " . $e->getMessage();
            }
        }
    }
?>