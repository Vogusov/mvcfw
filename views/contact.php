<?php

/** @var $modal \app\models\ContactForm */
/** @var $this \app\core\View */

use app\core\form\Form;

$this->title = 'Contact';
?>


<h1>Contact Us</h1>


<?php $form = Form::begin('', 'post'); ?>
<?= $form->field($model, 'subject'); ?>
<?= $form->field($model, 'email'); ?>
<?= new \app\core\form\TextareaField($model, 'body'); ?>
<button type="submit" class="btn btn-primary">Submit</button>
<?php Form::end(); ?>