<?php echo $this->Html->link(__('戻る', true), array('controller'=>'wills','action' => 'index'),array('data-role'=>'button','data-inline'=>'true')); ?>

<div class="users form">
	<h1>ユーザー設定</h1>

<?php echo $this->Form->create('User');?>
	<fieldset>
		<legend><?php __('ユーザー情報を修正できます'); ?></legend>
	<?php
		/*
		echo $this->Form->input('id');
		echo $this->Form->input('email');
		echo $this->Form->input('username');
		echo $this->Form->input('password');
		 *
		 */
	?>
	</fieldset>
<?php echo $this->Form->end(array('title'=>'ユーザー情報の修正','data-theme'=>'b'));?>
<?php
	echo $this->Html->link('Twitter認証',array('action'=>'setting_twitter'));
	if( $user['User']['twitter1']!=NULL){
		echo "<p>認証済み</p>";
	}
?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('User.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('User.id'))); ?></li>
		<li><?php echo $this->Html->link(__('New User', true), array('action' => 'add')); ?></li>
	</ul>
</div>
