<?php $id = (int) $_GET['id']; ?>
<?php $ticket = tickets::getInfo($id) ?>
<div class="modal-box">
	<div class="modal-hide" onclick="return modal.hide();"></div>
	<div class="progress-box" id="<?=$id?>">
        <div class="progress-title middle-size weight-normal">Обновить тикет</div>
		<div class="progress-field">
            <div class="field-title small-size">Выполнено</div>
            <div class="dropdown-select" for="done"></div>
            <select id="done" class="dropdown-template">
                <option <?=intval($ticket['done']) == 0 ? 'selected' : ''?>>0%</option>
                <option <?=intval($ticket['done']) == 10 ? 'selected' : ''?>>10%</option>
                <option <?=intval($ticket['done']) == 20 ? 'selected' : ''?>>20%</option>
                <option <?=intval($ticket['done']) == 30 ? 'selected' : ''?>>30%</option>
                <option <?=intval($ticket['done']) == 40 ? 'selected' : ''?>>40%</option>
                <option <?=intval($ticket['done']) == 50 ? 'selected' : ''?>>50%</option>
                <option <?=intval($ticket['done']) == 60 ? 'selected' : ''?>>60%</option>
                <option <?=intval($ticket['done']) == 70 ? 'selected' : ''?>>70%</option>
                <option <?=intval($ticket['done']) == 80 ? 'selected' : ''?>>80%</option>
                <option <?=intval($ticket['done']) == 90 ? 'selected' : ''?>>90%</option>
                <option <?=intval($ticket['done']) == 100 ? 'selected' : ''?>>100%</option>
            </select>
        </div>
        <div class="progress-field">
            <div class="field-title small-size">Времени потрачено (в минутах)</div>
            <input class="input block" type="number" step="5" style="background: #eee; box-shadow: 0 0 0 -1000px #eee inset" id="time_spent">
        </div>
        <button class="input cursor-pointer background-lightgrey iblock width25 fl-right" style="margin-top: 20px" onclick="return tickets.progress(this, event)">Обновить</button>
        <br style="clear: both"/>
	</div>
</div>