<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap sr-wrap">
    <h1>Confirmed Registrants</h1>
    <p>List for year <?php echo esc_html( $reg_year ); ?></p>

    <div class="sr-forms">
        <form method="post" class="sr-form" style="display:inline-block;margin-right:12px;">
            <?php wp_nonce_field( 'sr_confirmed', 'sr_confirmed_nonce' ); ?>
            <input type="submit" name="view-confirmed-names-and-addresses" value="Get Names &amp; Addresses" class="button button-primary sr-button" />
        </form>

        <?php if ( ! empty( $display_rows ) ) : ?>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="sr-form" style="display:inline-block;">
                <?php wp_nonce_field( 'sr_confirmed', 'sr_confirmed_nonce' ); ?>
                <input type="hidden" name="action" value="sr_export_confirmed_registrants" />
                <input type="submit" name="export-confirmed-csv" value="Export CSV (Excel)" class="button sr-button" />
            </form>
        <?php endif; ?>
    </div>

    <?php if ( empty( $display_rows ) ) : ?>
        <p>No registrants found or results not yet generated.</p>
    <?php else : ?>
        <div class="sr-table-wrap">
            <table class="sr-table">
                <thead>
                <tr>
                    <th>NAME</th>
                    <th>REG NUMBER</th>
                    <th>ADDRESS1</th>
                    <th>ADDRESS2</th>
                    <th>CITY</th>
                    <th>STATE</th>
                    <th>ZIP</th>
                    <th>COUNTRY</th>
                    <th>PHONE</th>
                    <th>EMAIL</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ( $display_rows as $r ) : ?>
                    <tr>
                        <td><?php echo $r->name; ?></td>
                        <td><?php echo $r->registration_number; ?></td>
                        <td><?php echo $r->address1; ?></td>
                        <td><?php echo $r->address2; ?></td>
                        <td><?php echo $r->city; ?></td>
                        <td><?php echo $r->state; ?></td>
                        <td><?php echo $r->zip; ?></td>
                        <td><?php echo $r->country; ?></td>
                        <td><?php echo $r->phone; ?></td>
                        <td><?php echo $r->email; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
