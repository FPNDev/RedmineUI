<head>
	<?php $v_all = 10 ?>
	<!-- Style Section -->
	<link rel="stylesheet" href="/assets/css/main.css?<?=$v_all?>">
	<link rel="stylesheet" href="/assets/css/scroll.css">
	<link rel="stylesheet" href="/assets/css/tooltips.css?<?=$v_all?>">
	<link rel="stylesheet" href="/assets/css/modal.css?<?=$v_all?>">

	<!-- Font Section -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:100,400" rel="stylesheet">
</head>
<body onresize="onBodyResize();">
	<div class="notifier-bar"></div>
	<div class="tooltip"></div>
	<div id="modal_bg"></div>
	<div id="modal_wrap"></div>
	<div class="page-wrapper">
		<?php handler::path(); ?>
	</div>
	<!-- Script section -->
	<script type="text/javascript" src="/assets/js/common.js?<?=$v_all?>"></script>
	<script type="text/javascript" src="/assets/js/methods.js?<?=$v_all?>"></script>
</body>