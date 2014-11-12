<?php if(!isset($_SESSION)) session_start(); ?>
<!--custom backgrounds-->
<div id="background" class="blur" style="background-image: <?php echo $_SESSION['bg']; ?>)"></div>
<div id="file-container">
<?php
		require_once($_SERVER['DOCUMENT_ROOT'].'/'.BASEPATH.'/_core/file.php');
		$file = new File();
		echo $file->getAll($_SESSION['user']);
	?>
	
</file>

<div class="file-tooltip uploading" style='left: 500px; top: 510px; display: none'>
	270 kB / 9,240 kB ( 50kB/s ) 
	<div class="btn btn-danger">cancel</div>
</div>

</div>

<!--<canvas id="canvas" width="100" height="100"></canvas>-->

<!-- physics deprecated -->
<!--<script src="_js/Box2D.js"></script>-->
<!--<script src="_js/MouseAndTouch.js"></script>-->
<!--<script src="_js/file_floater_physics.js"></script>-->
<!-- Draggable component requires JQuery UI -->
<script src="/<?=BASEPATH?>/_js/jquery-ui-1.10.3.min.js"></script>
<script src="/<?=BASEPATH?>/_js/file_floater.js"></script>
<script src="/<?=BASEPATH?>/_js/file_upload.js"></script>
