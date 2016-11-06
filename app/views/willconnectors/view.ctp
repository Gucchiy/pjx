<div class="willconnectors view">
<h2><?php  __('Willconnector');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $willconnector['Willconnector']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('User'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($willconnector['User']['email'], array('controller' => 'users', 'action' => 'view', $willconnector['User']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Will'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($willconnector['Will']['title'], array('controller' => 'wills', 'action' => 'view', $willconnector['Will']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Parent Will Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $willconnector['Willconnector']['parent_will_id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $willconnector['Willconnector']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $willconnector['Willconnector']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Willconnector', true), array('action' => 'edit', $willconnector['Willconnector']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Willconnector', true), array('action' => 'delete', $willconnector['Willconnector']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $willconnector['Willconnector']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Willconnectors', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Willconnector', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Wills', true), array('controller' => 'wills', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Will', true), array('controller' => 'wills', 'action' => 'add')); ?> </li>
	</ul>
</div>
