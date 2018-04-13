<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $site_title; ?></title>
	<link rel="shortcut icon" type="image/png" href="assets/img/favicon.png" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/main.css">
	<link rel="stylesheet" href="https://unpkg.com/bootstrap-material-design@4.1.1/dist/css/bootstrap-material-design.min.css" crossorigin="anonymous">
  
	<meta name="description" content="<?php if(isset($description)){ echo $description; } ?>">
	<meta name="author" content="<?php if(isset($site_name)){ echo $site_name; } ?>">
	
	<script src="https://www.google.com/recaptcha/api.js"></script>
</head>

<body>
  <div class="main">
    <nav class="navbar navbar-expand-lg navbar-light">
      <div class="container">
        <a class="navbar-brand" href="<?php echo base_url(); ?>"><img src="assets/img/logos/hamer1/logo.png" height="95px" width="auto"alt="Logo"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mr-auto">
			<?php echo $menu_items['left']; ?>
			<?php echo $menu_items['right']; ?>
          </ul>
          <form class="form-inline my-2 my-lg-0" action="http://veilinginfo.nl/search" method="get">
            <input class="form-control mr-sm-2" type="search" placeholder="Zoek" aria-label="Search" name="query">
            <button class="btn btn-raised btn-outline-light my-2 my-sm-0" type="submit">Zoek</button>
          </form>
        </div>
      </div>
    </nav>

    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
      <ol class="carousel-indicators">
        <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
        <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
        <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
      </ol>
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img class="d-block w-100" src="<?php echo base_url(); ?>assets/img/villa.png" alt="First slide">
        </div>
        <div class="carousel-item">
          <img class="d-block w-100" src="<?php echo base_url(); ?>assets/img/auto.png" alt="Second slide">
        </div>
        <div class="carousel-item">
          <img class="d-block w-100" src="<?php echo base_url(); ?>assets/img/yacht.png" alt="Third slide">
        </div>
      </div>
      <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Vorige</span>
      </a>
      <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Volgende</span>
    </a>
    </div>

	<?php $this->load->view($content_view); ?>

    <footer class="page-footer">
      <div class="container">
        <div class="row">
          <div class="col-sm-12 col-md-6 col-lg-6">
            <h5 class="title">Contact</h5>
            <p>070-12345678</p>
          </div>
          <div class="col-sm-12 col-md-6 col-lg-6">
            <h5 class="title">Over</h5>
            <p>Wij zijn veilinginfo.nl</p>
          </div>
        </div>
      </div>

      <div class="footer-copyright">
        <div class="container">
			<span class="left">&copy; <?php echo date('Y'); ?> - <?php echo $site_name; ?></span>
			<span class="right"><?php echo $footer_menu; ?></span>​
        </div>
      </div>
    </footer>
	</div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/popper.js@1.12.6/dist/umd/popper.js" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/bootstrap-material-design@4.1.1/dist/js/bootstrap-material-design.js" crossorigin="anonymous"></script>
	
</body>

</html>