<div class="ticket-wrap block-center" id="<?=$ticket['id']?>">
	<div class="ticket-base">
		<div class="ticket-id weight-normal middle-size fl-left" sAnim="fadeInLeft"><?=$ticket['type']?> #<?=$ticket['id']?></div>
		<div class="action-list fl-right">
			<a class="iblock ticket-back cursor-pointer" onclick="return nav.go('/')"></a>
			<a class="iblock ticket-correct cursor-pointer" href="/modal/correct.php?id=<?=$ticket['id']?>" onclick="return modal.show(this, event)"></a>
			<a class="iblock ticket-progress cursor-pointer" href="/modal/progress.php?id=<?=$ticket['id']?>" onclick="return modal.show(this, event)"></a>
			<a class="iblock ticket-finish cursor-pointer" href="/modal/finish.php?id=<?=$ticket['id']?>" onclick="return modal.show(this, event)"></a>
		</div>
	</div>
	<div class="ticket-info-wrap">
		<div class="ticket-bb">
			<div class="ticket-title weight-normal fl-left width75" sAnim="fadeInRight" sDuration="0.5"><?=$ticket['subject']?></div>
			<div class="tiny-size fl-right cursor-pointer refresh-btn" onclick="tickets.refreshThis(this, event)">Обновить</div>
		</div>
		<div class="ticket-info small-size" sAnim="fadeIn" sDuration="2">
			<div class="fl-left width50">
				<div class="info-entry" id="status">
					<div class="info-title fl-left">Статус</div>
					<div class="info-value fl-right"><?=$ticket['status']?></div>
				</div>
				<div class="info-entry" id="priority">
					<div class="info-title fl-left">Приорітет</div>
					<div class="info-value fl-right"><?=$ticket['priority']?></div>
				</div>
				<div class="info-entry" id="assignee">
					<div class="info-title fl-left">Призначена до</div>
					<div class="info-value fl-right"><?=$ticket['assignee']?></div>
				</div>
				<div class="info-entry" id="done">
					<div class="info-title fl-left">Зроблено на</div>
					<div class="info-value fl-right"><?=$ticket['done']?>%</div>
				</div>
			</div>
			<div class="fl-left width50">
				<div class="info-entry" id="start_date">
					<div class="info-title fl-left">Дата початку</div>
					<div class="info-value fl-right"><?=$ticket['start_date']?></div>
				</div>
				<div class="info-entry" id="due_date">
					<div class="info-title fl-left">Дата виконання</div>
					<div class="info-value fl-right"><?=$ticket['due_date']?></div>
				</div>
				<div class="info-entry" id="estimated_hours">
					<div class="info-title fl-left">Очікуваний час</div>
					<div class="info-value fl-right"><?=$ticket['estimated_hours']?></div>
				</div>
				<div class="info-entry" id="spent_time">
					<div class="info-title fl-left">Витрачений час</div>
					<div class="info-value fl-right"><?=$ticket['spent_time']?></div>
				</div>
			</div>
		</div>
	</div>
	<div class="ticket-wiki ajax small-size" id="content"><?=$ticket['content']?></div>
	<div class="ticket-history ajax tiny-size" id="history"><?=$ticket['history']?></div>
</div>