<div class="adding-post__input-wrapper form__input-wrapper">
	<label class="adding-post__label form__label" for="<?= $post_type['category']; ?>-tags">Теги</label>
	<div class="form__input-section">
		<input class="adding-post__input form__input" id="<?= $post_type['category']; ?>-tags"
			type="text" name="<?= $post_type['category']; ?>-tags" placeholder="Введите теги" value="<?=getPostVal($post_type['category'] . '-tags'); ?>">
		<?php require 'form-error.php';?>
	</div>
</div>