<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap">
    <h1>Class Level Counts</h1>
    <p>Gets counts for Beginner and Experienced levels for each class.</p>

    <div class="sr-forms">
        <?php if ( empty( $display_rows ) ) : ?>
        <form method="post" class="sr-form" style="display:inline-block;margin-right:12px;">
            <?php wp_nonce_field( 'sr_class_level_counts', 'sr_class_level_counts_nonce' ); ?>
            <input type="submit" name="view-class-level-counts" value="Get Level Counts" class="button button-primary sr-button" />
        </form>
        <?php endif; ?>

        <?php if ( ! empty( $display_rows ) ) : ?>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="sr-form" style="display:inline-block;">
                <?php wp_nonce_field( 'sr_class_level_counts', 'sr_class_level_counts_nonce' ); ?>
                <input type="hidden" name="action" value="sr_counts_per_class" />
            </form>
        <?php endif; ?>
    </div>

    <?php if ( empty( $display_rows ) ) : ?>
        <p>No registrants found.</p>
    <?php else : ?>
        <div class="sr-wrap">
            <table class="sr-table">
                <thead>
                <tr>
                    <th>CLASS NAME</th>
                    <th>LEVEL</th>
                    <th>NUMBER OF REGISTRANTS</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ( $display_rows as $r ) : ?>
                    <tr>
                        <td><?php echo $r->class_name; ?></td>
                        <td><?php echo $r->level; ?></td>
                        <td><?php echo $r->count; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
