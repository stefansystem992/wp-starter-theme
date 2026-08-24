<?php

/**
 * Template Name: ACF Block Audit
 * Template Post Type: page
 */

if (!function_exists('nmd36_collect_acf_blocks_from_blocks')) {
    function nmd36_collect_acf_blocks_from_blocks($blocks, &$acf_blocks)
    {
        foreach ($blocks as $block) {
            if (!empty($block['blockName']) && strpos($block['blockName'], 'acf/') === 0) {
                $acf_blocks[] = $block['blockName'];
            }

            if (!empty($block['innerBlocks'])) {
                nmd36_collect_acf_blocks_from_blocks($block['innerBlocks'], $acf_blocks);
            }
        }
    }
}

if (!function_exists('nmd36_collect_acf_blocks_from_content')) {
    function nmd36_collect_acf_blocks_from_content($content)
    {
        $acf_blocks = array();

        if (function_exists('parse_blocks')) {
            nmd36_collect_acf_blocks_from_blocks(parse_blocks($content), $acf_blocks);
        }

        if (empty($acf_blocks) && preg_match_all('/<!--\s+wp:(acf\/[a-z0-9-]+)/i', $content, $matches)) {
            $acf_blocks = $matches[1];
        }

        return $acf_blocks;
    }
}

$acf_block_labels = function_exists('nmd36_acf_block_labels') ? nmd36_acf_block_labels() : array();
$acf_block_usage = array();

$pages_query = new WP_Query(array(
    'post_type'              => 'page',
    'post_status'            => 'publish',
    'posts_per_page'         => -1,
    'orderby'                => 'title',
    'order'                  => 'ASC',
    'no_found_rows'          => true,
    'update_post_meta_cache' => false,
    'update_post_term_cache' => false,
));

if ($pages_query->have_posts()) {
    while ($pages_query->have_posts()) {
        $pages_query->the_post();

        $page_id = get_the_ID();
        $page_blocks = array_count_values(nmd36_collect_acf_blocks_from_content(get_post_field('post_content', $page_id)));

        foreach ($page_blocks as $block_name => $block_count) {
            if (!isset($acf_block_usage[$block_name])) {
                $acf_block_usage[$block_name] = array(
                    'label' => isset($acf_block_labels[$block_name]) ? $acf_block_labels[$block_name] : $block_name,
                    'count' => 0,
                    'pages' => array(),
                );
            }

            $acf_block_usage[$block_name]['count'] += $block_count;
            $acf_block_usage[$block_name]['pages'][] = array(
                'id'    => $page_id,
                'title' => get_the_title(),
                'url'   => get_permalink(),
                'count' => $block_count,
            );
        }
    }

    wp_reset_postdata();
}

ksort($acf_block_usage, SORT_NATURAL | SORT_FLAG_CASE);

get_header();
?>

<main class="acf-block-audit">
    <style>
        .acf-block-audit {
            padding: 140px 24px 80px;
        }

        .acf-block-audit__inner {
            margin: 0 auto;
            max-width: 1180px;
        }

        .acf-block-audit__header {
            margin-bottom: 32px;
        }

        .acf-block-audit__title {
            margin-bottom: 8px;
        }

        .acf-block-audit__summary {
            font-size: 18px;
            margin: 0;
        }

        .acf-block-audit__table-wrap {
            overflow-x: auto;
        }

        .acf-block-audit__table {
            border-collapse: collapse;
            min-width: 760px;
            width: 100%;
        }

        .acf-block-audit__table th,
        .acf-block-audit__table td {
            border-bottom: 1px solid rgba(0, 0, 0, .16);
            padding: 16px;
            text-align: left;
            vertical-align: top;
        }

        .acf-block-audit__table thead th {
            border-top: 1px solid rgba(0, 0, 0, .16);
            font-size: 13px;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        .acf-block-audit__block-label {
            display: block;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .acf-block-audit__block-subtitle {
            display: block;
            font-size: 13px;
            opacity: .72;
        }

        .acf-block-audit code {
            font-size: 13px;
        }

        .acf-block-audit__page-list {
            display: grid;
            gap: 6px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .acf-block-audit__page-list span {
            margin-left: 4px;
            opacity: .72;
        }
    </style>

    <div class="acf-block-audit__inner">
        <header class="acf-block-audit__header">
            <h1 class="acf-block-audit__title"><?php the_title(); ?></h1>
            <p class="acf-block-audit__summary">
                <?php
                printf(
                    esc_html__('%1$d ACF block types found across published pages.', 'custom_wp_theme'),
                    count($acf_block_usage)
                );
                ?>
            </p>
        </header>

        <?php if (!empty($acf_block_usage)) : ?>
            <div class="acf-block-audit__table-wrap">
                <table class="acf-block-audit__table">
                    <thead>
                        <tr>
                            <th scope="col"><?php esc_html_e('Block', 'custom_wp_theme'); ?></th>
                            <th scope="col"><?php esc_html_e('Pages', 'custom_wp_theme'); ?></th>
                            <th scope="col"><?php esc_html_e('Total instances', 'custom_wp_theme'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($acf_block_usage as $block_name => $block_data) : ?>
                            <tr>
                                <th scope="row">
                                    <code class="acf-block-audit__block-label"><?php echo esc_html($block_name); ?></code>
                                    <span class="acf-block-audit__block-subtitle"><?php echo esc_html($block_data['label']); ?></span>
                                </th>
                                <td>
                                    <ul class="acf-block-audit__page-list">
                                        <?php foreach ($block_data['pages'] as $page_data) : ?>
                                            <li>
                                                <a href="<?php echo esc_url($page_data['url']); ?>">
                                                    <?php echo esc_html($page_data['title']); ?>
                                                </a>
                                                <?php if ($page_data['count'] > 1) : ?>
                                                    <span><?php echo esc_html(sprintf('(%d)', $page_data['count'])); ?></span>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </td>
                                <td><?php echo esc_html($block_data['count']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <p class="acf-block-audit__empty"><?php esc_html_e('No ACF blocks were found in published page content.', 'custom_wp_theme'); ?></p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
