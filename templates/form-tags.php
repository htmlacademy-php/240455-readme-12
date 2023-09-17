<div class="adding-post__input-wrapper form__input-wrapper">
	<label class="adding-post__label form__label" for="<?= $post_type['category']; ?>-tags">Теги</label>
	<div class="form__input-section">
		<input class="adding-post__input form__input" id="<?= $post_type['category']; ?>-tags"
			type="text" name="<?= $post_type['category']; ?>-tags" placeholder="Введите теги" value="<?=getPostVal($post_type['category'] . '-tags'); ?>">
		<?php require 'form-error.php';?>
	</div>
</div>


<!-- Валидация поля «Теги» -->

<!-- В этом поле пользователь вводит теги, к которым относится публикация. Теги разделяются пробелом. Выполняя валидацию, 
нужно убедиться, что в поле одно или больше слов, а сами слова разделены пробелом. Каждый тег состоит только из одного слова. -->
<!-- Привязка тегов к публикации -->

<!-- Информацию из поля «Теги» надо разделить на отдельные теги-слова. Эти теги сохраняются в отдельной таблице и ссылаются на запись 
из таблицы постов (подробности смотри в разделе «Сущности»). -->
<!-- . Если первый символ является решёткой — «#», то это означает поиск по тегу. -->