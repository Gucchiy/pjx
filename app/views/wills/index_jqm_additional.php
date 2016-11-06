<?php
	if( !isset($fb_login_url) ):
?>

<!-- Start of second page -->
<div data-role="dialog" id="custom">

	<div data-role="header">
		<h1>自由入力</h1>
	</div><!-- /header -->

	<div data-role="content">
		<p><?php echo $fb_me['username'];__('さんの目標を教えてください！'); ?></p>
		<?php echo $this->Form->create('Will', array('data-ajax'=>'false'));?>
			<fieldset data-role=“fieldcontain”>
				<?php
					echo $this->Form->input('title', array('label'=>false ));
				?>
			</fieldset>
			<fieldset class="ui-grid-a"> 
				<?php
					echo $this->Form->submit('挑戦追加',array('data-theme'=>'b','data-ajax'=>'false','data-rel'=>'external','div'=>'ui-block-a'));
				?>
				<div class="ui-block-b"><a href="#home" data-theme='c' data-role='button'>ｷｬﾝｾﾙ</a></div>
			</fieldset>
		<?php echo $this->Form->end();?>


	</div><!-- /content -->


</div><!-- /dialog -->

	
<?php
	// print_r($question);
	// echo $this->Form->create('Will');
		
	if( isset($question['Question']['question']) ){
?>

		<script language="JavaScript">
			
			
			$(document).ready(function(){
				$('#answer').val('');
				$('#new_will').val('');
				$('#ans_content').val('');

				$('#next').click(function(){
					// alert('test');
					
					$('#new_will').val($('#answer').val());
					$('#ans_content').val($('#answer').val());
					if($('#answer').val()==""){
						
						alert('インタビューにお答えください');
						return false;
					}
				});
				$('#cancel').click(function(){
					
					$('#new_will').val('');
				});
			});

			/*
			// jQuery Mobile のページ遷移イベントで何かする場合
			$(document).bind('pagebeforechange', function(e,d){
				
				if(typeof d.toPage == 'string'){
					if( d.toPage == document.URL+'#question'){
						alert('come?');
						$('#answer').val('aaa');
						$('#new_will').val('');
						$('#ans_content').val('');
					}
				}
			} );
			*/

		</script>
		
		<div data-role="dialog" id="question">
		
			<div class="ws-normal">
			<div data-role="header">
				<h1>Interview</h1>
			</div><!-- /header -->
			</div>
		
			<div data-role="content">
				
			<?php
				if( isset($question['PrevQuestion']['id'])){
					echo "<p>あなたは、「Q:".$question['PrevQuestion']['question']."」に対して、";
					$answer = "";
					foreach($question['PrevQuestion']['Answer'] as $answer ){
					
						if( $answer['user_id'] == $user_data['User']['id'] ){
							echo "「".$answer['content']."」と答えています。<br /></p>";
							break;
						}	
					}					
					
				}
			?>

			<h3><?=$question['Question']['question']?></h3>
			<?php
				echo $this->Form->create('Will', array('data-ajax'=>'false'));
				echo $this->Form->hidden("Answer.question_id", array('value'=>$question['Question']['id']));
				echo $this->Form->hidden("Answer.user_id",array('value'=>$user_data['User']['id']));
				echo $this->Form->input('Answer.content', array('label'=>false,'id'=>'answer' ));
				// <input type='text' value="" name="data[Answer][content]" id="answer" />
			?>
			
			<fieldset class="ui-grid-a">
				<div class="ui-block-a">
				<?php
					if( $question['Question']['purpose']==0){

						echo $this->Form->submit('登録',array('data-theme'=>'b','data-ajax'=>"false",'data-rel'=>'external'));
						
					}else{
						
						echo $this->Html->link('次へ',
							'#suggestion', array('data-theme'=>'b','data-role'=>'button','data-ajax'=>'false', 'id'=>'next'));
					}
					// <a href="#suggestion" data-theme='b' data-role='button' data-ajax='false' id="next">次へ</a>
				
				?>
					
				</div>
				<div class="ui-block-b">
				<?php
					echo $this->Html->link('ｷｬﾝｾﾙ',
						array('action'=>'index'),array('data-role'=>'button','data-ajax'=>"false",'data-rel'=>'external'));
				?>							
				</div>
			</fieldset>
			<?php echo $this->Form->end(); ?>
			
			<?php
				if(count($question['Answer'])){
					
					echo "<p>他の人たちはこの質問に以下のように言っています。クリックすると回答をコピーします。</p>\n";
					echo "<div class='ws-normal'>\n";
					foreach($question['Answer'] as $answer ){
					
						if( $answer['user_id'] == $user_data['User']['id'] ){
							continue;	// 自分の回答はいらないよ
						}
						
						echo $this->Form->button($answer['content'],array('data-theme'=>'c',
							'onClick'=>"$('#answer').val('${answer['content']}');"));
						
					}
					echo "</div>\n";

				}

			?>

			</div>

		</div>

		<div data-role="dialog" id="suggestion">
		
			<div data-role="header">
				<h1>インタビュー</h1>
			</div><!-- /header -->
		
			<div data-role="content">

				<h3>これを挑戦としてみてはどうでしょう？</h3>
				<?php
					echo $this->Form->create('Will', array('data-ajax'=>'false'));
					echo $this->Form->hidden("Answer.question_id", array('value'=>$question['Question']['id'],'id'=>"ans_qid"));
					echo $this->Form->hidden("Answer.content", array('id'=>"ans_content"));
					echo $this->Form->hidden("Answer.user_id",array('value'=>$user_data['User']['id']));
					echo $this->Form->input('title', array('label'=>false,'id'=>'new_will' ));
					
					echo "<fieldset class='ui-grid-a'>\n";
					echo $this->Form->submit('はい',array('data-theme'=>'b','div'=>'ui-block-a','data-ajax'=>"false",'data-rel'=>'external'));
					echo $this->Form->submit('ｷｬﾝｾﾙ',array('data-theme'=>'c','id'=>'cancel','div'=>'ui-block-b','data-ajax'=>"false",'data-rel'=>'external'));
					echo "</fieldset>\n";
					echo $this->Form->end();
				?>
			</div>
		</div>

</div>

<?php

	}else{

	// インタビューがない場合
?>

		<div data-role="dialog" id="question">
		
			<div data-role="header">
				<h1>インタビュー</h1>
			</div><!-- /header -->
		
			<div data-role="content">

			<h3>全てのインタビューに答えてしまったようです！</h3>			
			<?php
				echo $this->Html->link('OK',
					array('action'=>'index'),array('data-role'=>'button','data-ajax'=>"false",'data-rel'=>'external'));
			?>							

			</div>

		</div>

<?php
	}
endif;
?>

	