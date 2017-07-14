<?php $id = (int) $_GET['id']; ?>
<div class="modal-box">
	<div class="modal-hide" onclick="return modal.hide();"></div>
	<div class="progress-box" id="<?=$id?>">
        <div class="progress-title middle-size weight-normal">Обновить тикет</div>
		<div class="progress-field">
            <div class="field-title small-size">Выполнено</div>
            <div class="dropdown-select" for="done"></div>
            <select id="done" class="dropdown-template">
                <option selected>0%</option>
                <option>10%</option>
                <option>20%</option>
                <option>30%</option>
                <option>40%</option>
                <option>50%</option>
                <option>60%</option>
                <option>70%</option>
                <option>80%</option>
                <option>90%</option>
                <option>100%</option>
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