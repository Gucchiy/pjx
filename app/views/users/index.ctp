<?php 
	$username = $fb_me['username'];

	// echo $this->Html->link(__('戻る', true), 
	//	array('controller'=>'wills','action' => 'index'),array('data-role'=>'button','data-inline'=>'true')); 
	// echo $this->Html->link(__('戻る', true), 
	//	array(),array('data-role'=>'button','data-inline'=>'true','data-direction'=>'reverse','onClick'=>'history.back()')
	// ); 
?>

<div class="users index">
	
	<?php
		// print_r($friends);
	?>
	
	<h1><?=$username;?>さんの情報</h1>
	<img src='https://graph.facebook.com/<?=$fb_me['id']?>/picture' />

	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $user_data['User']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Email'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $user_data['User']['email']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Username'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $user_data['User']['username']; ?>
			&nbsp;
		</dd>
	</dl>

	<?php
		foreach( $friends as $friend ){

			$img = "https://graph.facebook.com/".$friend['User']['fbid']."/picture";
			echo $this->Html->image( $img, array('alt'=>$friend['User']['username'],'border'=>'0', 'width'=>'20'));
		}
	?>


	<div class="ws-normal"> <div data-role="collapsible" data-theme="b" data-content-theme="b">
		<h3>
			<?='友達リスト'?>
		</h3>
		<div data-role="collapsible-set" data-theme="c" data-content-theme="c">
		<?php			
			foreach( $friends as $friend ):
				$img = "https://graph.facebook.com/".$friend['User']['fbid']."/picture";
		?>
			<div data-role="collapsible">
				<h3>
					<?php
						echo $this->Html->image( $img, array('width'=>'20','alt'=>$friend['User']['username'],'boder'=>'0'));
						echo $friend['User']['username'].'さん';
					?>
				</h3>
			<?php	
				echo $this->Html->link(
					$this->Html->image( $img, array('alt'=>$friend['User']['username'],'boder'=>'0')),
					array('action'=>'view',$friend['User']['id']), array('escape'=>false));
			?>	
			</div>
		<?php	
			endforeach;
		?>
		</div>
	</div> </div>

	<div class="ws-normal"> <div data-role="collapsible" data-theme="b" data-content-theme="b">
		<h3>
			<?='インタビューリスト'?>
		</h3>
		<div data-role="collapsible-set" data-theme="c" data-content-theme="c">
		<?php
			foreach ($answers as $answer):
		?>
			<div data-role="collapsible">
				<h3>Q:<?=$answer['Question']['question']?></h3>
				<?php echo $this->Form->create('User');?>
					<fieldset data-role=“fieldcontain”>
						<?php
							echo $this->Form->input('Answer.content', array('label'=>'Answer:','value'=>$answer['Answer']['content'] ));
							echo $this->Form->hidden('Answer.id', array('value'=>$answer['Answer']['id']));
						?>
					</fieldset>
					<fieldset class="ui-grid-a"> 
						<?php
							echo $this->Form->submit('修正',array('data-theme'=>'b','div'=>'ui-block-a'));
							$button_delete = $this->Html->link('削除',array('action'=>'delete_answer',$answer['Answer']['id']),
									array('data-role'=>'button','data-inline'=>'false','data-ajax'=>'false'),__('削除してよろしいですか？', true));
							echo $this->Html->div('ui-block-b', $button_delete, array('escape'=>false));
						?>
					</fieldset>
				<?php echo $this->Form->end();?>
				
				
			</div> 
		<?php
			endforeach;
		?>
		</div>
	</div> </div>


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
	<p>	<?php echo $this->Paginator->counter(array('format'=>'全部で%count%個のインタビューが登録されています！'));?></p>

</div>

<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

	</ul>
</div>
