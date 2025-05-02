<ul>
    <?php foreach ($this->data['info'] as $field => $value): ?>
        <li><b><?=$field?></b>: <?=$value?></li>
    <?php endforeach;?>
</ul>