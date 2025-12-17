<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap sr-wrap">
    <h1>Transportation Counts</h1>

    <?php if ( empty( $display_rows ) ) : ?>
    <div class="sr-forms">
        <form method="post" class="sr-form" style="display:inline-block;margin-right:12px;">
            <?php wp_nonce_field( 'sr_transportation_counts', 'sr_transportation_counts_nonce' ); ?>
            <input type="submit" name="view-transportation-counts" value="Get Transportation Counts" class="button button-primary sr-button" />
        </form>
    </div>
    <p>No data found for transportation.</p>
    <?php else : ?>
        <div class="sr-table-wrap">
            <table class="sr-table">
                <thead>
                <tr>
                    <th>TRANSPORTATION OPTION</th>
                    <th>NUMBER OF REGISTRANTS</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ( $display_rows as $r ) : ?>
                    <tr>
                        <td><?php echo $r->transport_option; ?></td>
                        <td><?php echo $r->count; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
