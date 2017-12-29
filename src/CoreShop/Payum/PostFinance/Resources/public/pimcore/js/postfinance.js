/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.provider.gateways.postfinance');
coreshop.provider.gateways.postfinance = Class.create(coreshop.provider.gateways.abstract, {

    getLayout: function (config) {

        var storeEnvironments = new Ext.data.ArrayStore({
            fields: ['environment', 'environmentName'],
            data: [
                ['test', 'Test'],
                ['production', 'Production']
            ]
        });

        return [
            {
                xtype: 'combobox',
                fieldLabel: t('postfinance.config.environment'),
                name: 'gatewayConfig.config.environment',
                value: config.environment ? config.environment : '',
                store: storeEnvironments,
                triggerAction: 'all',
                valueField: 'environment',
                displayField: 'environmentName',
                mode: 'local',
                forceSelection: true,
                selectOnFocus: true
            },
            {
                xtype: 'textfield',
                fieldLabel: t('postfinance.config.sha_in_passphrase'),
                name: 'gatewayConfig.config.shaInPassphrase',
                length: 255,
                value: config.shaInPassphrase ? config.shaInPassphrase : ""
            },
            {
                xtype: 'textfield',
                fieldLabel: t('postfinance.config.sha_out_passphrase'),
                name: 'gatewayConfig.config.shaOutPassphrase',
                length: 255,
                value: config.shaOutPassphrase ? config.shaOutPassphrase : ""
            },
            {
                xtype: 'textfield',
                fieldLabel: t('postfinance.config.pspid'),
                name: 'gatewayConfig.config.pspid',
                length: 255,
                value: config.pspid ? config.pspid : ""
            },
            {
                xtype: 'textfield',
                fieldLabel: t('postfinance.config.default_parameters'),
                name: 'gatewayConfig.config.defaultParameters',
                length: 255,
                value: config.defaultParameters ? config.defaultParameters : ""
            }
        ];
    }
});
