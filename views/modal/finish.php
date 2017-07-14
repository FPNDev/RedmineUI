<?php $id = (int) $_GET['id']; ?>
<div class="modal-box">
	<div class="modal-hide" onclick="return modal.hide();"></div>
	<div class="progress-box" id="<?=$id?>">
        <div class="progress-title middle-size weight-normal">Завершить тикет</div>
        <div class="progress-field">
            <div class="field-title small-size">Времени потрачено (в минутах)</div>
            <input class="input block" type="number" step="5" style="background: #eee; box-shadow: 0 0 0 -1000px #eee inset" id="time_spent">
        </div>
        <button class="input cursor-pointer background-lightgrey iblock width25 fl-right" style="margin-top: 20px" onclick="return tickets.finish(this, event)">Завершить</button>
        <br style="clear: both"/>
	</div>
</div>