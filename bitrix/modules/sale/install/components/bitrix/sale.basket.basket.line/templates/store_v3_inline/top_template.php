<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

/**
 * @var array $arParams
 * @var array $arResult
 * @var string $cartId
 */

$compositeStub = ($arResult['COMPOSITE_STUB'] ?? 'N') === 'Y';
?>
<a class="d-flex g-text-decoration-none--hover" href="<?= $arParams['PATH_TO_BASKET'] ?>" data-page-url="#system_mainpage">
	<svg width="31" height="27" viewBox="0 0 31 27" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path
			d="M7.24627 1.86722L8.08452 5.48828L29.0807 5.4494C29.4306 5.45089 29.7597 5.61317 29.9741 5.88713C30.19 6.1626 30.2674 6.51993 30.187 6.85941L28.0518 18.7144C27.9298 19.2266 27.4727 19.5884 26.9471 19.5914H10.0277C9.50215 19.5884 9.04506 19.2266 8.92297 18.7144L5.22598 3.27722H1.9472C1.31567 3.27722 0.803711 2.76526 0.803711 2.13373C0.803711 1.50219 1.31567 0.990234 1.9472 0.990234H6.14001C6.66709 0.993207 7.12269 1.35501 7.24627 1.86722Z"
			fill="#121212"
		/>
		<path
			d="M16.2356 23.7842C16.2356 25.3833 14.9387 26.6801 13.3381 26.6801C11.739 26.6801 10.4421 25.3833 10.4421 23.7842C10.4421 22.1836 11.739 20.8867 13.3381 20.8867C14.9387 20.8867 16.2356 22.1835 16.2356 23.7842Z"
			fill="#121212"
		/>
		<path
			d="M26.4511 23.7842C26.4511 25.3833 25.1543 26.6801 23.5537 26.6801C21.9546 26.6801 20.6577 25.3833 20.6577 23.7842C20.6577 22.1836 21.9545 20.8867 23.5537 20.8867C25.1543 20.8867 26.4511 22.1835 26.4511 23.7842Z"
			fill="#121212"
		/>
	</svg>
	<?php
	if (!$compositeStub)
	{
		if (
			$arParams['SHOW_NUM_PRODUCTS'] === 'Y'
			&& ($arResult['NUM_PRODUCTS'] > 0 || $arParams['SHOW_EMPTY_VALUES'] === 'Y')
		)
		{
			?><span class="catalog-cart-counter-menu"><?= $arResult['NUM_PRODUCTS'] ?></span><?php
		}
	}
	?>
</a>