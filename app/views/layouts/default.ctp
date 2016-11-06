<!DOCTYPE HTML>
<html lang="ja">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<?php
		echo '<link rel="stylesheet" href="http://code.jquery.com/mobile/1.0.1/jquery.mobile-1.0.1.min.css" />';
		echo '<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.4.min.js"></script>';
		echo '<script type="text/javascript" src="http://code.jquery.com/mobile/1.0.1/jquery.mobile-1.0.1.min.js"></script>';
	?>
	<title>
		<?php __('PjX Prototype - '); ?>
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		// サーチエンジンから見えないようにするよ
		echo $this->Html->meta(array('name' => 'ROBOTS', 'content' => 'NOINDEX, NOFOLLOW'),false);
		echo $this->Html->meta('icon');
		echo $this->Html->css('pjx');
		// echo $this->Html->css('cake.generic');

		echo $scripts_for_layout;
	?>
 
</head>
<body>

<?php
	$preload_file = APP.'views'.DS.strtolower($this->name).DS.$this->action.'_preload.php';

	if(file_exists($preload_file))
		require_once($preload_file);
	
	
	if( !isset($use_dialog) ){
?>	

<div id="home" data-role="page" data-theme="c">	
	<div class="ws-normal">
	<header data-role="header">
		<h1>
			<?php
				if( isset( $fb_logout_url ) ){

					echo "<img src='https://graph.facebook.com/".$fb_me['id']."/picture' width='20' />";					
				}
			?>
			PjX Prototype
		</h1>
	</header>
	</div>
<?php
	}
?>
<?php if( isset($fb_logout_url) ): ?>
	<nav data-role="navbar" data-iconpos="left"><ul>
		<li>
			<?php
				echo $this->Html->link('ホーム',array('controller'=>'wills','action'=>'index'),
						array('data-icon'=>"home",'data-theme'=>"d"));
			?>
		</li>
		<li>
			<?php
				echo $this->Html->link('情報',array('controller'=>'users','action'=>'index'),
						array('data-icon'=>"info",'data-theme'=>"d"));
			?>			
		</li>
		<li>
			<?php
				echo $this->Html->link('実行中',array('controller'=>'wills','action'=>'execution'),
						array('data-icon'=>"grid",'data-theme'=>"d"));
			?>			
		</li>
	</ul></nav>
<?php endif; ?>

	<div data-role="content">

			<?php echo $this->Session->flash(); ?>

			<?php echo $content_for_layout; ?>

	</div>
<?php
	if( !isset($use_dialog) ){		
?>
	<footer data-role="footer">
		<?php 
			echo $this->Html->link(
				$this->Html->image('cake.power.gif', array('alt'=> __('CakePHP: the rapid development php framework', true), 'border' => '0')),
				'http://www.cakephp.org/',
				array('escape' => false, 'data-role'=>'button','class'=>'ui-btn-right'));


			if( isset( $fb_logout_url ) ){

				echo $this->Html->link('設定',array('controller' => 'users', 'action' => 'setting'),
					array('data-role'=>'button','data-theme'=>'b','class'=>'ui-btn-right'));

				echo $this->Html->link('logout',$fb_logout_url,
					array('data-role'=>'button','data-theme'=>'c','class'=>'ui-btn-right'));				
			}
		?>
	</footer>
<?php
	}
?>
	<?php echo $this->element('sql_dump'); ?>

</div>

<?php
	$jqm_additional_file = APP.'views'.DS.strtolower($this->name).DS.$this->action.'_jqm_additional.php';
	// echo 'jqm:'.$jqm_additional_file.'<br />';

	if(file_exists($jqm_additional_file))
		require_once($jqm_additional_file);

	$afterload_file = APP.'views'.DS.strtolower($this->name).DS.$this->action.'_afterload.php';

	if(file_exists($afterload_file))
		require_once($afterload_file);
?>

</body>
</html>