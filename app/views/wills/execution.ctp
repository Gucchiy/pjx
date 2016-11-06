<div class="wills index">

<?php
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

		<h2><?php
			echo $fb_me['username'].'さんのメニュー一覧'; 
		?></h2>

<?php
	//	echo $this->Html->link('挑戦インタビューを受ける',
	//		array('action'=>'interview'),array('data-theme'=>'e','data-role'=>'button'));
?>
	</div>
	<h2>
		<?php __('挑戦リスト');?>
	</h2>

	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('メニュー','Will.title');?></th>
			<th><?php echo $this->Paginator->sort('挑戦','ParentWillconnector.title');?></th>
	</tr>
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
		<td><?php echo $this->Html->link($will['Will']['title'],array('action' => 'progress', $will['Willconnector']['id'])); ?>&nbsp;</td>
		<td><?php echo $will['ParentWillconnector']['Will']['title'];?>&nbsp;</td>
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
	<p>	<?php echo $this->Paginator->counter(array('format'=>'全部で%count%個のメニューが登録されています！'));?></p>
</div>

