<main class="page__main page__main--adding-post">
	<div class="page__main-section">
		<div class="container">
			<h1 class="page__title page__title--adding-post">Добавить публикацию</h1>
		</div>
		<div class="adding-post container">
			<div class="adding-post__tabs-wrapper tabs">
				<div class="adding-post__tabs filters">
					<ul class="adding-post__tabs-list filters__list tabs__list">
                    	<?php foreach ($post_types as $post_type): ?>    
                        <li class="adding-post__tabs-item filters__item">
                         	<a class="adding-post__tabs-link tabs__item filters__button  filters__button--<?= $post_type['category']; ?> 
                         		button <?= $post_type['id'] == $post_type_chosen ? ' filters__button--active tabs__item--active' : ''; ?>" href="?post_type_chosen=<?= $post_type['id']; ?>">
                         		<svg class="filters__icon" width=<?= $post_type['category_w'] . ' height=' . $post_type['category_h']; ?>>
                                    <use xlink:href="#icon-filter-<?= $post_type['category']; ?>"></use>
                                </svg> <span><?= $post_type['category_name']; ?></span>
                         	</a>
                         </li>
                         <?php endforeach; ?>
					</ul>
				</div>
				
				<div class="adding-post__tab-content">  
					<?php foreach ($post_types as $post_type): ?>  
					<?php switch ($post_type['id']):
    	    	
                            case ($post_type['id'] == '3'): ?>
					<section
						class="adding-post__photo tabs__content<?= $post_type['id'] == $post_type_chosen ? ' tabs__content--active' : ''; ?>">
						<h2 class="visually-hidden">Форма добавления фото</h2>
						<form class="adding-post__form form" action="add.php" method="post"
							enctype="multipart/form-data">
							<div class="form__text-inputs-wrapper">
								<div class="form__text-inputs">
									<?php require 'form-title.php';?>
									<div class="adding-post__input-wrapper form__input-wrapper">
										<label class="adding-post__label form__label" for="photo-url">Ссылка
											из интернета</label>
										<div class="form__input-section">
											<input class="adding-post__input form__input" id="photo-url"
												type="text" name="photo-url"
												placeholder="Введите ссылку">
											<?php require 'form-error.php';?>
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
											id="userpic-file-photo" type="file" name="userpic-file-photo"
											title=" ">
										
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
							<input type="hidden" name="post-type" value="3">
						</form>
					</section>
        					<?php break;
                        		
                            case ($post_type['id'] == '4'): ?>
					<section class="adding-post__video tabs__content<?= $post_type['id'] == $post_type_chosen ? ' tabs__content--active' : ''; ?>">
						<h2 class="visually-hidden">Форма добавления видео</h2>
						<form class="adding-post__form form" action="add.php" method="post"
							enctype="multipart/form-data">
							<div class="form__text-inputs-wrapper">
								<div class="form__text-inputs">
									<?php require 'form-title.php';?>
									<div class="adding-post__input-wrapper form__input-wrapper">
										<label class="adding-post__label form__label" for="video-url">Ссылка
											youtube <span class="form__input-required">*</span>
										</label>
										<div class="form__input-section">
											<input class="adding-post__input form__input" id="video-url"
												type="text" name="video-url"
												placeholder="Введите ссылку">											
											<?php require 'form-error.php';?>
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
							<input type="hidden" name="post-type" value="4">
						</form>
					</section>
        					<?php break;
                        		
                            case ($post_type['id'] == '1'): ?>	
					<section class="adding-post__text tabs__content<?= $post_type['id'] == $post_type_chosen ? ' tabs__content--active' : ''; ?>">
						<h2 class="visually-hidden">Форма добавления текста</h2>
						<form class="adding-post__form form" action="add.php" method="post">
							<div class="form__text-inputs-wrapper">
								<div class="form__text-inputs">
									<?php require 'form-title.php';?>
									<div
										class="adding-post__textarea-wrapper form__textarea-wrapper">
										<label class="adding-post__label form__label" for="post-text">Текст
											поста <span class="form__input-required">*</span>
										</label>
										<div class="form__input-section <?= isset($errors['post-text']) ? ' form__input-section--error' : ''; ?>">
											<textarea name="post-text"
												class="adding-post__textarea form__textarea form__input"
												id="post-text" placeholder="Введите текст публикации"><?= getPostVal('post-text'); ?></textarea>
											<button class="form__error-button button" type="button">
                                            	!<span class="visually-hidden">Информация об ошибке</span>
                                            </button>
                                            <div class="form__error-text">
                                            	<h3 class="form__error-title">Заголовок сообщения</h3>
                                            	<p class="form__error-desc"><?= $errors['post-text']; ?></p>
                                            </div>
										</div>
									</div>
									<?php require 'form-tags.php';?>
								</div>
								<?php require 'form-invalid-block.php';?>
							</div>
							<input type="hidden" name="post-type" value="1">
							<div class="adding-post__buttons">
								<button class="adding-post__submit button button--main"
									type="submit">Опубликовать</button>
								<a class="adding-post__close" href="#">Закрыть</a>
							</div>
						</form>
					</section>
        					<?php break;
                        		
                            case ($post_type['id'] == '2'): ?>
					<section class="adding-post__quote tabs__content <?= $post_type['id'] == $post_type_chosen ? ' tabs__content--active' : ''; ?>">
						<h2 class="visually-hidden">Форма добавления цитаты</h2>
						<form class="adding-post__form form" action="add.php" method="post">
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
                                            	<h3 class="form__error-title">Заголовок сообщения</h3>
                                            	<p class="form__error-desc">Текст сообщения об ошибке,
                                            		подробно объясняющий, что не так.</p>
                                            </div>
										</div>
									</div>
									<div class="adding-post__textarea-wrapper form__input-wrapper">
										<label class="adding-post__label form__label"
											for="quote-author">Автор <span class="form__input-required">*</span></label>
										<div class="form__input-section">
											<input class="adding-post__input form__input"
												id="quote-author" type="text" name="quote-author" value="<?= getPostVal('quote-author'); ?>">
											<?php require 'form-error.php';?>
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
							<input type="hidden" name="post-type" value="2">
						</form>
					</section>
        					<?php break;
                        		
                            case ($post_type['id'] == '5'): ?>
					<section class="adding-post__link tabs__content <?= $post_type['id'] == $post_type_chosen ? ' tabs__content--active' : ''; ?>">
						<h2 class="visually-hidden">Форма добавления ссылки</h2>
						<form class="adding-post__form form" action="add.php" method="post">
							<div class="form__text-inputs-wrapper">
								<div class="form__text-inputs">
									<?php require 'form-title.php';?>
									<div class="adding-post__textarea-wrapper form__input-wrapper">
										<label class="adding-post__label form__label" for="post-link">Ссылка
											<span class="form__input-required">*</span>
										</label>
										<div class="form__input-section">
											<input class="adding-post__input form__input" id="post-link"
												type="text" name="post-link">
											<?php require 'form-error.php';?>
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
							<input type="hidden" name="post-type" value="5">
						</form>
					</section>
					<?php break; 
                endswitch;
                endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</main>