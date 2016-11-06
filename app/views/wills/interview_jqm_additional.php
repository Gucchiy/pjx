<!-- Start of second page -->
<?php
$question_count = count($questions);
	
for( $i=1; $i<=$question_count; $i++ ){

	$question = $questions[1]['Question'];
?>

<div data-role="dialog" id="question<?=$i?>">

	<div data-role="header">
		<h1>インタビュー(<?=$i+1?>/<?=$question_count+1?>)</h1>
	</div><!-- /header -->

	<div data-role="content">
		
	
<?php
		if( $i < $question_count ){
?>
			<h3><?=$question['question']?></h3>	
	
			<fieldset class="ui-grid-a">
			<div class="ui-block-b"><a href="#question2" data-theme='b' data-role='button'>次へ</a></div>
<?php
		}else{
?>

			<h3>これを挑戦にしますか？</h3>	
	
			<fieldset class="ui-grid-a">

<?php
			echo $this->Form->submit('はい',array('data-theme'=>'b','div'=>'ui-block-a'));
		
		}
?>
			<div class="ui-block-b"><a href="index" data-theme='c' data-role='button'>キャンセル</a></div>
		</fieldset>

	</div><!-- /content -->

</div><!-- /page -->

<?php
}
?>