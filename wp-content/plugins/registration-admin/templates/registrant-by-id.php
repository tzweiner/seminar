<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap sr-wrap">
    <h1>Registrant By ID</h1>

    <?php if ( ! empty( $error_message ) ) : ?>
        <div class="notice notice-error"><p><?php echo esc_html( $error_message ); ?></p></div>
    <?php endif; ?>

    <?php if ( ! empty( $success_message ) ) : ?>
        <div class="notice notice-success"><p><?php echo esc_html( $success_message ); ?></p></div>
    <?php endif; ?>

    <?php if ( empty( $registrant ) && empty($classes)) : ?>
    <div class="sr-forms">
        <form name="form-registrant-by-id" method="post" class="sr-form">
            <?php wp_nonce_field( 'sr_registrant_by_id', 'sr_registrant_by_id_nonce' ); ?>
            <label for="txt-registrant-by-id">Registrant ID *</label>
            <input type="text" name="txt-registrant-by-id" id="txt-registrant-by-id" />
            <input type="submit" name="view-registrant-by-id" value="Get Registration and Classes" class="button button-primary sr-button" />
        </form>
    </div>
    <?php endif; ?>

    <?php if ( empty( $registrant ) || empty($classes) ) : ?>
        <p>No registrants found.</p>
    <?php else : ?>
        <h2>Registrant Information</h2>
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
<!--                    <th>GALA</th>-->
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
                <?php foreach ( $registrant as $r ) : ?>
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
<!--                        <td>--><?php //echo $r->gala ? 'Yes, ' . $r->meal_option : 'No'; ?><!--</td>-->
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

        <h2>Classes</h2>
        <div class="sr-table-wrap">
            <table class="sr-table">
                <thead>
                <tr>
                    <th>CLASS NAME</th>
                    <th>RENT/BRING</th>
                    <th>LEVEL</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ( $classes as $class ) : ?>
                    <tr>
                        <td><?php echo esc_html( $class->class_name ?? '' ); ?></td>
                        <td><?php echo esc_html( $class->rent  === 1 ? 'Yes' : 'No' ); ?></td>
                        <td><?php echo esc_html( $class->level ?? '' ); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
