<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap sr-wrap">
    <h1>Cancel Registration by ID</h1>

    <?php if ( ! empty( $error_message ) ) : ?>
        <div class="notice notice-error"><p><?php echo esc_html( $error_message ); ?></p></div>
    <?php endif; ?>

    <?php if ( ! empty( $success_message ) ) : ?>
        <div class="notice notice-success"><p><?php echo esc_html( $success_message ); ?></p></div>
    <?php endif; ?>

    <?php if ( empty( $display_rows ) ) : ?>
        <p>Enter a registration event ID and click View Registration. If information is correct, you can proceed to cancellation.</p>
        <div class="sr-forms">
            <form name="form-cancel-registration-by-id" method="post" class="sr-form">
                <?php wp_nonce_field( 'sr_cancel_registration_by_id', 'sr_cancel_registration_by_id_nonce' ); ?>
                <label for="txt-cancel-registration-by-id">Registration Event ID *</label> 
                <input type="text" name="txt-cancel-registration-by-id" id="txt-cancel-registration-by-id" />
                <input type="submit" name="view-cancel-registration-by-id" value="View Registration" class="button button-primary sr-button" />
            </form>
        </div>
    <?php endif; ?>

    <?php if ( ! empty( $display_rows ) ) : ?>
        <p>Showing registration group for registration #<?php echo esc_html( $input_id ); ?></p>
        <div class="sr-table-wrap">
            <table class="sr-table">
                <thead>
                <tr>
                    <th>REGISTRATION NUMBER</th>
                    <th>EMAIL</th>
                    <th>STATUS</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ( $display_rows as $r ) : ?>
                    <?php $class = ( $r->registration_status == 'cancelled' ) ? 'cancelled' : ''; ?>
                    <tr class="<?php echo esc_attr( $class ); ?>">
                        <td><?php echo esc_html( $r->registration_event_id ); ?></td>
                        <td><?php echo esc_html( $r->email ); ?></td>
                        <td><?php echo $r->registration_status; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <p class="legend">
                <span class="cancelled-legend">Red: Cancelled</span>
            </p>
        </div>

        <form method="post" style="margin-top:1em;">
            <input type="hidden" name="cancel_registration_event_id" value="<?php echo esc_html( $input_id ); ?>" />
            <input type="hidden" name="cancel_input_id" value="<?php echo esc_attr( $input_id ); ?>" />
            <input type="submit" name="confirm-cancel-registration" value="Cancel Registration"
                   class="button button-secondary" 
                   onclick="return confirm('Are you sure you want to cancel this registration group?');" />
        </form>
    <?php endif; ?>
</div>
