<section class="page__main page__main--popular">
    <div class="container">
        <h1 class="page__title page__title--popular">Популярное</h1>
    </div>
    
    <div class="popular container">
        <div class="popular__filters-wrapper">
            <div class="popular__sorting sorting">
                <b class="popular__sorting-caption sorting__caption">Сортировка:</b>
                <ul class="popular__sorting-list sorting__list">
                    <?php foreach (SORTING as $key => $sorting_item): ?>  
                    <li class="sorting__item<?= $key === $sort_chosen ? ' sorting__item--popular' : ''; ?>">
                        <a class="sorting__link<?= $key === $sort_chosen ? ' sorting__link--active' : ''; ?>" href="/?sort_by=<?= $key . '&categ_chosen=' . $categ_chosen ?>">
                            <span><?= $sorting_item; ?></span>
                            <svg class="sorting__icon" width="10" height="12">
                                <use xlink:href="#icon-sort"></use>
                            </svg>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="popular__filters filters">
                <b class="popular__filters-caption filters__caption">Тип контента:</b>
                <ul class="popular__filters-list filters__list">
                    <li class="popular__filters-item filters__item filters__item--all popular__filters-item--all">
                        <a class="filters__button filters__button--ellipse filters__button--all<?= $categ_chosen === 0 ? ' filters__button--active' : ''; ?>" href="/">
                            <span>Все</span>
                        </a>
                    </li>
                    <?php foreach ($categories as $categ): ?>    
                    <li class="popular__filters-item filters__item">
                     	<a class="filters__button filters__button--<?= $categ['category']; ?> button <?= $categ['id'] == $categ_chosen ? ' filters__button--active' : ''; ?>" href="/?categ_chosen=<?= $categ['id'] . '&sort_by=' . $sort_chosen ?>">
                     		<span class="visually-hidden"><?= $categ['category_name']; ?></span>
                     		<svg class="filters__icon" width="22" height="18">
                                <use xlink:href="#icon-filter-<?= $categ['category']; ?>"></use>
                            </svg>
                     	</a>
                     </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="popular__posts">
    		<?php foreach ($posts as $post): 
    		      $href = '/post.php?post_id=' . $post['id'];
    		?>         
            <article class="popular__post post post-<?= $post['category']; ?>">
                <header class="post__header">
                    <h2><a href="<?= $href; ?>"><?= $post['p_title']; ?></a></h2>
                </header>
                <div class="post__main">
                  
	    		<?php switch ("post-" . $post['category']):
    	    	
                        case 'post-photo': ?>
                    <div class="post-photo__image-wrapper">
                        <img src="uploads/<?= $post['p_img']; ?>" alt="Фото от пользователя" width="360" height="240">
                    </div>
                    	<?php break;
                                       	
                        case 'post-video': ?>
                    <div class="post-video__block">
                      <div class="post-video__preview">
                          <?= embed_youtube_cover($post['p_video']); ?>
                      </div>
                      <a href="<?= $href; ?>" class="post-video__play-big button">
                          <svg class="post-video__play-big-icon" width="14" height="14">
                              <use xlink:href="#icon-video-play-big"></use>
                          </svg>
                          <span class="visually-hidden">Запустить проигрыватель</span>
                      </a>
                    </div>
                		<?php break;
                		
                        case 'post-text': ?>
                  	<p><?php echo cut_text($post['p_content'], $href); ?></p>
                    	<?php break;
                    	
                          case 'post-quote': ?>
                    <blockquote>
                    	<p><?= $post['p_content']; ?></p>
                    	<cite><?= $post['author']; ?></cite>
                    </blockquote>
          	    		<?php break;
          	    		
                          case 'post-link': ?>
                    <div class="post-link__wrapper">
                    	<a class="post-link__external" href="<?= $post['p_link']; ?>" title="Перейти по ссылке">
                        	<div class="post-link__info-wrapper">
                            	<div class="post-link__icon-wrapper">
                                	<img src="https://www.google.com/s2/favicons?domain=<?= $post['p_link']; ?>" alt="Иконка">  
                                </div>
                                <div class="post-link__info">
                                	<h3><?= $post['p_title']; ?></h3>
                                </div>
                            </div>
                            <span><?= $post['p_content']; ?></span>
                        </a>
                    </div>
                    
                    	<?php break; 
                endswitch; ?>
                
                </div> 
                <footer class="post__footer">
                    <div class="post__author">
                        <a class="post__author-link" href="#" title="Автор">
                            <div class="post__avatar-wrapper">
                                <!--укажите путь к файлу аватара-->
                                <img class="post__author-avatar" src="img/<?= $post['avatar']; ?>" alt="Аватар пользователя">
                            </div>
                            <div class="post__info">
                                <b class="post__author-name"><?= $post['login']; ?></b>
                                <time class="post__time" title="<?= $post['date_title']; ?>" datetime="<?= $post['dt_add']; ?>"><?= $post['date_interval']; ?></time>
                            </div>
                        </a>
                    </div>
                    <div class="post__indicators">
                        <div class="post__buttons">
                            <a class="post__indicator post__indicator--likes button" href="#" title="Лайк">
                                <svg class="post__indicator-icon" width="20" height="17">
                                    <use xlink:href="#icon-heart"></use>
                                </svg>
                                <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                                    <use xlink:href="#icon-heart-active"></use>
                                </svg>
                                <span><?= $post['likes_count']; ?></span>
                                <span class="visually-hidden">количество лайков</span>
                            </a>
                            <a class="post__indicator post__indicator--comments button" href="#" title="Комментарии">
                                <svg class="post__indicator-icon" width="19" height="17">
                                    <use xlink:href="#icon-comment"></use>
                                </svg>
                                <span><?= $post['comments_count']; ?></span>
                                <span class="visually-hidden">количество комментариев</span>
                            </a>
                        </div>
                    </div>
                </footer>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
