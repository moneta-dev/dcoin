<!-- container -->
<div class="container">

	<script>
		$('#save').bind('click', function () {
			$('#wait').text('<?php echo $lng['please_wait']?>');
			$.post( 'ajax/clear_db.php', { } ,
					function () { 
						fc_navigate ('nulling', {'alert': 'Complete! Press F5'} );
					});
		});
	</script>

  <legend><h2><?php echo $lng['nulling_title']?></h2></legend>
 	<?php echo ($tpl['alert'])?'<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'.$tpl['alert'].'</div>':''?>
	<div class="alert alert-success" id="wait" style="display:none"></div>

    
    <div id="new">
		<button class="btn" id="save"><?php echo $lng['clear_db']?></button>

    </div>
     
    

</div>
<!-- /container -->