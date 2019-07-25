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
        if (file_exists($animals_data[$i]['info_url'])) {
        	$animals_data[$i]['info'] = file_get_contents($animals_data[$i]['info_url']);
        }
    }

    echo $twig->render('learn.html',
    	               ['page_name' => 'Learn',
                        'login_status' => isset($_SESSION['loggedin']),
    	                'animals' => $animals_data]);
?>