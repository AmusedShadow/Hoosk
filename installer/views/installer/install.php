<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Install Hoosk</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<link href="/installer/css/bootstrap.min.css" rel="stylesheet">
		<!--[if lt IE 9]>
			<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
<link href="/installer/css/styles.css" rel="stylesheet">
<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>

<!-- JUMBOTRON
=================================-->
<div class="jumbotron text-center errorpadding">
    <div class="container">
      <div class="row">
        <div class="col col-lg-12 col-sm-12">
        <img src="/installer/images/large_logo.png" />
        <h1>Install Hoosk.</h1>

        </div>
      </div>
    </div>
</div>
<!-- /JUMBOTRON container-->
<!-- CONTENT
=================================-->
<div class="container text-center">
	<div class="row" id="getDetails">
    <div class="col col-lg-3 col-sm-3"></div>
    	<div class="col col-lg-6 col-sm-6">
    	<?php
echo validation_errors('<div class="alert alert-info">', '</div>');
?>
            <form action="#" method="post">
        		<div class="control-group">
					<label class="control-label" for="siteName">Site Name</label>
					<div class="controls">
						<input type="text" id="siteName" name="siteName" value="<?php echo set_value('siteName'); ?>" class="span5">
					</div> <!-- /controls -->
				</div> <!-- /control-group -->
        		<div class="control-group">
					<label class="control-label" for="siteURL">Site URL</label>
					<div class="controls">
						<input type="text" id="siteURL" name="siteURL" value="<?php echo set_value('siteURL', site_url()); ?>" class="span5">
					</div> <!-- /controls -->
				</div> <!-- /control-group -->
        		<hr>
        		<div class="control-group">
					<label class="control-label" for="dbName">Database Name</label>
					<div class="controls">
						<input type="text" id="dbName" name="dbName" value="<?php echo set_value('dbName'); ?>" class="span5">
					</div> <!-- /controls -->
				</div> <!-- /control-group -->
        		<div class="control-group">
					<label class="control-label" for="dbUserName">Database Username</label>
					<div class="controls">
						<input type="text" id="dbUserName" name="dbUserName" value="<?php echo set_value('dbUserName'); ?>" class="span5">
					</div> <!-- /controls -->
				</div> <!-- /control-group -->
        		<div class="control-group">
					<label class="control-label" for="dbPass">Database Password</label>
					<div class="controls">
						<input type="text" id="dbPass" name="dbPass" class="span5" value="<?php echo set_value('dbPass'); ?>">
					</div> <!-- /controls -->
				</div> <!-- /control-group -->
         		<div class="control-group">
					<label class="control-label" for="dbHost">Database Host</label>
					<div class="controls">
						<input type="text" id="dbHost" name="dbHost" value="<?php echo set_value('dbHost', 'localhost'); ?>" class="span5">
					</div> <!-- /controls -->
				</div> <!-- /control-group -->

				<div class="control-group">
					<label class="control-label" for="timezone">Timezone</label>
					<div class="controls">
						<?php
echo form_dropdown('timezone', $timezones, 'America/New_York');
?>
					</div> <!-- /controls -->
				</div> <!-- /control-group -->

        <button class="btn-primary btn">Install</button>
        </div>
        <div class="col col-lg-3 col-sm-3"></div>
	</div>
  	<hr>
</div>
<!-- /CONTENT ============-->

    <!-- FOOTER
    =================================-->
    <div class="container">
     <p>&copy; Hoosk <?php echo date('Y'); ?></p>
    </div>
	<!-- /FOOTER ============-->

   	<!-- script references -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
		<script src="/installer/js/bootstrap.min.js"></script>
	</body>
</html>