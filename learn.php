<?php
    require_once('db.php');

    session_start();

    $get_animal_data = "SELECT * FROM Animals";
    $animals_set = mysqli_query($connection, $get_animal_data);
    if (!$animals_set) { echo mysqli_error($connection); }
    $animals_data = mysqli_fetch_all($animals_set, MYSQLI_BOTH);
    mysqli_free_result($animals_set);
    mysqli_next_result($connection);

    for ($i = 0; $i < count($animals_data); $i++) {
    	$animals_data[$i]['image_url'] = $config['app_root']
    	                                 . $config['images_location']
    	                                 . $animals_data[$i]['image_url'];
    	$animals_data[$i]['info_url'] = $config['info_location']
                                        . $animals_data[$i]['info_url'];
        $animals_data[$i]['index'] = $i;
        if (file_exists($animals_data[$i]['info_url'])) {
        	$animals_data[$i]['info'] = file_get_contents($animals_data[$i]['info_url']);
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Check if all fields are filled
        if (!isset($_POST['animal_name']) || empty(trim($_POST['animal_name'], ' '))
            || !isset($_POST['animal_description']) || empty(trim($_POST['animal_description'], ' '))
            || empty($_FILES)) {
                $_SESSION['error'] = array('message' => 'Fill all fields.',
                                           'return_page_url' => './learn.php',
                                           'return_button_text' => 'Return');
                Header('Location: ./error.php');              
        }
        

        // Check if animal already exists
        $info_filename = $config['info_location'] . $_POST['animal_name'] . ".txt";
        if (file_exists($info_filename)) {
            $_SESSION['error'] = array('message' => 'The animal ' . $_POST['animal_name'] . ' is already included.',
                                       'return_page_url' => './learn.php',
                                       'return_button_text' => 'Return');
            Header('Location: ./error.php');
        }
        
       // Check if image already exists
        $image_filename = $config['images_location'] . $_FILES['upload_image']['name'];
        if (file_exists($image_filename)) {
            $_SESSION['error'] = array('message' => 'File: ' . $image_filename . ' already exists.',
                                       'return_page_url' => './learn.php',
                                       'return_button_text' => 'Return');
            Header('Location: ./error.php');
        }

        // Check if file is appropriate
       $image_file_type = strtolower(pathinfo($image_filename,PATHINFO_EXTENSION));
       $file_size = $config['images_location'] . $_FILES['upload_image']['size'];
       if (!in_array($image_file_type, array('jpg', 'jpeg', 'png', 'gif')) 
           || $file_size > 500000) {
                $_SESSION['error'] = array('message' => 'Inappropriate file type or file size too large',
                                           'return_page_url' => './learn.php',
                                           'return_button_text' => 'Return');     
                Header('Location: ./error.php');       
       }

       // If no errors, add animal to database
       $add_animal_query = 'INSERT INTO Animals (name, info_url, image_url) VALUES (?, ?, ?)';
        if ($add_animal_stmt = mysqli_prepare($connection, $add_animal_query)) {
            mysqli_stmt_bind_param($add_animal_stmt, "sss", $_POST["animal_name"], $info_filename, $image_filename);
            $add_animal_stmt->execute();
            while(mysqli_next_result($connection)){;}
        }
    }

    echo $twig->render('learn.html',
    	               ['page_name' => 'Learn',
                        'login_status' => isset($_SESSION['loggedin']),
    	                'animals' => $animals_data,
                        'is_admin' => isset($_SESSION['loggedin']) ? $_SESSION["admin"]:False]);

/*
        if (isset($_POST["edit_info_textarea"])) {
            file_put_contents($animals_data[$_POST["array_index"]]['info_url'], $_POST["edit_info_textarea"]);
            #Header('Location: '.$_SERVER['PHP_SELF']);
        }
        else if (isset($_POST["animal_name"])
            && isset($_POST["animal_info"]) && isset($_POST["animal_image"])) {
            $animal_name = $_POST["animal_name"];
            $animal_info = $_POST["animal_info"];
            $target_file = $config["app_root"]
                           .$config["images_location"]
                           .str_replace(" ", "_", $animal_name);
            #More to do
        }
        else {
            $_SESSION["error"] = array("message" => "Please fill all fields before submitting.",
                                       "return_page_url" => "./learn.php",
                                       "return_button_text" => "Return",);
            Header('Location: ./error.php');
        } 

        // Check if animal exists already
       if (isset($_POST[animal_name])) {
            $info_file = $config['info_location'] . $animal_name;
            if (file_exists($info_file)) {
                $_SESSION['error'] = array('message' => 'Animal already included.',
                                           'return_page_url' => './learn.php',
                                           'return_button_text' => 'Return');
                Header('Location: ./error.php');
            }
       }
       // Check if image exists already
       if (isset($_POST[upload_image])) {
            $image_file = $config['images_location'] . $_FILES['upload_image'];
            if (file_exists($image_file)) {
                $_SESSION['error'] = array('message' => 'File: ' . $image_File . ' already exists.',
                                           'return_page_url' => './learn.php',
                                           'return_button_text' => 'Return');
                Header('Location: ./error.php');
            }
       }
*/

?>