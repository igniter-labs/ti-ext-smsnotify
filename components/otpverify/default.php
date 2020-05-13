<div
    class="otp-verify-form"
>
    <?php if ($wasVerifyCodeSent) { ?>
        <div class="form-group">
            <label for="otp_code"><?= $optVerifyCodeLabel; ?></label>
            <input
                type="text"
                name="otp_code"
                id="otp_code"
                class="form-control"
                value=""
            />
        </div>
    <?php } else { ?>
        <div class="form-group">
            <label><?= sprintf($optVerifyTelephoneLabel, $otpVerifyTelephone); ?></label>
            <p class="form-control-static hide"><?= $otpVerifyTelephone ?></p>
        </div>
    <?php } ?>

    <div class="form-group">
        <div class="row">
            <div class="col-12">
                <button
                    type="submit"
                    class="btn btn-primary btn-block btn-lg"
                    data-attach-loading
                ><?= $wasVerifyCodeSent ? $otpVerifyVerifyBtnLabel : $otpVerifyRequestBtnLabel; ?></button>
            </div>
        </div>
    </div>
</div>