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
                fieldLabel: 'environment',
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
                fieldLabel: 'sha_in_passphrase',
                name: 'gatewayConfig.config.shaInPassphrase',
                length: 255,
                value: config.shaInPassphrase ? config.shaInPassphrase : ""
            },
            {
                xtype: 'textfield',
                fieldLabel: 'sha_out_passphrase',
                name: 'gatewayConfig.config.shaOutPassphrase',
                length: 255,
                value: config.shaOutPassphrase ? config.shaOutPassphrase : ""
            },
            {
                xtype: 'textfield',
                fieldLabel: 'pspid',
                name: 'gatewayConfig.config.pspid',
                length: 255,
                value: config.pspid ? config.pspid : ""
            },
            {
                xtype: 'textfield',
                fieldLabel: 'default_parameters',
                name: 'gatewayConfig.config.defaultParameters',
                length: 255,
                value: config.defaultParameters ? config.defaultParameters : ""
            }
        ];
    }
});
