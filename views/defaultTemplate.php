<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<h1>Ceci est mon template</h1>
	
	<?php include $this->v;?>
	<br>
	<?php 
        if(isset($_SESSION['facebook_access_token'])){
    ?>
        <a href= <?php echo $logUrl ?>>Se déconnecter</a>

    <?php
        }else{
            echo "<a href='".$logUrl."'>Se connecter</a>";
        }
    ?>
	
</body>
</html>