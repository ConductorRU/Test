<?php
/* @var $this \yii\web\View */
/* @var $content string */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use common\models\Settings;
use common\models\Auth;
use common\models\User;

AppAsset::register($this);
$sets = Settings::getValues();
$preload_show = isset($sets['preload_show']) ? (int)$sets['preload_show'] : 1;
$user = Yii::$app->user->identity;
$userPhoto = '';
$notes = [];
if($user)
{
	$userPhoto = $user->GetPhotoUrl();
	if(substr_count(Url::to(), '/account'))
		$notes = $user->GetNotifyes();
}
$ggAuth = Settings::GetValue('google_id');
$vkAuth = Auth::GetVkRef();
$vkLogout = Auth::GetVkLogoutRef();
$mess = Yii::$app->session->getFlash('message');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="/favicon.ico" type="image/x-icon">
	<link rel="icon" type="image/png" href="/img/logo-16.png" sizes="16x16">
	<link rel="icon" type="image/png" href="/img/logo-32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="/img/logo-48.png" sizes="48x48">
	<?= Html::csrfMetaTags() ?>
	<title><?= Html::encode($this->title) ?></title>
	<?php $this->head() ?>
	<?= Settings::getValue('code_head'); ?>
	<style type="text/css">
		#preload {display:block;position:fixed;top:0;left:0;right:0;bottom:0;background:#2F292B;	z-index: 1100;}
		#preload .back > div > div, #preload .back > div > div > div {height:0;transition:ease height 800ms;}
		#preload .centre {text-align:center; position: absolute;top: 50%;left: 0;right: 0;transform: translateY(-50%);}
		#preload .centreB {position:relative;display:inline-block;}
		#preload .centreB > div {display:inline-block;}
		#preload .loadbar {position:absolute;top:0;left:0;width:0;overflow:hidden;}
  </style>
</head>
<body>
<?= Settings::getValue('code_body_begin'); ?>
	<?php $this->beginBody() ?>
	<?php if($preload_show && (!isset($_COOKIE['Preload']) || $preload_show == 2)):
		echo $this->render('_preload');
		endif;
		SetCookie('Preload', 1, time() + 60*60*24, '/');
		$q = isset($_GET['q']) ? trim($_GET['q']) : '';
	?>
	<div class="wrap">
		<header>
			<div class="container">
				<div class="headBlock">
					<div class="head">
						<button class="menu bt-menu-trigger"><span></span></button>
						<a class="logo" href="/"><img src="/img/logo_full.svg" alt="" /></a>
						<div class="search <?= ($q != '') ? 'active' : '' ?>">
							<input type="text" placeholder="Введите Ваш запрос" value="<?= htmlspecialchars($q) ?>" />
							<span><img class="svg" src="/img/search.svg" alt="Поиск" /></span>
						</div>
						<button class="user">
							<?php if($userPhoto): ?><span style="background-image:url('<?= $userPhoto ?>');"></span>
							<?php else: ?><img src="/img/user.svg" alt="" /><?php endif ?>
						</button>
						<div id="headPopup">
							<ul class="ulClear nosel">
							<?php if($user): ?>
								<?php if(count($notes)): ?><li class="ref"><a href="/account">Уведомления<b><?= count($notes) ?></b></a></li><?php endif ?>
								<li class="ref"><a href="/account">Личный кабинет</a></li>
								<?php if($user->role == 1 || $user->role == 2): ?><li class="ref"><a href="/admin_fr20ee19bie">Администрирование</a></li><?php endif ?>
								<li class="ref"><a href="/logout" onclick="main.Logout('<?= $ggAuth ?>', '<?= $vkLogout ?>');">Выйти</a></li>
							<?php else: ?>
								<li data-toggle="modal" data-target="#modalLogin">Войти</li>
								<li data-toggle="modal" data-target="#modalReg">Регистрация</li>
								<?php if($vkAuth): ?><li class="vk" onclick="main.AuthOpen(this, '<?= $vkAuth ?>');"><span><img class="svg" src="/img/vk.svg" alt="" /></span>Войти через VK</li><?php endif ?>
								<?php if($ggAuth): ?><li class="google" onclick="main.GoogleAuthOpen(this, '<?= $ggAuth ?>');"><span><img class="svg" src="/img/google.svg" alt="" /></span>Войти через Google</li><?php endif ?>
							<?php endif ?>
							</ul>
						</div>
						<?php if($user && count($notes)): ?>
						<div class="notifyes">
							<?php foreach($notes as $note): ?>
							<div class="note">
								<?php if($note->status == 1): ?><div>Срок действия на <a href="/subscription">Подписку</a> истекает!</div><?php
								elseif($note->status == 2): ?><div>Срок действия на <a href="/subscription">Подписку</a> истёк!</div><?php
								elseif($note->status == 3): ?><div>Срок действия на пакет <a href="#"><b>Акцию</b> Инди игры на РС</a> истекает!</div><?php endif; ?>
								<i class="but" data-id="<?= $note->id ?>"><img src="/img/close_note.svg" alt="" /></i>
							</div>
							<?php endforeach ?>
						</div>
						<?php endif ?>
					</div>
					<div id="menu">
						<div class="row">
							<div class="col-xm-5 col-xs-16">
								<div class="menus">
									<nav>
										<ul class="ulClear">
											<li><a href="/">Главная</a></li>
											<li><a href="/catalog">Каталог</a></li>
											<li><a href="/about">О компании</a></li>
											<li><a href="/subscription">Месячная подписка</a></li>
											<li><a href="/faq">Как это работает?</a></li>
										</ul>
									</nav>
								</div>
								<div class="search">
									<input type="text" placeholder="Введите Ваш запрос" />
									<span><img class="svg" src="/img/search.svg" alt="Поиск" /></span>
								</div>
							</div>
							<div class="col-xm-11 col-xs-16">
								<div class="address">
									<?php if(isset($sets['contact_address'])): ?><div><?= $sets['contact_address'] ?></div><?php endif ?>
									<?php if(isset($sets['contact_email'])): ?><div><?= $sets['contact_email'] ?></div><?php endif ?>
									<?php if(isset($sets['contact_phone'])): ?><div>тел.: <?= $sets['contact_phone'] ?></div><?php endif ?>
								</div>
								<a class="brick" href="https://brickfabrique.ru" target="_blank">
									<span class="img"><img src="/img/brickfabrique.svg" alt="BrickFabrique" /></span>
									<span class="text">made by<br>Brick Fabrique</span>
								</a>
								<div class="mobMenuFooter">
									<div class="social">
										<table class="tSocial">
											<colgroup>
												<col width="76" />
												<col width="76" />
												<col width="76" />
												<col width="76" />
												<col width="76" />
											</colgroup>
											<tr>
												<?php if(isset($sets['social_ig'])): ?><td><a class="ig" href="<?= $sets['social_ig'] ?>"><img src="/img/ig.svg" alt="Instagram" /></a></td><?php endif ?>
												<?php if(isset($sets['social_fb'])): ?><td><a class="fb" href="<?= $sets['social_fb'] ?>"><img src="/img/fb.svg" alt="Facebook" /></a></td><?php endif ?>
												<?php if(isset($sets['social_vk'])): ?><td><a class="vk" href="<?= $sets['social_vk'] ?>"><img src="/img/vk.svg" alt="ВКонтакте" /></a></td><?php endif ?>
												<?php if(isset($sets['social_tg'])): ?><td><a class="tg" href="<?= $sets['social_tg'] ?>"><img src="/img/tg.svg" alt="Telegram" /></a></td><?php endif ?>
												<td class="copy">Freebie <?= date('Y') ?></td>
											</tr>
										</table>
										<div class="copy">Freebie <?= date('Y') ?></div>
									</div>
								</div>
							</div>
						</div>
						<ul class="ulClear social">
							<?php if(isset($sets['social_ig'])): ?><li><a class="ig" href="<?= $sets['social_ig'] ?>"><img src="/img/ig.svg" alt="Instagram" /></a></li><?php endif ?>
							<?php if(isset($sets['social_fb'])): ?><li><a class="fb" href="<?= $sets['social_fb'] ?>"><img src="/img/fb.svg" alt="Facebook" /></a></li><?php endif ?>
							<?php if(isset($sets['social_vk'])): ?><li><a class="vk" href="<?= $sets['social_vk'] ?>"><img src="/img/vk.svg" alt="ВКонтакте" /></a></li><?php endif ?>
							<?php if(isset($sets['social_tg'])): ?><li><a class="tg" href="<?= $sets['social_tg'] ?>"><img src="/img/tg.svg" alt="Telegram" /></a></li><?php endif ?>
						</ul>
					</div>
				</div>
			</div>
		</header>
		<div class="content">
			<div class="back">
				<div class="container"><div><div></div></div><div><div></div></div></div>
			</div>
			<?php if($mess): ?>
				<div class="container" style="position:relative;">
					<div id="mess">
						<div><div><?= $mess ?></div></div>
						<button class="but" onclick="main.CloseMessage();"><img src="/img/cross.svg" alt="" /></button>
					</div>
				</div>
			<?php endif ?>
			<?= $content ?>
		</div>
		<footer class="footer">
			<div class="container">
				<div class="row">
					<div class="col-md-4 col-xs-6">
						<div class="menus">
							<nav>
								<ul class="ulClear">
									<li><a href="/">Главная</a></li>
									<li><a href="/catalog">Каталог</a></li>
									<li><a href="/about">О компании</a></li>
									<li><a href="/subscription">Месячная подписка</a></li>
									<li><a href="/faq">Как это работает?</a></li>
								</ul>
							</nav>
						</div>
					</div>
					<div class="col-md-7 col-xs-10">
						<div class="address">
							<?php if(isset($sets['contact_address'])): ?><div><?= $sets['contact_address'] ?></div><?php endif ?>
							<?php if(isset($sets['contact_email'])): ?><div><?= $sets['contact_email'] ?></div><?php endif ?>
							<?php if(isset($sets['contact_phone'])): ?><div>тел.: <?= $sets['contact_phone'] ?></div><?php endif ?>
						</div>
					</div>
					<div class="col-md-5 col-xs-10">
						<div class="subscribe">
							<div class="title">Подписаться на новостную рассылку:</div>
							<div class="inputBox">
								<input type="text" name="subscribe" placeholder="Введите Ваш E-mail" />
								<button id="subscribeBut" title="Подписаться"><img src="/img/envelope.svg" alt="Подписка" /></button>
								<div class="tips"></div>
							</div>
							<div class="social">
								<table class="tSocial">
									<colgroup>
										<col width="76" />
										<col width="76" />
										<col width="76" />
										<col width="76" />
										<col width="76" />
									</colgroup>
									<tr>
										<?php if(isset($sets['social_ig'])): ?><td><a class="ig" href="<?= $sets['social_ig'] ?>"><img src="/img/ig.svg" alt="Instagram" /></a></td><?php endif ?>
										<?php if(isset($sets['social_fb'])): ?><td><a class="fb" href="<?= $sets['social_fb'] ?>"><img src="/img/fb.svg" alt="Facebook" /></a></td><?php endif ?>
										<?php if(isset($sets['social_vk'])): ?><td><a class="vk" href="<?= $sets['social_vk'] ?>"><img src="/img/vk.svg" alt="ВКонтакте" /></a></td><?php endif ?>
										<?php if(isset($sets['social_tg'])): ?><td><a class="tg" href="<?= $sets['social_tg'] ?>"><img src="/img/tg.svg" alt="Telegram" /></a></td><?php endif ?>
										<td class="copy">Freebie <?= date('Y') ?></td>
									</tr>
								</table>
								<div class="copy">Freebie <?= date('Y') ?></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</footer>
	</div>
	<div id="modalReg" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="head">
					<h4 class="title">Регистрация аккаунта</h4>
					<button type="button" class="cross" data-dismiss="modal"></button>
				</div>
				<div class="info form">
					<table class="formI">
						<tr>
							<td><label class="check"><input id="check1" type="checkbox" name="subscribe" /><i></i></label></td>
							<td><label for="check1">Сообщите мне об акциях Freebie. <br/>(Вы можете отказаться от подписки <br/>на рассылку в любое время)</label></td>
						</tr>
						<tr>
							<td><label class="check"><input id="check2" type="checkbox" name="agree" /><i></i></label></td>
							<td><label for="check2">Я согласен с <a href="#">Условиями</a> и <br/><a href="#">Политикой конфиденциальности</a> данного сайта.</label></td>
						</tr>
					</table>
					<div class="field">
						<div class="title">E-mail</div>
						<div class="inp"><input type="text" name="email" placeholder="Введите Ваш E-mail" data-require="Введите адрес электронной почты" /><div class="tip"></div><b></b></div>
					</div>
					<div class="field">
						<div class="title">Пароль <span>(не менее 8 символов)</span></div>
						<div class="inp pass"><input type="password" name="password" placeholder="Введите Ваш пароль" data-require="Введите пароль" /><div class="tip"></div><i><img class="svg" src="/img/sight.svg" alt="" /></i><b></b></div>
					</div>
					<div class="field out"></div>
					<div class="center actions">
						<button class="but" name="reg">Зарегистрироваться</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="modalLogin" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="head">
					<h4 class="title">Вход в аккаунт</h4>
					<button type="button" class="cross" data-dismiss="modal"></button>
				</div>
				<div class="info form">
					<div class="field">
						<div class="title">E-mail</div>
						<div class="inp"><input type="text" name="email" placeholder="Введите Ваш E-mail" data-require="Введите адрес электронной почты" /><div class="tip"></div></div>
					</div>
					<div class="field">
						<div class="title">Пароль</div>
						<div class="inp pass"><input type="password" name="password" placeholder="Введите Ваш пароль" data-require="Введите пароль" /><div class="tip"></div><i><img class="svg" src="/img/sight.svg" alt="" /></i><b></b></div>
					</div>
					<div class="field">
						<div class="center forgot"><span class="nosel" onclick="main.OpenResetForm()">Забыли пароль?</span></div>
					</div>
					<div class="field out"></div>
					<div class="center actions">
						<button class="but" name="login">Войти</button>
					</div>
					<?php
					if($vkAuth || $ggAuth): ?>
					<div class="center socialReg">
						<hr />
						<?php if($vkAuth): ?><div><button class="but vk" onclick="main.AuthOpen(this, '<?= $vkAuth ?>');"><img src="/img/vk.svg" alt="ВКонтакте" /><span>Войти через VK</span></button></div><?php endif ?>
						<?php if($ggAuth): ?><div><button class="but gg" onclick="main.GoogleAuthOpen(this, '<?= $ggAuth ?>');"><img src="/img/google.svg" alt="Google" /><span>Войти через Google</span></button></div><?php endif ?>
					</div>
					<?php endif ?>
				</div>
			</div>
		</div>
	</div>
	<div id="modalReset" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="head">
					<h4 class="title">Сброс пароля</h4>
					<button type="button" class="cross" data-dismiss="modal"></button>
				</div>
				<div class="info form">
					<div class="field infoT">
						Мы отправим ссылку для восстановления пароля на адрес электронной почты вашей учетной записи.
					</div>
					<div class="field">
						<div class="title">E-mail</div>
						<div class="inp"><input type="text" name="email" placeholder="Введите Ваш E-mail" /><div class="tip"></div></div>
					</div>
					<div class="field out"></div>
					<div class="center actions">
						<button class="but" name="reset">Сброс пароля</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="modalResetComplete" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="head">
					<h4 class="title">Восстановление пароля</h4>
					<button type="button" class="cross" data-dismiss="modal"></button>
				</div>
				<div class="info form">
					<div class="field infoT">
						Ваш пароль был успешно сброшен!<br/>
						Письмо с подтверждением было отправлено на<br/>
						<b class="email"></b><br/><br/>
						Эта процедура может занять определенное время!<br/><br/>
					</div>
					<div class="center actions">
						<button class="but" data-dismiss="modal">Хорошо!</button>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php if(isset($_GET['action']) && isset($_GET['key']) && $_GET['action'] == 'reset'):
	$user = User::find()->where(['password_reset_token' => $_GET['key']])->one();
?>
	<div id="modalResetInput" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="head">
					<h4 class="title">Восстановление пароля</h4>
					<button type="button" class="cross" data-dismiss="modal"></button>
				</div>
				<div class="info form">
				<?php if($user): ?>
					<input type="hidden" name="key" value="<?= $_GET['key'] ?>" />
					<div class="field infoT">
						Восстановление пароля для:<br/>
						<b class="email"><?= $user->email ?></b><br/><br/>
					</div>
					<div class="field">
						<div class="title">Ваш пароль должен быть не менее 8 символов.</div>
						<div class="inp pass"><input type="password" name="password" placeholder="Введите новый пароль" data-require="Введите пароль" /><div class="tip"></div><i><img class="svg" src="/img/sight.svg" alt="" /></i><b></b></div>
					</div>
					<div class="field">
						<div class="title">Повторите новый пароль</div>
						<div class="inp pass"><input type="password" name="password_confirm" placeholder="Введите новый пароль" data-require="Введите пароль" /><div class="tip"></div><i><img class="svg" src="/img/sight.svg" alt="" /></i><b></b></div>
					</div>
					<div class="center actions">
						<button class="but" name="reset">Восстановить</button>
					</div>
				<?php else: ?>
					<div class="field infoT">
						Ссылка для смены пароля устарела.<br/>
						При необходимости можете повторить<br/>процедуру смены пароля.<br/>
					</div>
					<div class="center actions">
						<button class="but" data-dismiss="modal">Хорошо!</button>
					</div>
				<?php endif ?>
				</div>
			</div>
		</div>
	</div>
	<script>
		$(document).ready(function() {$("#modalResetInput").modal('toggle');});
	</script>
<?php endif ?>
	<div id="modalConfirm" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="head">
					<h4 class="title">Активация аккаунта</h4>
					<button type="button" class="cross" data-dismiss="modal"></button>
				</div>
				<div class="info form">
					<div class="field infoB">
						Ваша учетная запись в настоящее время не активна. Пожалуйста, проверьте свою электронную<br>почту для ссылки активации.
					</div>
					<div class="field">
						<div class="title">Не получили ссылку активации? Используйте форму ниже, чтобы повторно отправить электронное письмо.</div>
						<div class="inp inpB"><input type="text" name="email" placeholder="Введите Ваш E-mail" /><button class="but" name="resend">Обновить</button><div class="tip"></div></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="modalCookie" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="info form">
					<div class="field infoT">
					Наш сайт использует cookie (файлы с данными о прошлых посещениях сайта) для персонализации сервисов и удобства пользователей. Мы серьезно относимся к защите персональных данных — ознакомьтесь с условиями и принципами их обработки.
					</div>
					<div class="center actions">
						<button class="but" data-dismiss="modal">Хорошо!</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?= isset($this->blocks['forms']) ? $this->blocks['forms'] : ''; ?>
	<?php $this->endBody() ?>
	<?= Settings::getValue('code_body_end'); ?>
</body>
</html>
<?php $this->endPage() ?>
