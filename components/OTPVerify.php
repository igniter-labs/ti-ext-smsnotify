<?php

namespace IgniterLabs\SmsNotify\Components;

use IgniterLabs\SmsNotify\Exceptions\OTPException;

class OTPVerify extends \System\Classes\BaseComponent
{
    use \Main\Traits\UsesPage;

    public function defineProperties()
    {
        return [
            'telephoneLabel' => [
                'label' => 'The input label when requesting the OTP code',
                'type' => 'text',
                'default' => lang('igniterlabs.smsnotify::default.otpverify.label_telephone'),
            ],
            'verifyCodeLabel' => [
                'label' => 'The input label when verifying the OTP code',
                'type' => 'text',
                'default' => lang('igniterlabs.smsnotify::default.otpverify.label_verify_code'),
            ],
            'requestBtnLabel' => [
                'label' => 'The button label when requesting the OTP code',
                'type' => 'text',
                'default' => lang('igniterlabs.smsnotify::default.otpverify.button_request_code'),
            ],
            'verifyBtnLabel' => [
                'label' => 'The button label when verifying the OTP code',
                'type' => 'text',
                'default' => lang('igniterlabs.smsnotify::default.otpverify.button_verify_code'),
            ],
            'successPage' => [
                'label' => 'The page to redirect to when login is successful',
                'type' => 'select',
                'default' => 'account/account',
                'options' => [static::class, 'getThemePageOptions'],
            ],
        ];
    }

    public function onRun()
    {
        $this->addJs('js/otpverify.js', 'otpverify-js');

//        $this->page['currentUrl'] = Request::url();
//        $this->page['seoSettings'] = Settings::instance();
    }

    public function onLogin()
    {
        try {
            $accountComponent = $this->controller->findComponentByAlias('account');
            $accountComponent->onLogin();
        }
        catch (OTPException $ex) {
            $this->page['wasVerifyCodeSent'] = $ex->codeWasSent;
            $this->page['otpVerifyTelephone'] = $ex->user->telephone;
        }

        return [
            '[data-control=otp-verify]' => $this->loadForm(),
        ];
    }

    public function onRegister()
    {

    }

    protected function loadForm()
    {
        $this->prepareVars();

        return $this->renderPartial('@default');
    }

    protected function prepareVars()
    {
        $this->page['optVerifyTelephoneLabel'] = $this->property('telephoneLabel');
        $this->page['otpVerifyRequestBtnLabel'] = $this->property('requestBtnLabel');
        $this->page['optVerifyCodeLabel'] = $this->property('verifyCodeLabel');
        $this->page['otpVerifyBtnLabel'] = $this->property('verifyBtnLabel');
    }
}