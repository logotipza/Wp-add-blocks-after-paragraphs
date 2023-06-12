<?php
$categories = get_categories();
$category_options = [];
foreach ($categories as $category) {
    $category_options[$category->term_id] = $category->name;
}

if ($_POST['form_submitted'] == 'Y') {
    $action = $_POST['action'];
    if ($action == 'add') {
        $rules = get_option('insert_blocks_rules');
        if (empty($rules)) {
            $rules = [];
        }
        array_push($rules, [
            'category' => $_POST['category'],
            'paragraph' => $_POST['paragraph'],
            'block_id' => $_POST['block_id'],
        ]);
        update_option('insert_blocks_rules', $rules);
    } elseif ($action == 'delete') {
        $rules = get_option('insert_blocks_rules');
        array_splice($rules, $_POST['rule_index'], 1);
        update_option('insert_blocks_rules', $rules);
    } elseif ($action == 'update') {
        $rules = get_option('insert_blocks_rules');
        $rules[$_POST['rule_index']] = [
            'category' => $_POST['category'],
            'paragraph' => $_POST['paragraph'],
            'block_id' => $_POST['block_id'],
        ];
        update_option('insert_blocks_rules', $rules);
    }
}
?>

<div class="wrap">
    <h2>Вставка блоков</h2>
    <h2>Настройки</h2>
    <form method="post" action="">
        <input type="hidden" name="form_submitted" value="Y">
        <h2>Добавить новое правило</h2>
        <input type="hidden" name="action" value="add">
        <p>Категория:</p>
        <select name="category">
            <option value="*">Все категории</option>
            <?php
            foreach ($category_options as $value => $name) {
                echo '<option value="' . $value . '">' . $name . '</option>';
            }
            ?>
        </select>
        <p>Параграф:</p>
        <input type="number" name="paragraph">
        <p>ID блока:</p>
        <input type="number" name="block_id">
        <p><input class="button button-primary" type="submit" value="Добавить правило"></p>
    </form>
    <h2>Существующие правила</h2>
    <?php
    $rules = get_option('insert_blocks_rules');
    if (!empty($rules)) {
        foreach ($rules as $index => $rule) {
            echo '<form method="post" action="" style="display: inline-block; margin-right: 40px;">';
            echo '<input type="hidden" name="form_submitted" value="Y">';
            echo '<input type="hidden" name="action" value="update">';
            echo '<input type="hidden" name="rule_index" value="' . $index . '">';
            echo '<p>Категория:</p>';
            echo '<select name="category">';
            echo '<option value="*" ' . ($rule['category'] == '*' ? 'selected' : '') . '>Все категории</option>';
            foreach ($category_options as $value => $name) {
                echo '<option value="' . $value . '" ' . ($rule['category'] == $value ? 'selected' : '') . '>' . $name . '</option>';
            }
            echo '</select>';
            echo '<p>Параграф:</p>';
            echo '<input type="number" name="paragraph" value="' . $rule['paragraph'] . '">';
            echo '<p>ID блока:</p>';
            echo '<input type="number" name="block_id" value="' . $rule['block_id'] . '">';
            echo '<p><input class="button button-primary" type="submit" value="Изменить правило"></p>';
            echo '</form>';
            echo '<form method="post" action="" style="display: inline-block;">';
            echo '<input type="hidden" name="form_submitted" value="Y">';
            echo '<input type="hidden" name="action" value="delete">';
            echo '<input type="hidden" name="rule_index" value="' . $index . '">';
            echo '<p><input class="button button-primary" type="submit" value="Удалить правило"></p>';
            echo '</form>';
            echo '<hr>';
        }
    }
    ?>
</div>
