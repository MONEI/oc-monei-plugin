<?php namespace MONEI\MONEI;

use MONEI\MONEI\Classes\Helper;
use MONEI\MONEI\Classes\MONEIGateway;
use MONEI\MONEI\Components\PaymentForm;
use MONEI\MONEI\Models\Settings;
use System\Classes\PluginBase;
use Backend;
use Event;

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
        ];
    }
    
    public function registerSettings()
    {
        return [
//            'orders' => [
//                'label'       => 'All Orders',
//                'description' => 'Configure available orders.',
//                'icon'        => 'icon-usd',
//                'url'         => Backend::url('successivesoftware/paypal/orders'),
//                'category'    => 'Paypal',
//                'order'       => 500,
//            ],
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

    public function boot()
    {
    }
    
}
