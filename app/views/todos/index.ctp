<div class="todos form">
<?php echo $this->Form->create('Todo');?>
	<fieldset>
		<legend><?php __('Add Todo'); ?></legend>
	<?php
		// echo $this->Form->input('user_id');
		// ユーザー ID は HABTM 化でいらなくなった
		// echo $this->Form->input('user_id', array('type'=>'hidden', 'value'=> $user_data['id']));
		echo $this->Form->input('title');
	?>
	<?php echo $this->Form->end(__('Submit', true));?>
	</fieldset>
</div>
	

<div class="todos index">
	<h2><?php __('Todos');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('title');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	
	foreach ($todos as $todo):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';	// 表の段ごとに色を変える処理
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $todo['Todo']['title']; ?>&nbsp;</td>
		<td><?php echo $todo['Todo']['created']; ?>&nbsp;</td>
		<td><?php echo $todo['Todo']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $todo['Todo']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $todo['Todo']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $todo['Todo']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $todo['Todo']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>

	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Todo', true), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
	</ul>
</div>