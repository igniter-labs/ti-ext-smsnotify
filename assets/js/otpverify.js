+function ($) {
    "use strict"

    if ($.fn.otpVerify === undefined)
        $.fn.otpVerify = {}

    var OtpVerify = function (element, options) {
        this.$el = $(element)
        this.options = options || {}
        this.$form = this.$el.closest('form')
        this.$otpVerifyBtn = $(document).find(this.options.buttonSelector)
        // this.$modalRootElement = $('<div/>', this.options.attributes)

        this.init()
    }

    OtpVerify.prototype.init = function () {
        // $(document).on('click', '[data-otp-verify-control]', $.proxy(this.onControlClick, this))

        $(document)
            // .on('submit', this.$form, $.proxy(this.onSubmitOtpVerifyForm, this))
        //     .on('ajaxPromise', this.options.buttonSelector, function () {
        //         $(this).prop('disabled', true)
        //     })
        //     .on('ajaxFail ajaxDone', this.options.buttonSelector, function () {
        //         $(this).prop('disabled', false)
        //     })
        //     .on('submit', this.options.formSelector, $.proxy(this.onSubmitOtpVerifyForm, this))
        //     .on('ajaxDone', '[data-control="otp-verify"]', $.proxy(this.onLoadOtpVerifyForm, this))
    }

    OtpVerify.prototype.onLoadOtpVerifyForm = function (json) {
        var self = this

        // setTimeout(function () {
        //     self.$form.html(json.result)
        // }, 0)
        // event.preventDefault();
        //
        // this.$form.request(this.options.request).done(function (json) {
        // })
    }

    OtpVerify.DEFAULTS = {
        alias: 'otpverify',
        request: undefined,
        buttonSelector: '.checkout-btn',
    }

    // PLUGIN DEFINITION
    // ============================

    var old = $.fn.otpVerify

    $.fn.otpVerify = function (option) {
        var args = arguments

        return this.each(function () {
            var $this = $(this)
            var data = $this.data('ti.otpVerify')
            var options = $.extend({}, OtpVerify.DEFAULTS, $this.data(), typeof option == 'object' && option)
            if (!data) $this.data('ti.otpVerify', (data = new OtpVerify(this, options)))
            if (typeof option == 'string') data[option].apply(data, args)
        })
    }

    $.fn.otpVerify.Constructor = OtpVerify

    $.fn.otpVerify.noConflict = function () {
        $.fn.otpVerify = old
        return this
    }

    $(document).render(function () {
        $('[data-control="otp-verify"]').otpVerify()
    })
}(window.jQuery)