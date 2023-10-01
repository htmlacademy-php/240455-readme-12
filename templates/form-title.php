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
		<button class="form__error-button button" type="button">
			!<span class="visually-hidden">Информация об ошибке</span>
		</button>
		<div class="form__error-text">
			<h3 class="form__error-title">Заголовок сообщения</h3>
			<p class="form__error-desc"><?= $errors['heading']; ?></p>
		</div>
	</div>
</div>

<!-- Если проверка формы выявила ошибки, то сделать следующее: -->

<!-- для всех полей формы, где найдены ошибки: -->
<!-- добавить контейнеру с этим полем класс form__input-section--error; -->
<!-- в тег div.form__error-text этого контейнера записать текст ошибки внутри параграфа с классом form__error-desc. Например: «Заполните это поле»; -->
<!-- весь список ошибок надо вывести в блоке div.form__invalid-block (см. пример из вёрстки). -->