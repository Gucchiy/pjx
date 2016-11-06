<div class="wills form">
<div class="actions">
	<ul>
		<li>
			<?php echo $this->Html->link(__('<< 戻る', true), array('action' => 'index')); ?>
		</li>
	</ul>
</div>

<?php echo $this->Form->create('Will');?>
	<fieldset>
		<legend><?php __('Add Will'); ?></legend>
	<?php
		echo $this->Form->input('title');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Wills', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Willconnectors', true), array('controller' => 'willconnectors', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Willconnector', true), array('controller' => 'willconnectors', 'action' => 'add')); ?> </li>
	</ul>
</div>