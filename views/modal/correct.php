<?php $id = (int) $_GET['id']; ?>
<div class="modal-box">
	<div class="modal-hide" onclick="return modal.hide();"></div>
	<div class="progress-box" id="<?=$id?>">
        <div class="progress-title middle-size weight-normal">Уточнити інформацію по завданню</div>
        <div class="progress-field">
            <div class="field-title small-size">Що саме ви хочете уточнити?</div>
            <input class="input block" type="text" style="background: #eee; box-shadow: 0 0 0 -1000px #eee inset" id="correct_question">
        </div>
        <button class="input cursor-pointer background-lightgrey iblock width25 fl-right" style="margin-top: 20px" onclick="return tickets.correct(this, event)">Уточнити</button>
        <br style="clear: both"/>
	</div>
</div>