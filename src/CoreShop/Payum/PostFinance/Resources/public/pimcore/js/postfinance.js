/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.provider.gateways.postfinance');
coreshop.provider.gateways.postfinance = Class.create(coreshop.provider.gateways.abstract, {

    /**
     * @see https://e-payment.postfinance.ch/ncol/param_cookbook.asp
     */
    optionalFields: [
        'PM',
        'PMLISTTYPE',
        'BRAND',
        'TITLE',
        'BGCOLOR',
        'TXTCOLOR',
        'TBLBGCOLOR',
        'TBLTXTCOLOR',
        'BUTTONBGCOLOR',
        'BUTTONTXTCOLOR',
        'LOGO',
        'FONTTYPE',
        'TP',
        'ALIAS',
        'ALIASOPERATION',
        'ALIASUSAGE',
        'ALIASPERSISTEDAFTERUSE',
        'DEVICE',
        'HOMEURL'
    ],

    getLayout: function (config) {

        var storeEnvironments = new Ext.data.ArrayStore({
            fields: ['environment', 'environmentName'],
            data: [
                ['test', 'Test'],
                ['production', 'Production']
            ]
        });

        var optionalFields = [{
            xtype: 'label',
            anchor: '100%',
            style: 'display:block; padding:5px; background:#f5f5f5; border:1px solid #eee; font-weight: 300;',
            html: 'Parameter Cookbook: <a href="https://e-payment.postfinance.ch/ncol/param_cookbook.asp" target="_blank">https://e-payment.postfinance.ch/ncol/param_cookbook.aspa</a>'
        }];

        Ext.Array.each(this.optionalFields, function (field) {
            var value = config.optionalParameters && config.optionalParameters[field] ? config.optionalParameters[field] : '';
            optionalFields.push({
                xtype: 'textfield',
                fieldLabel: field,
                name: 'gatewayConfig.config.optionalParameters.' + field,
                length: 255,
                flex: 1,
                labelWidth: 250,
                anchor: '100%',
                value: value
            })
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
                xtype: 'fieldset',
                title: t('postfinance.config.optional_parameter'),
                collapsible: true,
                collapsed: true,
                autoHeight: true,
                labelWidth: 250,
                anchor: '100%',
                flex: 1,
                defaultType: 'textfield',
                items: optionalFields
            }
        ];
    }
});
