<div class="wills view">
<a href='<?=$back_url?>' data-role='button' data-inline='true' data-direction='reverse'>戻る</a>
<div data-role="collapsible"> 
	<h2>メニュー:<?php echo $will['Will']['title'];?></h2>
	<p><?php echo $will['Will']['title'];?></p>
	<p>
		ID:&nbsp;<?php echo $will['Will']['id']; ?><br />
		作成:&nbsp;<?php echo $will['Will']['created']; ?><br />
		更新:&nbsp;<?php echo $will['Will']['modified']; ?><br />
		<?php		
			if( $will['Willconnector']['started'] == null ){
				
				echo "未開始<br />";
			
			}else{
				echo "開始日時：&nbsp;".$will['Willconnector']['started'];
			}
			echo $this->Form->create('Willconnector', array('url'=>'/wills/progress/'.$will['Willconnector']['id']));
			echo $this->Form->input('Willconnector.id');
			echo $this->Form->select('Willconnector.period',
				array('0'=>'選択してください','7'=>'一週間', '30'=>'一か月', '365'=>'1年') );
			echo "<fieldset class='ui-grid-a'>\n";
			echo $this->Form->submit( '更新', array('data-theme'=>'b','div'=>'ui-block-a') );

			$delete_btn = $this->Html->link(__('削除', true), 
				array('action' => 'delete_Willconnector', $will['Willconnector']['id']), 
					array('data-role'=>"button"), sprintf(__('削除してよろしいですか？', true), $will['Will']['id']));
			echo $this->Html->div('ui-block-b', $delete_btn, array('escape'=>false));
			echo "</fieldset>";
			echo $this->Form->end();
		?>
		
	</p>

</div>

<?php
	switch( $will['Willconnector']['period']){
		case 7:
			$disp = '1週間';
			break;
		case 30:
			$disp = '1か月間';
			break;
		default: 
			$disp = '期限未設定'; 
	}

?>

<P><?=$disp?>コース現在の進捗状況:</P>
<p>
<?php
	echo str_repeat('◆',$count);
?>
</p>
<div class="tweets form">
<?php echo $this->Form->create('Will', array('url'=>'/wills/progress/'.$will['Willconnector']['id']));?>
	<fieldset>
		<legend><?php __('進捗を呟くよ'); ?></legend>
	<?php
		echo $this->Form->input('Willconnector.id');
		echo $this->Form->input('Tweet.content',array('label'=>false));
		echo $this->Form->submit('今日も実施！',array('data-theme'=>'b'))
	?>
	</fieldset>
<?php echo $this->Form->end();?>
</div>

<table cellpadding="0" cellspacing="0">
	<?php
	$i = 0;
	foreach ($tweets as $tweet):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $tweet['Tweet']['content']; ?>&nbsp;</td>
		<td><?php echo $tweet['Tweet']['modified']; ?>&nbsp;</td>
	</tr>
<?php endforeach; ?>
</table>

</div>
