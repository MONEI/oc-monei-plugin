<?php namespace MONEI\MONEI;

use MONEI\MONEI\Components\PaymentForm;
use MONEI\MONEI\Components\SuccessPage;
use MONEI\MONEI\Models\Settings;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{

    public function pluginDetails()
    {
        return [
            'name'        => 'monei.monei::lang.plugin.name',
            'description' => 'monei.monei::lang.plugin.description',
            'author'      => 'MONEI',
            'icon'        => 'icon-usd',
        ];
    }

    public function registerComponents()
    {
        return [
            PaymentForm::class => 'MONEIPaymentForm',
            SuccessPage::class => 'MONEISuccessPage',
        ];
    }
    
    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'monei.monei::lang.settings.label',
                'description' => 'monei.monei::lang.settings.description',
                'icon'        => 'icon-cog',
                'category'    => 'MONEI',
                'class'       => Settings::class,
                'order'       => 510,
                'permissions' => ['monei.monei.access_settings'],
            ],
        ];
    }
    
    public function registerPermissions()
    {
        return [
            'monei.monei.access_settings' => ['tab' => 'settings', 'label' => 'Settings'],
        ];
    }
    
}
