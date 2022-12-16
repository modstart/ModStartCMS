<div>
    <?php
    $f = new \ModStart\Field\File($fieldName, [$field['title']]);
    if (!empty($field['isRequired'])) {
        $f->required();
    }
    $f->renderMode(\ModStart\Field\Type\FieldRenderMode::FORM);
    $f->value(!empty($record)?$record[$fieldName]:null);
    echo $f->render();
    ?>
</div>