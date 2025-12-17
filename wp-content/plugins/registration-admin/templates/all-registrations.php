<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap sr-wrap">
    <h1>All Registrations</h1>

    <div class="sr-forms">
        <form method="post" class="sr-form" style="display:inline-block;margin-right:12px;">
            <?php wp_nonce_field( 'sr_all_registrations', 'sr_all_registrations_nonce' ); ?>
            <input type="submit" name="view-all-registrations" value="Get All Registrations" class="button button-primary sr-button" />
        </form>

        <?php if ( ! empty( $display_rows ) ) : ?>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="sr-form" style="display:inline-block;">
                <?php wp_nonce_field( 'sr_all_registrations', 'sr_all_registrations_nonce' ); ?>
                <input type="hidden" name="action" value="sr_export_all_registrations" />
                <input type="submit" name="export-all-registrations-csv" value="Export CSV (Excel)" class="button sr-button" />
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
                    <th>PHONE</th>
                    <th>EMAIL</th>
                    <th>EMERGENCY</th>
                    <th>NUMBER OF DAYS</th>
                    <th>GALA</th>
                    <th>AGE GROUP</th>
                    <th>EEFC MEMBER</th>
                    <th>BULGARIAN</th>
                    <th>TRANSPORTATION</th>
                    <th>VIDEO REQUESTED</th>
                    <th>BALANCE</th>
                    <th>PAYMENT OPTION</th>
                    <th>REGISTRATION DATE</th>
                    <th>REGISTRATION CONFIRMATION EMAIL</th>
                    <th>REGISTRATION STATUS</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ( $display_rows as $r ) : ?>
                    <tr class="<?php if ($r->registration_status === 'cancelled') echo 'cancelled'; ?>">
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
                        <td><?php echo $r->emergency; ?></td>
                        <td><?php echo $r->num_days; ?></td>
                        <td><?php echo $r->gala ? 'Yes, ' . $r->meal_option : 'No'; ?></td>
                        <td><?php echo $r->age; ?></td>
                        <td><?php echo $r->is_eefc ? 'Yes' : 'No'; ?></td>
                        <td><?php echo $r->is_bulgarian ? 'Yes' : 'No'; ?></td>
                        <td><?php echo $r->transport; ?></td>
                        <td><?php echo $r->media ? 'Yes' : 'No'; ?></td>
                        <td><?php echo $r->balance; ?></td>
                        <td><?php echo $r->payment; ?></td>
                        <td><?php echo $r->registration_date; ?></td>
                        <td><?php echo $r->registration_email_sent ? $r->registration_email_sent_timestamp : 'No'; ?></td>
                        <td><?php echo $r->registration_status; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
