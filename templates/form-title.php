<div class="adding-post__input-wrapper form__input-wrapper">
	<label class="adding-post__label form__label"
		for="heading">Заголовок <span
		class="form__input-required">*</span></label>
	<div class="form__input-section<?= isset($errors['heading']) ? ' form__input-section--error' : ''; ?>">
		<input class="adding-post__input form__input"
			id="heading" type="text"
			name="heading"
			placeholder="Введите заголовок"
			value="<?= getPostVal('heading'); ?>">
		<?php 
		    $error_type = 'heading';
		    require 'form-error.php';
		?>
	</div>
</div>
