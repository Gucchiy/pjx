<?php
	print_r( $user_data);
?>
<div class="todos form">
<?php echo $this->Form->create('Todo');?>
	<fieldset>
		<legend><?php __('Add Todo'); ?></legend>
	<?php
		// echo $this->Form->input('user_id');
		echo $this->Form->input('user_id', array('type'=>'hidden', 'value'=> $user_data['id']));
		echo $this->Form->input('title');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Todos', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
	</ul>
</div>