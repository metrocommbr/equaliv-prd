define([
    'Magento_Ui/js/form/element/abstract',
    'underscore',
    'mageUtils'
], function (Abstract, _, utils) {
    return Abstract.extend({
        defaults: {
            visible:  true,
            label:    '',
            error:    '',
            uid:      utils.uniqueid(),
            disabled: false,
            
            group:      null,
            table:      null,
            field:      null,
            aggregator: null,
            
            groups:      null,
            tables:      null,
            fields:      null,
            aggregators: null,
            
            links:   {
                value: '${ $.provider }:${ $.dataScope }'
                
            },
            imports: {
                schema: '${ $.provider }:schema'
            },
            
            listens: {
                group:      'updateSchema',
                table:      'updateSchema',
                field:      'updateSchema',
                aggregator: 'compileValue'
            }
        },
        
        initialize: function () {
            this._super();
            
            utils.limit(this, 'updateSchema', 5);
            
            _.bindAll(
                this,
                'setGroup',
                'setTable',
                'setField',
                'setAggregator',
                
                'changeColumn'
            );
            
            this.groups(this.schema.group);
            
            var column = _.findWhere(this.schema.column, {
                identifier: this.value()
            });
            
            if (column) {
                this.setGroup(_.findWhere(this.schema.group, {
                    identifier: column.group
                }));
                this.setTable(_.findWhere(this.schema.table, {
                    identifier: column.table
                }));
                this.setField(_.findWhere(this.schema.field, {
                    identifier: column.field
                }));
                this.setAggregator(_.findWhere(this.schema.aggregator, {
                    identifier: column.aggregator
                }));
            }
        },
        
        initObservable: function () {
            this.observe('group')
                .observe('table')
                .observe('field')
                .observe('aggregator');
            
            this.observe('groups', [])
                .observe('tables', [])
                .observe('fields', [])
                .observe('aggregators', []);
            
            return this._super();
        },
        
        updateSchema: function () {
            // allowed columns
            var allowedColumns = _.filter(
                _.where(this.schema.column, this.filters),
                function (column) {
                    return (!this.group() || column.group === this.group().identifier)
                        && (!this.table() || column.table === this.table().identifier)
                        && (!this.field() || column.field === this.field().identifier)
                        && (!this.aggregator() || column.aggregator === this.aggregator().identifier);
                },
                this);
            
            if (this.filtersByColumn) { //dynamic rows
                var filtersByColumn = _.uniq(_.pluck(this.filtersByColumn, 'column'));
                allowedColumns = _.filter(
                    allowedColumns,
                    function (column) {
                        return _.indexOf(filtersByColumn, column.identifier) !== -1;
                    }
                );
            }
            
            var allowedGroups = _.uniq(_.pluck(allowedColumns, 'group'));
            var allowedTables = _.uniq(_.pluck(allowedColumns, 'table'));
            var allowedFields = _.uniq(_.pluck(allowedColumns, 'field'));
            var allowedAggregators = _.uniq(_.pluck(allowedColumns, 'aggregator'));
            
            this.groups(
                _.filter(this.schema.group, function (group) {
                    return _.indexOf(allowedGroups, group.identifier) !== -1;
                })
            );
            
            this.tables(
                _.sortBy(
                    _.filter(this.schema.table, function (table) {
                        return (!this.group() || _.indexOf(table.group, this.group().identifier) !== -1)
                            && _.indexOf(allowedTables, table.identifier) !== -1;
                    }, this),
                    'label'
                )
            );
            
            this.fields(
                _.sortBy(
                    _.filter(this.schema.field, function (field) {
                        return (!this.table() || _.indexOf(field.table, this.table().identifier) !== -1)
                            && _.indexOf(allowedFields, field.identifier) !== -1;
                    }, this),
                    function (field) {
                        return field.isInternal ? 'z' : field.label;
                    }
                )
            );
            
            this.aggregators(
                _.sortBy(
                    _.filter(this.schema.aggregator, function (aggregator) {
                        return (!this.field() || _.lastIndexOf(aggregator.field, this.field().identifier) !== -1)
                            && _.indexOf(allowedAggregators, aggregator.identifier) !== -1;
                    }, this),
                    'label'
                )
            );
        },
        
        compileValue: function () {
            if (!this.group()
                || !this.table()
                || !this.field()
                || !this.aggregator()) {
                return;
            }
            
            var column = _.findWhere(this.schema.column, {
                group:      this.group().identifier,
                table:      this.table().identifier,
                field:      this.field().identifier,
                aggregator: this.aggregator().identifier
            });
            
            if (column) {
                this.value(column.identifier)
            } else {
                this.value('');
            }
        },
        
        changeColumn: function () {
            this.value(null);
            this.setGroup(null);
        },
        
        setGroup: function (group) {
            this.group(group);
            
            this.setTable(null);
        },
        
        setTable: function (table) {
            this.table(table);
            
            this.setField(null);
        },
        
        setField: function (field) {
            this.field(field);
            
            this.setAggregator(null);
        },
        
        setAggregator: function (aggregator) {
            this.aggregator(aggregator);
        }
    });
});