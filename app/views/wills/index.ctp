<div class="wills index">

<?php
	if( isset($fb_login_url)){

			echo $this->Html->div(
				'login',
				$this->Html->link(
					$this->Html->image('pjx/facebook_login.png', array('alt'=> __('CFacebookへログイン', true), 'border' => '0')),
					$fb_login_url,
					array('escape' => false)
				),
				array('escape'=>false)				
			);

	}else{

	if( isset( $debug_data ) ){
		
		print_r($debug_data);
		// echo "test2";
		
	}
	// print_r($wills);
	//	echo "debug:".$debug_data."<br />";
	// print_r($questions);
	// echo "test";
?>
	<div class="wills form">

		<h2>
<?php
			echo $this->Html->link($fb_me['username'],array('controller'=>'users','action'=>'index'));
			echo __('さんのことを教えてください！'); 
?>
		</h2>

<?php
	//	echo $this->Html->link('挑戦インタビューを受ける',
	//		array('action'=>'interview'),array('data-theme'=>'e','data-role'=>'button'));
?>
		<p>あなたのことを教えてください！そこからあなたの夢をかなえるための目標が見えてくるかも知れません</p>
		<a href="#question" data-theme='e' data-role='button' data-ajax='false'>インタビューを受ける</a>
		<a href="#custom" data-theme='b' data-role='button'>自分で目標入力する</a>
	</div>

	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('目標','Will.title');?></th>
	</tr>
	<h2>
		<?php __('目標リスト');?>
	</h2>
	<?php
	$i = 0;
	foreach ($wills as $will):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $will['Willconnector']['id']; ?>&nbsp;</td>
		<td><?php echo $this->Html->link($will['Will']['title'],array('action' => 'view', $will['Willconnector']['id'])); ?>&nbsp;</td>
	</tr>
<?php endforeach; ?>
	</table>

	<div class="paging">
		<?php echo $this->Paginator->numbers();?>
		<div data-role="controlgroup" data-type="horizontal"> 
			<?php
				if( $this->Paginator->hasPrev()){
					echo $this->Paginator->prev('' . __('previous', true), 
						array('data-role'=>'button','data-icon'=>'arrow-l'), null, array('class' => 'disabled'));
				} 
				if( $this->Paginator->hasNext()){
					echo $this->Paginator->next(__('next', true) . '', 
						array('data-role'=>'button','data-icon'=>'arrow-r','data-iconpos'=>'right'), null, array('class' => 'disabled'));					
				}
			?>
		</div> 
	</div>
	<p>	<?php echo $this->Paginator->counter(array('format'=>'全部で%count%個の挑戦が登録されています！'));?></p>
</div>
<?php } ?>

