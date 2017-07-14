<head>
	<!-- Style Section -->
	<link rel="stylesheet" href="/assets/css/main.css">
	<link rel="stylesheet" href="/assets/css/scroll.css">
	<link rel="stylesheet" href="/assets/css/tooltips.css">
	<link rel="stylesheet" href="/assets/css/modal.css">

	<!-- Font Section -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:100,400" rel="stylesheet">
</head>
<body onresize="onBodyResize();">
	<div class="notifier-bar"></div>
	<div class="tooltip"></div>
	<div id="modal_bg"></div>
	<div id="modal_wrap"></div>
	<div class="page-wrapper">
		<?php handler::path(); if(!isset($_PAGE['no_template']) && !isset($_POST['ajax'])): ?>
	</div>
	<!-- Script section -->
	<script type="text/javascript" src="/assets/js/common.js"></script>
	<script type="text/javascript" src="/assets/js/methods.js"></script>
</body>
<?php endif; ?>