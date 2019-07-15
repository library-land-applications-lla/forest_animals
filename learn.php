<?php
    include('db.php');
    echo $twig->render('learn.html',
    	               ['page_name' => 'Learn']);
?>c