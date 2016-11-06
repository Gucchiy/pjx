<div class="willconnectors form">
<?php echo $this->Form->create('Willconnector');?>
	<fieldset>
		<legend><?php __('Add Willconnector'); ?></legend>
	<?php
		echo $this->Form->input('user_id');
		echo $this->Form->input('will_id');
		echo $this->Form->input('parent_will_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Willconnectors', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Wills', true), array('controller' => 'wills', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Will', true), array('controller' => 'wills', 'action' => 'add')); ?> </li>
	</ul>
</div>