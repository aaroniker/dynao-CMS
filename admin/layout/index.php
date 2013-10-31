<!DOCTYPE html>
<html lang="de">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Backend - <?php echo dyn::get('hp_name'); ?></title>
	<?php echo layout::getCSS(); ?>
</head>

<body>
	<div id="navi">
		<?php echo backend::getNavi(); ?>
	</div><!--end #navi-->
	
    <div id="wrap">
        <div id="subnavi">
            <div id="user">
            
                <img src="layout/img/user/defaultm.png" alt="Profilbild" />
                
                <a class="icon-cog settings" href=""></a>
                    
                <h3>Aaron Iker</h3>
                Administrator
                
                <a class="icon-envelope messages" href=""><span>2</span></a>
                
                <a href="index.php?logout=1" class="icon-lock logout"> <span>Logout</span></a>
            
            </div><!--end #user-->
            
            <h1>Dashboard</h1>
            <div id="mobil">Navigation</div>
            <?php echo backend::getSubnavi(); ?>
            
        </div><!--end #subnavi-->
        
        <div id="content">
            <?php echo $CONTENT; ?>		
        </div><!--end #content-->
        
        <div class="clearfix"></div>
    </div><!--end #wrap-->
	
	<div id="tools">
	
		<a id="trash" href=""></a>
		
	</div><!--end #tools-->

<?php echo layout::getJS(); ?>
</body>
</html>