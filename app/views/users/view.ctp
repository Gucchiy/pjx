<div class="users view">
<h2><?php echo $user['User']['username'].'さん'?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $user['User']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Username'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $user['User']['username']; ?>
			&nbsp;
		</dd>
	</dl>
	<img src='https://graph.facebook.com/<?=$user['User']['fbid']?>/picture' />

	<div class="ws-normal">
		
	<div data-role="collapsible" data-theme="b" data-content-theme="b">
		<h2>
			<?php echo $user['User']['username']; __('さんの目標');?>
		</h2>
		
		<table cellpadding="0" cellspacing="0">
		<tr>
			<td>ID</td>
			<td>目標</td>
		</tr>	
		<?php
		$i = 0;
		foreach ($wills as $will):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $will['Will']['id']; ?>&nbsp;</td>
			<td><?php echo $will['Will']['title']; ?>&nbsp;</td>
		</tr>
	<?php endforeach; ?>
		</table>	
	</div>

	<div data-role="collapsible" data-theme="b" data-content-theme="b">
		
		<h2>
			<?php echo $user['User']['username']; __('さんのインタビューリスト');?>
		</h2>

		<div data-role="collapsible-set" data-theme="c" data-content-tehme="c">
		<?php
			foreach( $answers as $answer ):
		?>
				<div data-role="collapsible">
					<h3><?=$answer['Question']['question']?></h3>
					<p>A:<?=$answer['Answer']['content']?></p>
				</div>
		<?php		
			endforeach;
		?>
		</div>

	</div>
	
	</div>	<!-- end of ws-normal -->		

</div>
