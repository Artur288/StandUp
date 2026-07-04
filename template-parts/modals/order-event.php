<div class="order_event_modal" id="orderEventModal" aria-hidden="true">
	<div class="order_event_modal__overlay js-order-event-close" tabindex="-1" aria-label="Закрыть"></div>
	<div class="order_event_modal__content" role="dialog" aria-modal="true" aria-label="Заказать мероприятие">
		<button type="button" class="order_event_modal__close js-order-event-close" aria-label="Закрыть">
			<svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 42 42" fill="none">
				<path d="M9.49027 11.2812L32.7614 29.5541" stroke="#EEEDDE" stroke-width="2" stroke-linecap="square"/>
				<path d="M8.77689 29.5488L32.048 11.276" stroke="#EEEDDE" stroke-width="2" stroke-linecap="square"/>
			</svg>
		</button>
		<div class="order_event_modal__header">
			<div class="order_event_modal__title">Заказать мероприятие</div>
			<div class="order_event_modal__subtitle">
				Укажите в вашем сообщении какой формат<br>
				вас заинтересовал и мы обязательно<br>
				обсудим с вами все детали
			</div>
		</div>
		<form class="order_event_form" id="orderEventForm" novalidate>
			<input type="hidden" name="event_format" value="">
			<div class="order_event_form__grid">
				<div class="order_event_field">
					<div class="order_event_field__label">ФИО</div>
					<input class="order_event_field__control" type="text" name="name" placeholder="ФИО" autocomplete="name">
				</div>
				<div class="order_event_field">
					<div class="order_event_field__label">E–mail</div>
					<input class="order_event_field__control" type="email" name="email" placeholder="gmail@gmail.com" autocomplete="email">
				</div>
				<div class="order_event_field">
					<div class="order_event_field__label">Телефон</div>
					<input class="order_event_field__control js-phone-mask" type="tel" name="phone" placeholder="+7 (999) 000–00–00" autocomplete="tel">
				</div>
				<div class="order_event_field">
					<div class="order_event_field__label">Дата мероприятия</div>
					<input class="order_event_field__control js-date-mask" type="text" name="date" placeholder="01.01.2000" inputmode="numeric">
				</div>
				<div class="order_event_field order_event_field--message">
					<div class="order_event_field__label">Сообщение</div>
					<textarea class="order_event_field__control" name="message" placeholder="Напишите ваше сообщение..." rows="3"></textarea>
				</div>
			</div>
			<div class="order_event_form__actions">
				<button type="submit" class="order_event_submit">
					<span class="order_event_submit__text">Отправить</span>
					<span class="order_event_submit__icon" aria-hidden="true">
						<svg xmlns="http://www.w3.org/2000/svg" width="8" height="18" viewBox="0 0 8 18" fill="none">
							<path d="M1 1L7 9L1 17" stroke="#EEEDDE" stroke-width="2" stroke-linejoin="round"/>
						</svg>
					</span>
				</button>
			</div>
		</form>
	</div>
</div>
