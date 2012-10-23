<? defined('C5_EXECUTE') or die(_('Access Denied.'));
$form = Loader::helper('form');
?>

<img style="margin-left: 20px; display: block; float: right" src="/<?= DIRNAME_PACKAGES ?>/message_of_the_day/icon.png" />
<h4><?= t('Install Message Lists') ?></h4>
<p><?= t('Select which message lists you would like to install. Each one includes a large selection of quotes.') ?></p>
<ul class="inputs-list">
	<li><?= $form->checkbox('scripture', 'yes', 0) ?> <?= t('Bible Scripture') ?></li>
	<li><?= $form->checkbox('mark-twain', 'yes', 0) ?> <?= t('Mark Twain') ?></li>
</ul>
