<?php
// File: wp-content/plugins/registration-admin/templates/media-orders.php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap sr-wrap">
    <h1>Registrants with media orders</h1>
    <p>Query: confirmed, not cancelled, with media orders for year <?php echo esc_html( $reg_year ); ?></p>

    <form method="post" class="sr-form">
        <?php wp_nonce_field( 'sr_media_orders', 'sr_media_orders_nonce' ); ?>
        <input type="submit" name="view-media-orders-names-and-addresses" value="Get Names &amp; Addresses" class="button button-primary sr-button" />
    </form>

    <?php if ( empty( $display_rows ) ) : ?>
        <p>No registrants found.</p>
    <?php else : ?>
        <div class="sr-table-wrap">
            <table class="sr-table">
                <thead>
                <tr>
                    <th>NAME</th>
                    <th>ADDRESS1</th>
                    <th>ADDRESS2</th>
                    <th>CITY</th>
                    <th>STATE</th>
                    <th>ZIP</th>
                    <th>COUNTRY</th>
                    <th>EMAIL</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ( $display_rows as $r ) : ?>
                    <tr>
                        <td><?php echo $r->name; ?></td>
                        <td><?php echo $r->address1; ?></td>
                        <td><?php echo $r->address2; ?></td>
                        <td><?php echo $r->city; ?></td>
                        <td><?php echo $r->state; ?></td>
                        <td><?php echo $r->zip; ?></td>
                        <td><?php echo $r->country; ?></td>
                        <td><?php echo $r->email; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
