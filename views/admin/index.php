<?php $_PAGE['title'] = 'Мои тикеты'; ?>
<div class="big-size align-center" style="margin: 25px 0 35px">Мои тикеты</div>
<div class="tickets align-center">
	<div class="cursor-pointer fl-left tiny-size tt_parent" tt_mode="bottom" tt_am tt_selector=".user_tooltip" style="padding: 3px; margin: 4px;"><?=cookie::get()['username']?></div>
	<div class="tooltip_template">
		<div class="user_tooltip">
			<a class="option cursor-pointer tiny-size" href="/" onclick="return nav.go(this, event)">Мои тикеты</a>
			<a class="option cursor-pointer tiny-size" onclick="return log.out(this, event)">Выход</a>
		</div>
	</div>
	<div class="cursor-pointer fl-right tickets-refresh tiny-size" style="padding: 3px; margin: 4px;" onclick="tickets.refresh();">Перезагрузить список</div>
	<div class="block tbl" id="tickets-list">
		<div class="tbl-row tbl-header">
			<div class="tbl-cell">
				Проект
				<select for="project" onchange="tickets.get(this, event)">
					<?php foreach($categories['project'] as $k => $cat):?>
						<option value="<?=$k > 0 ? htmlspecialchars($cat) : ''?>"><?=htmlspecialchars($cat)?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="tbl-cell">
				Вид
				<select for="type" onchange="tickets.get(this, event)">
					<?php foreach($categories['type'] as $k => $cat):?>
						<option value="<?=$k > 0 ? htmlspecialchars($cat) : ''?>"><?=htmlspecialchars($cat)?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="tbl-cell">
				Приоритет
				<select for="priority" onchange="tickets.get(this, event)">
					<?php foreach($categories['priority'] as $k => $cat):?>
						<option value="<?=$k > 0 ? htmlspecialchars($cat) : ''?>"><?=htmlspecialchars($cat)?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="tbl-cell">Завдання</div>
			<div class="tbl-cell">
				Статус
				<select for="status" onchange="tickets.get(this, event)">
					<?php foreach($categories['status'] as $k => $cat):?>
						<option value="<?=$k > 0 ? htmlspecialchars($cat) : ''?>"<?=$cat == 'Новий' || $cat == 'Новый' || $cat == 'New' ? ' selected' : ''?>><?=htmlspecialchars($cat)?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="tbl-cell"><span>Времени дано</span></div>
			<div class="tbl-cell"><span>Времени потрачено</span></div>
		</div>
		<?php foreach($tickets as $ticket): ?>
				<div class="tbl-row small-size" id="<?=$ticket['id']?>" style='background: linear-gradient(to right, #d5eed5 <?=$ticket['done']?>%, #f2f2f2 <?=$ticket['done']?>%)' sAnim='fadeInUp'>
					<div class="tbl-cell" id="project"><?=$ticket['project']?></div>
					<div class="tbl-cell" id="type"><?=$ticket['type']?></div>
					<div class="tbl-cell" id="priority"><?=$ticket['priority']?></div>
					<div class="tbl-cell" id="subject"><span><?=$ticket['subject']?></span></div>
					<div class="tbl-cell" id="status"><?=$ticket['status']?></div>
					<div class="tbl-cell" id="estimated"><?=$ticket['estimated'] == '00 год.' ? '' : $ticket['estimated']?></div>
					<div class="tbl-cell" id="spent"><?=$ticket['spent']?></div>
					<div class="tbl-cell">
						<div class="action-list">
							<div class="iblock ticket-progress cursor-pointer" href="/modal/progress.php?id=<?=$ticket['id']?>" onclick="return modal.show(this, event)"></div>
							<div class="iblock ticket-finish cursor-pointer" href="/modal/finish.php?id=<?=$ticket['id']?>" onclick="return modal.show(this, event)"></div>
							<div class="iblock ticket-go cursor-pointer" href="/ticket/<?=$ticket['id']?>" onclick="return nav.go(this, event)"></div>
						</div>
					</div>
				</div>
		<?php endforeach; ?>
	</div>
	<div id="no-tickets" class="big-size uppercase color-grey" style="margin-top: 126px;">Тикетов не найдено</div>
</div>