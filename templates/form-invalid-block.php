<?php if ($errors) { ?>
<div class="form__invalid-block">
	<b class="form__invalid-slogan">Пожалуйста, исправьте следующие
		ошибки:</b>
	<ul class="form__invalid-list">
		<?php if (isset($errors['heading'])) { ?>
		<li class="form__invalid-item"><?= $errors['heading']['head']; ?>. <?= $errors['heading']['description']; ?></li>
		<?php } ?>
		<?php if (isset($errors['post-text'])) { ?>
		<li class="form__invalid-item"><?= $errors['post-text']['head']; ?>. <?= $errors['post-text']['description']; ?></li>
		<?php } ?>
		<?php if (isset($errors['cite-text'])) { ?>
		<li class="form__invalid-item"><?= $errors['cite-text']['head']; ?>. <?= $errors['cite-text']['description']; ?></li>
		<?php } ?>
		<?php if (isset($errors['quote-author'])) { ?>
		<li class="form__invalid-item"><?= $errors['quote-author']['head']; ?>. <?= $errors['quote-author']['description']; ?></li>
		<?php } ?>
		<?php if (isset($errors['post-link'])) { ?>
		<li class="form__invalid-item"><?= $errors['post-link']['head']; ?>. <?= $errors['post-link']['description']; ?></li>
		<?php } ?>
		<?php if (isset($errors['video-url'])) { ?>
		<li class="form__invalid-item"><?= $errors['video-url']['head']; ?>. <?= $errors['video-url']['description']; ?></li>
		<?php } ?>
		<?php if (isset($errors['file'])) { ?>
		<li class="form__invalid-item"><?= $errors['file']['head']; ?>. <?= $errors['file']['description']; ?></li>
		<?php } ?>
	</ul>
</div>
<?php } ?>