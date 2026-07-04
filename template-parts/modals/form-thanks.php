<?php
$title = (string) get_field('form_thanks_title', 'option');
$text  = (string) get_field('form_thanks_text', 'option');
?>
<div class="form_thanks" id="formThanksModal" aria-hidden="true">
	<div class="form_thanks__overlay" data-form-thanks-close></div>
	<div class="form_thanks__dialog" role="dialog" aria-modal="true" aria-labelledby="formThanksTitle">
		<button type="button" class="form_thanks__close" aria-label="Закрыть" data-form-thanks-close>
			<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none">
				<path d="M2 2L20 20M20 2L2 20" stroke="#EEEDDE" stroke-width="2" stroke-linecap="round"/>
			</svg>
		</button>
		<?php if ($title !== ''): ?>
			<h3 id="formThanksTitle" class="form_thanks__title"><?php echo nl2br(esc_html($title)); ?></h3>
		<?php endif; ?>
		<?php if ($text !== ''): ?>
			<p class="form_thanks__text"><?php echo esc_html($text); ?></p>
		<?php endif; ?>
	</div>
</div>
