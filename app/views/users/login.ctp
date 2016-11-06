<?php
if  ($session->check('Message.auth')) $session->flash('auth');
echo $form->create('User', array('action' => 'login'));
echo $form->input('email');
echo $form->input('password');
?>

<br />
<input type="checkbox" name="data[User][remember_me]" id="UserRememberMe" />
<label for="UserRememberMe">次回から自動的にログイン</label>

<?php
// echo $form->checkbox('remember_me')."次回から自動的にログイン";
echo $form->end('Login');
echo $this->Html->link(__('New User', true), array('action' => 'add'));
?>