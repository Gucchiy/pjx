<div class="wills form">
<a href='<?=$back_url?>' data-role='button' data-inline='true'>戻る</a>

<?php echo $this->Form->create('Will');?>
	<fieldset>
		<legend><?php __('Edit Will'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('title');
	?>
	</fieldset>
<?php echo $this->Form->end(array('label'=>'修正','data-theme'=>'b'));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Will.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Will.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Wills', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Willconnectors', true), array('controller' => 'willconnectors', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Willconnector', true), array('controller' => 'willconnectors', 'action' => 'add')); ?> </li>
	</ul>
</div>