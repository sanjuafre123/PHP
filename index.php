<?php

include "config.php";
$c1 = new Config();
$c1->connect();

session_start();

$btn_set = isset($_POST["button"]);
$error_message = "";

if ($btn_set) {
    $name = trim($_POST["name"]);
    $age = intval($_POST["age"]);
    $contact = trim($_POST["contact"]);
    $course = trim($_POST["course"]);

    if (empty($name) || empty($age) || empty($contact) || empty($course)) {
        $error_message = "All fields are required.";
    } elseif (str_word_count($name) < 2) {
        $error_message = "Name must be at least 2 words.";
    } elseif ($age <= 0 || $age > 100) {
        $error_message = "Age must be between 1 and 100.";
    } elseif (!preg_match("/^\d{10}$/", $contact)) {
        $error_message = "Phone number must be exactly 10 digits.";
    } else {
        $c1->insertData($name, $age, $contact, $course);
        header('Location: index.php'); // Redirect to refresh the page and clear the form.
        exit();
    }
}

if (isset($_POST['delete'])) {
    $id = $_POST['updateId'];
    $c1->deleteData($id);
    header('Location: index.php');
    exit();
}

if (isset($_POST['update'])) {
    $updateId = $_REQUEST['updateId'];
    $name = $_REQUEST['updateName'];
    $age = $_REQUEST['updateAge'];
    $contact = $_REQUEST['updateContact'];
    $course = $_REQUEST['updateCourse'];

    $_SESSION['id'] = $updateId;
    $_SESSION['name'] = $name;
    $_SESSION['age'] = $age;
    $_SESSION['contact'] = $contact;
    $_SESSION['course'] = $course;

    header('Location: update.php');
    exit();
}

// Fetching all data
$res = $c1->readData();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Student Registration</title>
    <style>
        body {
            background: linear-gradient(to bottom right, #4facfe, #00f2fe);
            font-family: 'Arial', sans-serif;
        }

        .form-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 40px;
            height: 80%;
            width: 38%;
            animation: fadeIn 1s ease-in-out;
        }

        .form-container h1 {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .input-group input {
            border-radius: 5px;
            border: 1px solid #ccc;
            padding: 10px;
            width: 100%;
            margin-bottom: 15px;
        }

        .form-container button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            width: 100%;
            transition: background-color 0.3s;
        }

        .form-container button:hover {
            background: #0056b3;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
            text-align: center;
        }

        .students-container {
            margin-top: 40px;
            background: white;
            margin-left: 210px;
            padding: 20px;
            height: 80%;
            width: 70%;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1.5s ease-in-out;
        }

        .students-container h2 {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .table {
            text-align: center;
            border-collapse: collapse;
        }

        .table thead th {
            background-color: #007bff;
            color: white;
        }

        .table tbody tr {
            transition: transform 0.3s;
        }

        .table tbody tr:hover {
            transform: scale(1.02);
        }

        .table tbody td button {
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 12px;
        }

        .table tbody td .btn-update {
            background: #28a745;
        }

        .table tbody td .btn-update:hover {
            background: #218838;
        }

        .table tbody td .btn-delete {
            background: #dc3545;
        }

        .table tbody td .btn-delete:hover {
            background: #c82333;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Top Section: Registration Form -->
        <div class="form-container mx-auto col-md-6">
            <h1>Student Registration</h1>
            <?php if (!empty($error_message)) { ?>
                <div class="error-message"> <?php echo $error_message; ?> </div>
            <?php } ?>
            <form method="POST">
                <div class="input-group">
                    <input type="text" name="name" placeholder="Full Name" required>
                </div>
                <div class="input-group">
                    <input type="number" name="age" placeholder="Age" required>
                </div>
                <div class="input-group">
                    <input type="number" name="contact" placeholder="Phone Number" required>
                </div>
                <div class="input-group">
                    <input type="text" name="course" placeholder="Course" required>
                </div>
                <button type="submit" name="button">Submit</button>
            </form>
        </div>

        <!-- Bottom Section: Registered Students -->
        <div class="students-container">
            <h2>Registered Students</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Phone</th>
                            <th>Course</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($data = mysqli_fetch_assoc($res)) { ?>
                            <tr>
                                <td><?php echo $data["name"] ?></td>
                                <td><?php echo $data["age"] ?></td>
                                <td><?php echo $data["contact"] ?></td>
                                <td><?php echo $data["course"] ?></td>
                                <td>
                                    <form method="POST" class="d-flex justify-content-center gap-2">
                                        <input name="updateId" type="hidden" value="<?php echo $data["id"] ?>">
                                        <input name="updateName" type="hidden" value="<?php echo $data["name"] ?>">
                                        <input name="updateAge" type="hidden" value="<?php echo $data["age"] ?>">
                                        <input name="updateContact" type="hidden" value="<?php echo $data["contact"] ?>">
                                        <input name="updateCourse" type="hidden" value="<?php echo $data["course"] ?>">
                                        <button class="btn-update" type="submit" name="update">Update</button>
                                        <button class="btn-delete" type="submit" name="delete">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>
