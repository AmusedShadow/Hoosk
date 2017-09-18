<?php echo $header; ?>
<!-- CONTENT
=================================-->
<div class="container content-padding">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                {results}
                	<h3>{title}</h3>
                	<p class="meta">Type: {type}</p>
                	<a href="/{url}" class="btn btn-primary">Read More</a>
                {/results}
            </div>
        </div>
    </div>
</div>
<!-- /CONTENT ============-->

<?php echo $footer; ?>
