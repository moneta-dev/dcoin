<style>
	#page-wrapper{
		margin: 0px 10% 0px 10%;
		border: 1px solid #E7E7E7;
		min-height: 550px;
	}
	#wrapper{height: 100%;}
	#dc_content{
		height: 550px;
		vertical-align: middle;
	}
</style>

<?php
if (!$user_id) {
?>
<nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="margin-bottom: 0">
	<div class="navbar-header">
		<a class="navbar-brand" href="#" style="display: block;
	                   background: url(img/logo.png) center left no-repeat;
	                   text-align: center;
	                   background-size: 30px 30px;
	                   padding-left: 40px; margin-left: 15px; margin-right: 50px" onclick="fc_navigate('home')">Dcoin </a>
	</div>
	<!-- /.navbar-header -->

	<ul class="nav navbar-top-links navbar-right">
		<!-- /.dropdown -->
		<li class="dropdown">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#">
				<i class="fa  fa-globe fa-fw"></i> Language <i class="fa fa-caret-down"></i>
			</a>
			<ul class="dropdown-menu dropdown-user">
				<li><a href="#" onclick="fc_navigate('home', 'lang=en'); load_menu();">English</a>
				</li>
				<li><a href="#" onclick="fc_navigate('home', 'lang=ru'); load_menu();">Русский</a>
				</li>
			</ul>
			<!-- /.dropdown-user -->
		</li>
		<!-- /.dropdown -->
	</ul>
	<!-- /.navbar-top-links -->

</nav>
<?php
}
?>
<div style="margin-left: -42px; width:84px;position: absolute;top: 50%;left: 50%;  ">
	<?php
	if ($tpl['pool_tech_works'])
		echo '<div class="alert alert-info" style="width: 540px;position:absolute; top: 50%; left: 50%; margin-left: -270px; text-align:center">'.$lng['pool_tech_works'].'</div><div id="show_login" style="width: 40px; height:40px"></div>';
	else
		echo '<button type="button" class="btn btn-primary btn-lg" id="show_login">'.$lng['login'].'</button>';
	?>

</div>

<?php if (!$user_id) echo '<div class="alert alert-info" style="width: 540px;position:absolute; bottom:0; left: 50%; margin-left: -270px; text-align:center">'.$lng['login_help_text'].'</div>' ?>

<?php
require_once( ABSPATH . 'templates/modal.tpl' );
echo str_ireplace('myModal', 'myModalLogin', $modal);
?>

<script>
	$('#myModal').remove();
	$('#show_login').bind('click', function () {
		$('#myModalLogin').modal({ backdrop: 'static' });
	});
</script>
<div class="for-signature"></div>
