<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap">
    <h1>Count per Class</h1>
    <p>Gets counts for classes in the database for confirmed registrants only.</p>

    <div class="sr-forms">
        <?php if ( empty( $display_rows ) ) : ?>
        <form method="post" class="sr-form" style="display:inline-block;margin-right:12px;">
            <?php wp_nonce_field( 'sr_counts_per_class', 'sr_counts_per_class_nonce' ); ?>
            <input type="submit" name="view-counts-per-class" value="Get Registrant Counts per Class" class="button button-primary sr-button" />
        </form>
        <?php endif; ?>

        <?php if ( ! empty( $display_rows ) ) : ?>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="sr-form" style="display:inline-block;">
                <?php wp_nonce_field( 'sr_counts_per_class', 'sr_counts_per_class_nonce' ); ?>
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
                    <th>NUMBER OF REGISTRANTS</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ( $display_rows as $r ) : ?>
                    <tr>
                        <td><?php echo $r->class_name; ?></td>
                        <td><?php echo $r->count; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
