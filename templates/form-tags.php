<div class="adding-post__input-wrapper form__input-wrapper">
	<label class="adding-post__label form__label" for="<?= $categ['category']; ?>-tags">Теги</label>
	<div class="form__input-section">
		<input class="adding-post__input form__input" id="<?= $categ['category']; ?>-tags"
			type="text" name="<?= $categ['category']; ?>-heading" placeholder="Введите теги">
		<?php require 'form-error.php';?>
	</div>
</div>