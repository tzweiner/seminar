<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap sr-wrap">
    <h1>Registrants with requests to rent an instrument</h1>
    <p>Gets confirmed registrants' information for those who have requested instrument rental.</p>

    <div class="sr-forms">
        <form method="post" class="sr-form" style="display:inline-block;margin-right:12px;">
            <?php wp_nonce_field( 'sr_rented', 'sr_rented_nonce' ); ?>
            <input type="submit" name="view-rented" value="Get Rentals" class="button button-primary sr-button" />
        </form>

        <?php if ( ! empty( $display_rows ) ) : ?>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="sr-form" style="display:inline-block;">
                <?php wp_nonce_field( 'sr_rented', 'sr_rented_nonce' ); ?>
                <input type="hidden" name="action" value="sr_export_rented" />
                <input type="submit" name="export-rented-csv" value="Export CSV" class="button sr-button" />
            </form>
        <?php endif; ?>
    </div>

    <?php if ( empty( $display_rows ) ) : ?>
        <p>No registrants found.</p>
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
                    <th>EMAIL</th>
                    <th>INSTRUMENT</th>
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
                        <td><?php echo $r->email; ?></td>
                        <td><?php echo $r->class_name; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
