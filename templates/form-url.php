<div class="form__input-section<?= isset($errors['url']) ? ' form__input-section--error' : ''; ?>">
	<input class="adding-post__input form__input"
		type="text" name="url"
		placeholder="Введите ссылку" value="<?= getPostVal('url'); ?>">											
	<?php 
	    $error_type = 'url';
	    require 'form-error.php';
    ?>
</div>