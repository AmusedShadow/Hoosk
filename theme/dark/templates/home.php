<?php echo $header; ?>
<!-- JUMBOTRON
=================================-->
<?php
$enabled   = false;
$slider    = false;
$jumbotron = false;

if ($page['enableSlider'] == 1) {
    $enabled = true;
    $slider  = true;
}

if ($page['enableJumbotron'] == 1) {
    $enabled   = true;
    $jumbotron = true;
}

if ($enabled == true) {
    echo '<div class="jumbotron text-center">';

    if ($slider == true) {
        echo
        '
<div id="carousel" class="carousel slide " data-ride="carousel">
  ' . getCarousel($page['pageID']) . '
  <a class="left carousel-control" href="#carousel" role="button" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>

  <a class="right carousel-control" href="#carousel" role="button" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>
';
    }

    if ($jumbotron == true) {
        echo
            '
    <div class="container content-padding">
      <div class="row">
        <div class="col-md-12">
          ' . $page['jumbotronHTML'] . '
        </div>
      </div>
    </div>
  ';
    }

    echo '</div>';
}
?>



<!-- /JUMBOTRON container-->
<!-- CONTENT
=================================-->
<div class="container content-padding">
    <?php echo $page['pageContentHTML']; ?>
</div>
<!-- /CONTENT ============-->

<?php echo $footer; ?>
