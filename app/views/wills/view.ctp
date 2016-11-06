<div class="wills view">
<?php echo $this->Html->link(__('戻る', true), array('action' => 'index'),array('data-role'=>'button', 'class'=>"ui-btn-right",'data-inline'=>'true','data-direction'=>'reverse' )); ?>
<div data-role="collapsible"> 
	<h2>挑戦:<?php echo $will['Will']['title'];?></h2>
	<p>
		ID:&nbsp;<?php echo $will['Will']['id']; ?><br />
		作成:&nbsp;<?php echo $will['Will']['created']; ?><br />
		更新:&nbsp;<?php echo $will['Will']['modified']; ?><br />
	</p>
	<?php echo $this->Html->link(__('編集', true), array('action' => 'edit', $will['Will']['id']), array('data-role'=>"button",'data-inline'=>"true")); ?>			
	<?php echo $this->Html->link(__('削除', true), array('action' => 'delete', $will['Will']['id']), array('data-role'=>"button",'data-inline'=>"true", 'data-ajax'=>"false"), __('削除してよろしいですか？', true)); ?>			
</div>

</div>
<h3>挑戦:<?php echo $will['Will']['title'];?>のメニュー一覧</h3>

<table cellpadding="0" cellspacing="0">
<tr>
		<th><?php echo $this->Paginator->sort('id','Will.id');?></th>
		<th><?php echo $this->Paginator->sort('メニュー','Will.title');?></th>
</tr>
<?php
$i = 0;
foreach ($millstones as $millstone):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
<tr<?php echo $class;?>>
	<td><?php echo $millstone['Will']['id']; ?>&nbsp;</td>
	<td><?php echo $this->Html->link($millstone['Will']['title'],array('action' => 'progress', $millstone['Willconnector']['id'])); ?>&nbsp;</td>
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
	<p>	<?php echo $this->Paginator->counter(array('format'=>'全部で%count%個のメニューが選択されています！'));?></p>

<div class="millstone form">
<?php
	// print_r( $recommends );
?>
<?php echo $this->Form->create(null,array('type'=>'post','action'=>'./view/'.$will['Willconnector']['id'])); ?>
	<fieldset>
	<?php
		// print_r($recommend_datas);
		echo $this->Form->input('Will.title', array('label'=>'自分でメニューを作成する'));
		echo $this->Form->submit('メニューの追加',array('data-theme'=>'b','data-ajax'=>'false','rel'=>'external'));


		if( count( $recommend_datas ) ){

			echo "<h3>挑戦: ".$will['Will']['title']." へのお勧めメニューが見つかりました！</h3>";	
			echo '<div class="input select"><label for="WillRecommends">以下のメニューがお勧めです</label><input name="data[Will][recommends]" value="" id="WillRecommends" type="hidden">';
			foreach( $recommend_datas as $recommend_data ){
					
				$id = $recommend_data['Will']['id'];
				$label = $this->Html->link($recommend_data['Will']['title'], array('action'=>'show',$id));
				echo '<div class="checkbox"><input name="data[Will][recommends][]" value="'.$id
						.'" id="WillRecommends'.$id.'" type="checkbox"><label for="WillRecommends'.$id.'">'
						.$recommend_data['Will']['title'].'</label></div>'."\n";
				foreach( $recommend_data['Users'] as $recommend_user ){
					
					$img = "https://graph.facebook.com/".$recommend_user['User']['fbid']."/picture";
					echo $this->Html->image( $img, array('alt'=>$recommend_user['User']['username'],'border'=>'0', 'width'=>'20'));
					
				}
				
			}
			echo "</div>\n";
			echo $this->Form->end(array('label'=>'メニューの追加','data-theme'=>'b'));
		}
		
	?>
	</fieldset>
</div>

