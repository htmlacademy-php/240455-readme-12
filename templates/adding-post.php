<main class="page__main page__main--adding-post">
	<div class="page__main-section">
		<div class="container">
			<h1 class="page__title page__title--adding-post">Добавить публикацию</h1>
		</div>
		<div class="adding-post container">
			<div class="adding-post__tabs-wrapper tabs">
				<div class="adding-post__tabs filters">
					<ul class="adding-post__tabs-list filters__list tabs__list">
                    	<?php foreach ($categories as $category): ?>    
                    	<?php echo $category['category']; ?>
                        <li class="adding-post__tabs-item filters__item">
                         	<a class="adding-post__tabs-link tabs__item filters__button  filters__button--<?= $category['category']; ?> 
                         		button<?= $category['category'] == $category_chosen ? ' filters__button--active tabs__item--active' : ''; ?>" href="?category_chosen=<?= $category['category']; ?>">
                         		<svg class="filters__icon" width=<?= $category['category_w'] . ' height=' . $category['category_h']; ?>>
                                    <use xlink:href="#icon-filter-<?= $category['category']; ?>"></use>
                                </svg> <span><?= $category['category_name']; ?></span>
                         	</a>
                         </li>
                         <?php endforeach; ?>
					</ul>
				</div>
				<div class="adding-post__tab-content">  
					<section
						class="adding-post__photo tabs__content<?= $category_chosen === 'photo' ? ' tabs__content--active' : ''; ?>">
						<h2 class="visually-hidden">Форма добавления фото</h2>
						<form class="adding-post__form form" action="add.php?category_chosen=photo" method="post"
							enctype="multipart/form-data">
							<div class="form__text-inputs-wrapper">
								<div class="form__text-inputs">
									<?php require 'form-title.php';?>
									<div class="adding-post__input-wrapper form__input-wrapper">
										<label class="adding-post__label form__label" for="photo-url">Ссылка
											из интернета</label>
										<div class="form__input-section<?= isset($errors['file']) ? ' form__input-section--error' : ''; ?>">
											<input class="adding-post__input form__input" id="photo-url"
												type="text" name="photo-url" placeholder="Введите ссылку" value="<?= getPostVal('photo-url'); ?>">
											<button class="form__error-button button" type="button">
                                            	!<span class="visually-hidden">Информация об ошибке</span>
                                            </button>
                                            <div class="form__error-text">
                                            	<h3 class="form__error-title"><?= $errors['file']['head']; ?></h3>
                                            	<p class="form__error-desc"><?= $errors['file']['description']; ?></p>
                                            </div>
										</div>
									</div>
									<?php require 'form-tags.php';?>
								</div>
								<?php require 'form-invalid-block.php';?>
							</div>		
							<div
								class="adding-post__input-file-container form__input-container form__input-container--file">
								<div
									class="adding-post__input-file-wrapper form__input-file-wrapper">
									<div
										class="adding-post__file-zone adding-post__file-zone--photo form__file-zone dropzone">
										<input class="adding-post__input-file form__input-file"
											id="userpic-file-photo" type="file" name="userpic-file-photo">
									</div>
									<button
										class="adding-post__input-file-button form__input-file-button form__input-file-button--photo button"
										type="button">
										<span>Выбрать фото</span>
										<svg class="adding-post__attach-icon form__attach-icon"
											width="10" height="20">
                      <use xlink:href="#icon-attach"></use>
                    </svg>
									</button>
								</div>
								<div
									class="adding-post__file adding-post__file--photo form__file dropzone-previews">

								</div>
							</div>
							<div class="adding-post__buttons">
								<button class="adding-post__submit button button--main"
									type="submit">Опубликовать</button>
								<a class="adding-post__close" href="#">Закрыть</a>
							</div>
							<input type="hidden" name="category" value="3">
						</form>
					</section>
					<section class="adding-post__video tabs__content<?= $category_chosen === 'video' ? ' tabs__content--active' : ''; ?>">
						<h2 class="visually-hidden">Форма добавления видео</h2>
						<form class="adding-post__form form" action="add.php?category_chosen=video" method="post"
							enctype="multipart/form-data">
							<div class="form__text-inputs-wrapper">
								<div class="form__text-inputs">
									<?php require 'form-title.php';?>
									<div class="adding-post__input-wrapper form__input-wrapper">
										<label class="adding-post__label form__label" for="video-url">Ссылка
											youtube <span class="form__input-required">*</span>
										</label>
										<div class="form__input-section<?= isset($errors['video-url']) ? ' form__input-section--error' : ''; ?>">
											<input class="adding-post__input form__input" id="video-url"
												type="text" name="video-url"
												placeholder="Введите ссылку" value="<?= getPostVal('video-url'); ?>">											
											<button class="form__error-button button" type="button">
                                            	!<span class="visually-hidden">Информация об ошибке</span>
                                            </button>
                                            <div class="form__error-text">
                                            	<h3 class="form__error-title"><?= $errors['video-url']['head']; ?></h3>
                                            	<p class="form__error-desc"><?= $errors['video-url']['description']; ?></p>
                                            </div>
										</div>
									</div>
									<?php require 'form-tags.php';?>
								</div>
								<?php require 'form-invalid-block.php';?>
							</div>

							<div class="adding-post__buttons">
								<button class="adding-post__submit button button--main"
									type="submit">Опубликовать</button>
								<a class="adding-post__close" href="#">Закрыть</a>
							</div>
							<input type="hidden" name="category" value="4">
						</form>
					</section>
					<section class="adding-post__text tabs__content<?= $category_chosen === 'text' ? ' tabs__content--active' : ''; ?>">
						<h2 class="visually-hidden">Форма добавления текста</h2>
						<form class="adding-post__form form" action="add.php?category_chosen=text" method="post">
							<div class="form__text-inputs-wrapper">
								<div class="form__text-inputs">
									<?php require 'form-title.php';?>
									<div
										class="adding-post__textarea-wrapper form__textarea-wrapper">
										<label class="adding-post__label form__label" for="post-text">Текст
											поста <span class="form__input-required">*</span>
										</label>
										<div class="form__input-section<?= isset($errors['post-text']) ? ' form__input-section--error' : ''; ?>">
											<textarea name="post-text"
												class="adding-post__textarea form__textarea form__input"
												id="post-text" placeholder="Введите текст публикации"><?= getPostVal('post-text'); ?></textarea>
											<button class="form__error-button button" type="button">
                                            	!<span class="visually-hidden">Информация об ошибке</span>
                                            </button>
                                            <div class="form__error-text">
                                            	<h3 class="form__error-title"><?= $errors['post-text']['head']; ?></h3>
                                            	<p class="form__error-desc"><?= $errors['post-text']['description']; ?></p>
                                            </div>
										</div>
									</div>
									<?php require 'form-tags.php';?>
								</div>
								<?php require 'form-invalid-block.php';?>
							</div>
							<input type="hidden" name="category" value="1">
							<div class="adding-post__buttons">
								<button class="adding-post__submit button button--main"
									type="submit">Опубликовать</button>
								<a class="adding-post__close" href="#">Закрыть</a>
							</div>
						</form>
					</section>
					<section class="adding-post__quote tabs__content <?= $category_chosen === 'quote' ? ' tabs__content--active' : ''; ?>">
						<h2 class="visually-hidden">Форма добавления цитаты</h2>
						<form class="adding-post__form form" action="add.php?category_chosen=quote" method="post">
							<div class="form__text-inputs-wrapper">
								<div class="form__text-inputs">
									<?php require 'form-title.php';?>
									<div class="adding-post__input-wrapper form__textarea-wrapper">
										<label class="adding-post__label form__label" for="cite-text">Текст
											цитаты <span class="form__input-required">*</span>
										</label>
										<div class="form__input-section<?= isset($errors['cite-text']) ? ' form__input-section--error' : ''; ?>">
											<textarea
												class="adding-post__textarea adding-post__textarea--quote form__textarea form__input"
												id="cite-text" placeholder="Текст цитаты" name="cite-text"><?= getPostVal('cite-text'); ?></textarea>
											<button class="form__error-button button" type="button">
                                            	!<span class="visually-hidden">Информация об ошибке</span>
                                            </button>
                                            <div class="form__error-text">
                                            	<h3 class="form__error-title"><?= $errors['cite-text']['head']; ?></h3>
                                            	<p class="form__error-desc"><?= $errors['cite-text']['description']; ?></p>
                                            </div>
										</div>
									</div>
									<div class="adding-post__textarea-wrapper form__input-wrapper">
										<label class="adding-post__label form__label"
											for="quote-author">Автор <span class="form__input-required">*</span></label>
										<div class="form__input-section<?= isset($errors['quote-author']) ? ' form__input-section--error' : ''; ?>">
											<input class="adding-post__input form__input"
												id="quote-author" type="text" name="quote-author" value="<?= getPostVal('quote-author'); ?>">
											<button class="form__error-button button" type="button">
                                            	!<span class="visually-hidden">Информация об ошибке</span>
                                            </button>
                                            <div class="form__error-text">
                                            	<h3 class="form__error-title"><?= $errors['quote-author']['head']; ?></h3>
                                            	<p class="form__error-desc"><?= $errors['quote-author']['description']; ?></p>
                                            </div>
										</div>
									</div>
									<?php require 'form-tags.php';?>
								</div>
								<?php require 'form-invalid-block.php';?>
							</div>
							<div class="adding-post__buttons">
								<button class="adding-post__submit button button--main"
									type="submit">Опубликовать</button>
								<a class="adding-post__close" href="#">Закрыть</a>
							</div>
							<input type="hidden" name="category" value="2">
						</form>
					</section>
					<section class="adding-post__link tabs__content <?= $category_chosen === 'link' ? ' tabs__content--active' : ''; ?>">
						<h2 class="visually-hidden">Форма добавления ссылки</h2>
						<form class="adding-post__form form" action="add.php?category_chosen=link" method="post">
							<div class="form__text-inputs-wrapper">
								<div class="form__text-inputs">
									<?php require 'form-title.php';?>
									<div class="adding-post__textarea-wrapper form__input-wrapper">
										<label class="adding-post__label form__label" for="post-link">Ссылка
											<span class="form__input-required">*</span>
										</label>
										<div class="form__input-section<?= isset($errors['post-link']) ? ' form__input-section--error' : ''; ?>">
											<input class="adding-post__input form__input" id="post-link"
												type="text" name="post-link" value="<?= getPostVal('post-link'); ?>">
											<button class="form__error-button button" type="button">
                                            	!<span class="visually-hidden">Информация об ошибке</span>
                                            </button>
                                            <div class="form__error-text">
                                            	<h3 class="form__error-title"><?= $errors['post-link']['head']; ?></h3>
                                            	<p class="form__error-desc"><?= $errors['post-link']['description']; ?></p>
                                            </div>
										</div>
									</div>
									<?php require 'form-tags.php';?>
								</div>
								<?php require 'form-invalid-block.php';?>
							</div>
							<div class="adding-post__buttons">
								<button class="adding-post__submit button button--main"
									type="submit">Опубликовать</button>
								<a class="adding-post__close" href="#">Закрыть</a>
							</div>
							<input type="hidden" name="category" value="5">
						</form>
					</section>
				</div>
			</div>
		</div>
	</div>
</main>