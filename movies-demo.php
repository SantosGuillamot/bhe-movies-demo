<?php

/**
 * Plugin Name:       BHE Movies Demo
 * Version:           0.1.0
 * Requires at least: 6.0
 * Requires PHP:      5.6
 * Author:            Mario Santos
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       bhe-movies-demo
 */

function is_demo_variation($parsed_block)
{
    return isset($parsed_block['attrs']['namespace'])
        && substr($parsed_block['attrs']['namespace'], 0, 3) === 'bhe';
}

function update_demo_query($pre_render, $parsed_block)
{
    if ('core/query' !== $parsed_block['blockName']) {
        return;
    }

    if (is_demo_variation($parsed_block)) {
        add_filter(
            'query_loop_block_query_vars',
            'build_cast_query',
            10,
            1
        );
    }
};

function build_cast_query($query)
{
    global $post;
    $taxonomy = $query['tax_query'][0]['taxonomy'];
    $wp_term = get_term_by('slug', $post->post_name, $taxonomy);
    $cast_query = array('taxonomy' => $taxonomy, 'terms' => array($wp_term->term_id), 'include_children' => false);
    $new_query = array_replace($query, array('tax_query' => array($cast_query)));
    return $new_query;
}

add_action('pre_render_block', 'update_demo_query', 10, 2);

function add_query_loop_variations()
{
    wp_enqueue_script(
        'query-loop-variations',
        'http://movies.local/wp-content/plugins/bhe-movies-demo/build/index.js',
        array('wp-blocks')
    );
}
add_action('init', 'add_query_loop_variations');
