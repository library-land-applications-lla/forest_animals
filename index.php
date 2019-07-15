<?php
    include('db.php');
    echo $twig->render('homepage.html',
    	               ['page_name' => 'Forest Animals']);
?>