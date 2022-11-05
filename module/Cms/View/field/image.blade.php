<div>
    <?php
    $f = new \ModStart\Field\Image($field['name'], [$field['title']]);
    if ($field['isRequired']) {
        $f->required();
    }
    $f->renderMode(\ModStart\Field\Type\FieldRenderMode::FORM);
    $f->value($record?$record[$field['name']]:null);
    echo $f->render();
    ?>
</div>
