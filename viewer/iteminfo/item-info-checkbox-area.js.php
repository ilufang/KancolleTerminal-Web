(function() {
	var Button, Col, Divider, Grid, Input, ItemInfoCheckboxArea, Panel, React, ReactBootstrap, Row, __, jQuery;

	React = window.React, ReactBootstrap = window.ReactBootstrap, jQuery = window.jQuery, __ = window.__;

	Panel = ReactBootstrap.Panel, Button = ReactBootstrap.Button, Col = ReactBootstrap.Col, Input = ReactBootstrap.Input, Grid = ReactBootstrap.Grid, Row = ReactBootstrap.Row;

	Divider = React.createClass({displayName: "exports",
		render: function() {
			return React.createElement("div", {
			"className": "divider"
			}, React.createElement("h5", null, this.props.text), React.createElement("hr", null));
		}
	});

	ItemInfoCheckboxArea = React.createClass({displayName: "ItemInfoCheckboxArea",
		getInitialState: function() {
			return {
				itemTypeChecked: [false, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true]
			};
		},
		handleClickCheckbox: function(index) {
			var checkboxes, i, itemTypeChecked, itemTypeVal, j, len;
			checkboxes = [];
			itemTypeChecked = this.state.itemTypeChecked;
			itemTypeChecked[index] = !itemTypeChecked[index];
			for (i = j = 0, len = itemTypeChecked.length; j < len; i = ++j) {
				itemTypeVal = itemTypeChecked[i];
				if (itemTypeChecked[i]) {
					checkboxes.push(i);
				}
			}
			this.setState({
				itemTypeChecked: itemTypeChecked
			});
			return this.props.filterRules(checkboxes);
		},
		handleCilckCheckboxAllButton: function() {
			var checkboxes, i, itemTypeChecked, itemTypeVal, j, len;
			checkboxes = [];
			itemTypeChecked = this.state.itemTypeChecked;
			for (i = j = 0, len = itemTypeChecked.length; j < len; i = ++j) {
				itemTypeVal = itemTypeChecked[i];
				if (i) {
					itemTypeChecked[i] = true;
					checkboxes.push(i);
				}
			}
			this.setState({
				itemTypeChecked: itemTypeChecked
			});
			return this.props.filterRules(checkboxes);
		},
		handleCilckCheckboxNoneButton: function() {
			var checkboxes, i, itemTypeChecked, itemTypeVal, j, len;
			checkboxes = [];
			itemTypeChecked = this.state.itemTypeChecked;
			for (i = j = 0, len = itemTypeChecked.length; j < len; i = ++j) {
				itemTypeVal = itemTypeChecked[i];
				itemTypeChecked[i] = false;
			}
			this.setState({
				itemTypeChecked: itemTypeChecked
			});
			return this.props.filterRules(checkboxes);
		},
		render: function() {
			var index, itemTypeVal, path;
			return React.createElement("div", {
				"id": 'item-info-settings'
			}, React.createElement(Divider, {
				"text": __('Filter Setting')
			}), React.createElement(Grid, {
				"id": 'item-info-filter'
			}, React.createElement(Row, null, (function() {
				var j, len, ref, results;
				ref = this.state.itemTypeChecked;
				results = [];
				for (index = j = 0, len = ref.length; j < len; index = ++j) {
					itemTypeVal = ref[index];
					if (!index) {
						continue;
					}
					results.push(React.createElement(Col, {
						"key": index,
						"xs": 1.
					}, React.createElement("input", {
						"type": 'checkbox',
						"value": index,
						"onChange": this.handleClickCheckbox.bind(this, index),
						"checked": this.state.itemTypeChecked[index],
						"style": {
							verticalAlign: 'middle'
						}
					}), React.createElement("img", {
						"src": 'assets/img/slotitem/'+(index + 100) + ".png"
					})));
				}
				return results;
			}).call(this)), React.createElement(Row, null, React.createElement(Col, {
				"xs": 2.
			}, React.createElement(Button, {
				"className": "filter-button",
				"bsStyle": 'default',
				"bsSize": 'small',
				"onClick": this.handleCilckCheckboxAllButton,
				"block": true
			}, __('Select All'))), React.createElement(Col, {
				"xs": 2.
			}, React.createElement(Button, {
				"className": "filter-button",
				"bsStyle": 'default',
				"bsSize": 'small',
				"onClick": this.handleCilckCheckboxNoneButton,
				"block": true
			}, __('Deselect All'))))));
		}
	});

	return ItemInfoCheckboxArea;

})();
