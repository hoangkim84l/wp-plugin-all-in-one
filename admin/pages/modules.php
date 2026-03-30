<?php
if (!defined('ABSPATH')) exit;

$all_modules = MyAIO_Module_Manager::get_all_modules();
$active_modules = MyAIO_Module_Manager::get_active_modules();

$module_labels = [
    'permalink'      => __('Permalink Settings', 'my-woo-aio'),
    'category-page'  => __('Category Page Optimization', 'my-woo-aio'),
    'tags'           => __('Tags Enhancements', 'my-woo-aio'),
    'loadmore'       => __('Load More Button', 'my-woo-aio'),
    'price-engine'   => __('Price Engine', 'my-woo-aio'),
    'comment-upload' => __('Comment Image Upload', 'my-woo-aio'),
    'read-more'      => __('Change "Read More" text', 'my-woo-aio'),
    'scroll-to-top'  => __('"Scroll to Top" Button', 'my-woo-aio'),
];
?>

<div class="wrap">
    <h1><?php _e('Modules', 'my-woo-aio'); ?></h1>

    <form method="post" action="options.php">
        <?php
        settings_fields('myaio_modules_group');
        ?>

        <table class="widefat striped">
            <thead>
                <tr>
                    <th><?php _e('Module', 'my-woo-aio'); ?></th>
                    <th><?php _e('Status', 'my-woo-aio'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_modules as $module): ?>
                    <?php 
                        $label = isset($module_labels[$module]) ? $module_labels[$module] : $module;
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo esc_html($label); ?></strong><br>
                            <small><code><?php echo esc_html($module); ?></code></small>
                        </td>
                        <td>
                            <label>
                                <input
                                    type="checkbox"
                                    name="myaio_active_modules[]"
                                    value="<?php echo esc_attr($module); ?>"
                                    <?php checked(in_array($module, $active_modules)); ?>
                                />
                                <?php _e('Enabled', 'my-woo-aio'); ?>
                            </label>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php submit_button(); ?>
    </form>
</div>
