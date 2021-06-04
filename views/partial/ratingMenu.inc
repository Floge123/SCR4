<div class="mb-3">
<label for="comment" class="form-label">Comment</label>
<textarea class="form-control shadow textarea" id="comment" name="rc">
<?php if ($data['rating'] != null): ?>
<?php $htmlOut($data['rating']->getComment()); ?>
<?php endif; ?>
</textarea>
</div>
<h5>Grade</h5>
<div class="mb-2">
    <button class="btn btn-sm btn-outline-info rating-button" type="button" name="btn_1" value="1">1</button>
    <button class="btn btn-sm btn-outline-info rating-button" type="button" name="btn_2" value="2">2</button>
    <button class="btn btn-sm btn-outline-info rating-button" type="button" name="btn_3" value="3">3</button>
    <button class="btn btn-sm btn-outline-info rating-button" type="button" name="btn_4" value="4">4</button>
    <button class="btn btn-sm btn-outline-info rating-button" type="button" name="btn_5" value="5">5</button>
</div>
<input class="form-control hide visually-hidden" type="text" id="grade" name="grade" value=""/>

<script>
    $(".rating-button").click(function () {
        var buttons = $(".rating-button");
        var gradeInput = document.getElementById("grade");
        for (let i = 0; i < buttons.length; i++) {
            buttons[i].classList.remove("active");
        }
        gradeInput.setAttribute('value', this.value);
        this.classList.add("active");
    });
    $(document).ready(function() {
        $('textarea').on('keyup keypress', function() {
            $(this).height(0);
            $(this).height(this.scrollHeight);
        });
    });
</script>