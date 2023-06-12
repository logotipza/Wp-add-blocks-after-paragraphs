<?php
/*
Plugin Name: Вставка блоков после параграфов
Description: Автоматическая вставка повторно используемых блоков после указанных параграфов и после последнего параграфа во всех записях. Конфигурация на странице настроек.
Version: 2.0
Author: [intrsno.ru](http://intrsno.ru/)
Author URI: [https://intrsno.ru](https://intrsno.ru/)
*/

add_action('admin_menu', 'register_insert_blocks_menu_page');

function register_insert_blocks_menu_page(){
    add_menu_page('Вставка блоков', 'Вставка блоков', 'manage_options', 'insert-blocks-settings', 'insert_blocks_settings_page', '', 6);
}

function insert_blocks_settings_page(){
    include 'options-page.php';
}

function insert_reusable_blocks_after_paragraphs( $content ) {
    if ( is_singular( 'post' ) ) {
        $rules = get_option('insert_blocks_rules');
        if (empty($rules)) {
            return $content;
        }

        foreach ( $rules as $rule ) {
            $block_id = intval($rule['block_id']);
            $paragraph_number = intval($rule['paragraph']);

            $post_categories = wp_get_post_categories(get_the_ID());
            if ($rule['category'] != '*' && !in_array($rule['category'], $post_categories)) {
                continue;
            }

            $block = '';
            if ( function_exists( 'gutenberg_render_block_core_block' ) ) {
                $block = gutenberg_render_block_core_block( array( 'ref' => $block_id ) );
            } elseif ( function_exists( 'render_block_core_block' ) ) {
                $block = render_block_core_block( array( 'ref' => $block_id ) );
            }

            if ( ! empty( $block ) ) {
                // Разбиваем контент на параграфы
                $paragraphs = preg_split( '/<\/p>/', $content );
                $last_paragraph = array_pop($paragraphs);
                $paragraphs[] = $last_paragraph . '</p>';

                // Позиция, где мы вставляем блок
                $insert_position = ($paragraph_number == -1) ? -1 : $paragraph_number - 1;
                $block_content = '</p>' . $block;

                if ($insert_position >= 0 && $insert_position < count($paragraphs)) {
                    // Вставляем блок на определенную позицию
                    array_splice($paragraphs, $insert_position, 0, $block_content);
                } elseif ($insert_position == -1) {
                    // Вставляем в конец
                    $paragraphs[] = $block_content;
                }

                $content = implode('</p>', $paragraphs);
            }
        }
    }

    return $content;
}

add_filter( 'the_content', 'insert_reusable_blocks_after_paragraphs' );
