var Button, Col, ExpeditionCheck, Grid, Input, LockedCheck, LvCheck, ModernizationCheck, React, ReactBootstrap, RemodelCheck, Row, ShipInfoFilter, TypeCheck, __, config, jQuery, shipTypes;

React = window.React, ReactBootstrap = window.ReactBootstrap, jQuery = window.jQuery, __ = window.__, config = window.config;

Grid = ReactBootstrap.Grid, Row = ReactBootstrap.Row, Col = ReactBootstrap.Col, Input = ReactBootstrap.Input, Button = ReactBootstrap.Button;

shipTypes = ['', '海防艦', '駆逐艦', '軽巡洋艦', '重雷装巡洋艦', '重巡洋艦', '航空巡洋艦', '軽空母', '戦艦', '戦艦', '航空戦艦', '正規空母', '超弩級戦艦', '潜水艦', '潜水空母', '補給艦', '水上機母艦', '揚陸艦', '装甲空母', '工作艦', '潜水母艦', '練習巡洋艦'];

TypeCheck = React.createClass({displayName: "TypeCheck",
	getInitialState: function() {
		return {
			checked: [false, true, true, true, true, true, true, true, true, false, true, true, true, true, true, true, true, true, true, true, true, true, true, true],
			checkedAll: true
		};
	},
	componentDidMount: function() {
		var checked, checkedAll, i, j, k, len, len1, ref, shipType;
		checked = this.state.checked;
		for (i = j = 0, len = shipTypes.length; j < len; i = ++j) {
			shipType = shipTypes[i];
			checked[i] = false;
		}
		ref = this.props.shipTypeBoxes;
		for (k = 0, len1 = ref.length; k < len1; k++) {
			i = ref[k];
			checked[i] = true;
		}
		checkedAll = config.get("plugin.ShipInfo.shipCheckedAll", true);
		return this.setState({
			checked: checked,
			checkedAll: checkedAll
		});
	},
	handleClickCheckbox: function(index) {
		var checkboxes, checked, checkedAll, i, j, len, ref, shipType;
		checkboxes = [];
		ref = this.state, checked = ref.checked, checkedAll = ref.checkedAll;
		checked[index] = !checked[index];
		for (i = j = 0, len = shipTypes.length; j < len; i = ++j) {
			shipType = shipTypes[i];
			if (checked[i]) {
				checkboxes.push(i);
			}
		}
		checkedAll = false;
		config.set("plugin.ShipInfo.shipCheckedAll", checkedAll);
		this.setState({
			checked: checked,
			checkedAll: checkedAll
		});
		return this.props.filterRules('type', checkboxes);
	},
	handleCilckCheckboxAll: function() {
		var checkboxes, checked, checkedAll, i, j, k, len, len1, ref, shipType;
		checkboxes = [];
		ref = this.state, checked = ref.checked, checkedAll = ref.checkedAll;
		if (checkedAll) {
			for (i = j = 0, len = shipTypes.length; j < len; i = ++j) {
				shipType = shipTypes[i];
				checked[i] = false;
			}
			checkedAll = false;
			config.set("plugin.ShipInfo.shipCheckedAll", checkedAll);
			this.setState({
				checked: checked,
				checkedAll: checkedAll
			});
			return this.props.filterRules('type', checkboxes);
		} else {
			for (i = k = 0, len1 = shipTypes.length; k < len1; i = ++k) {
				shipType = shipTypes[i];
				if (i !== 0 && i !== 9) {
					checked[i] = true;
					checkboxes.push(i);
					checkedAll = true;
				}
			}
			config.set("plugin.ShipInfo.shipCheckedAll", checkedAll);
			this.setState({
				checked: checked,
				checkedAll: checkedAll
			});
			return this.props.filterRules('type', checkboxes);
		}
	},
	handleClickFilterButton: function(type) {
		var checkboxes, checked, checkedAll, i, j, len, ref, shipType;
		checkboxes = [];
		ref = this.state, checked = ref.checked, checkedAll = ref.checkedAll;
		checkedAll = false;
		for (i = j = 0, len = shipTypes.length; j < len; i = ++j) {
			shipType = shipTypes[i];
			checked[i] = false;
		}
		switch (type) {
			case 'DD':
				checked[2] = true;
				checkboxes.push(2);
				break;
			case 'CL':
				checked[3] = true;
				checked[4] = true;
				checkboxes.push(3);
				checkboxes.push(4);
				break;
			case 'CA':
				checked[5] = true;
				checked[6] = true;
				checkboxes.push(5);
				checkboxes.push(6);
				break;
			case 'BB':
				checked[8] = true;
				checked[10] = true;
				checked[12] = true;
				checkboxes.push(8);
				checkboxes.push(10);
				checkboxes.push(12);
				break;
			case 'CV':
				checked[7] = true;
				checked[11] = true;
				checked[18] = true;
				checkboxes.push(7);
				checkboxes.push(11);
				checkboxes.push(18);
				break;
			case 'SS':
				checked[13] = true;
				checked[14] = true;
				checkboxes.push(13);
				checkboxes.push(14);
		}
		config.set("plugin.ShipInfo.shipCheckedAll", checkedAll);
		this.setState({
			checked: checked,
			checkedAll: checkedAll
		});
		return this.props.filterRules('type', checkboxes);
	},
	render: function() {
		var index, shipType;
		return React.createElement("div", null, (!this.props.buttonsOnly ? React.createElement("div", null, React.createElement(Row, null, React.createElement(Col, {
			"xs": 2.
		}, React.createElement(Input, {
			"type": 'checkbox',
			"label": __('All'),
			"onChange": this.handleCilckCheckboxAll,
			"checked": this.state.checkedAll
		}))), React.createElement(Row, null, (function() {
			var j, len, results;
			results = [];
			for (index = j = 0, len = shipTypes.length; j < len; index = ++j) {
				shipType = shipTypes[index];
				if (index < 1 || shipType === shipTypes[index - 1]) {
					continue;
				}
				results.push(React.createElement(Col, {
					"key": index,
					"xs": 2.
				}, React.createElement(Input, {
					"type": 'checkbox',
					"label": shipType,
					"key": index,
					"value": index,
					"onChange": this.handleClickCheckbox.bind(this, index),
					"checked": this.state.checked[index]
				})));
			}
			return results;
		}).call(this))) : void 0), React.createElement(Row, null, React.createElement(Col, {
			"xs": 2.
		}, React.createElement(Button, {
			"className": "filter-button",
			"bsStyle": 'default',
			"bsSize": 'small',
			"onClick": this.handleClickFilterButton.bind(this, 'DD'),
			"block": true
		}, __('FilterDD'))), React.createElement(Col, {
			"xs": 2.
		}, React.createElement(Button, {
			"className": "filter-button",
			"bsStyle": 'default',
			"bsSize": 'small',
			"onClick": this.handleClickFilterButton.bind(this, 'CL'),
			"block": true
		}, __('FilterCL'))), React.createElement(Col, {
			"xs": 2.
		}, React.createElement(Button, {
			"className": "filter-button",
			"bsStyle": 'default',
			"bsSize": 'small',
			"onClick": this.handleClickFilterButton.bind(this, 'CA'),
			"block": true
		}, __('FilterCA'))), React.createElement(Col, {
			"xs": 2.
		}, React.createElement(Button, {
			"className": "filter-button",
			"bsStyle": 'default',
			"bsSize": 'small',
			"onClick": this.handleClickFilterButton.bind(this, 'BB'),
			"block": true
		}, __('FilterBB'))), React.createElement(Col, {
			"xs": 2.
		}, React.createElement(Button, {
			"className": "filter-button",
			"bsStyle": 'default',
			"bsSize": 'small',
			"onClick": this.handleClickFilterButton.bind(this, 'CV'),
			"block": true
		}, __('FilterCV'))), React.createElement(Col, {
			"xs": 2.
		}, React.createElement(Button, {
			"className": "filter-button",
			"bsStyle": 'default',
			"bsSize": 'small',
			"onClick": this.handleClickFilterButton.bind(this, 'SS'),
			"block": true
		}, __('FilterSS')))));
	}
});

LvCheck = React.createClass({displayName: "LvCheck",
	getInitialState: function() {
		return {
			checked: [false, false, true]
		};
	},
	handleCilckRadio: function(index) {
		return this.props.filterRules('lv', index);
	},
	render: function() {
		return React.createElement("div", null, React.createElement(Row, null, React.createElement(Col, {
			"xs": 2.,
			"className": 'filter-span'
		}, React.createElement("span", null, __('Level Setting'))), React.createElement(Col, {
			"xs": 2.
		}, React.createElement(Input, {
			"type": 'radio',
			"label": __('All'),
			"onChange": this.handleCilckRadio.bind(this, 0),
			"checked": this.props.keyRadio === 0
		})), React.createElement(Col, {
			"xs": 2.
		}, React.createElement(Input, {
			"type": 'radio',
			"label": __('Lv.1'),
			"onChange": this.handleCilckRadio.bind(this, 1),
			"checked": this.props.keyRadio === 1
		})), React.createElement(Col, {
			"xs": 2.
		}, React.createElement(Input, {
			"type": 'radio',
			"label": __('Above Lv.2'),
			"onChange": this.handleCilckRadio.bind(this, 2),
			"checked": this.props.keyRadio === 2
		}))));
	}
});

LockedCheck = React.createClass({displayName: "LockedCheck",
	getInitialState: function() {
		return {
			checked: [false, true, false]
		};
	},
	handleCilckRadio: function(index) {
		return this.props.filterRules('locked', index);
	},
	render: function() {
		return React.createElement("div", null, React.createElement(Row, null, React.createElement(Col, {
			"xs": 2.,
			"className": 'filter-span'
		}, React.createElement("span", null, __('Lock Setting'))), React.createElement(Col, {
			"xs": 2.
		}, React.createElement(Input, {
			"type": 'radio',
			"label": __('All'),
			"onChange": this.handleCilckRadio.bind(this, 0),
			"checked": this.props.keyRadio === 0
		})), React.createElement(Col, {
			"xs": 2.
		}, React.createElement(Input, {
			"type": 'radio',
			"label": __('Locked'),
			"onChange": this.handleCilckRadio.bind(this, 1),
			"checked": this.props.keyRadio === 1
		})), React.createElement(Col, {
			"xs": 2.
		}, React.createElement(Input, {
			"type": 'radio',
			"label": __('Not Locked'),
			"onChange": this.handleCilckRadio.bind(this, 2),
			"checked": this.props.keyRadio === 2
		}))));
	}
});

ExpeditionCheck = React.createClass({displayName: "ExpeditionCheck",
	getInitialState: function() {
		return {
			checked: [true, false, false]
		};
	},
	handleCilckRadio: function(index) {
		return this.props.filterRules('expedition', index);
	},
	render: function() {
		return React.createElement("div", null, React.createElement(Row, null, React.createElement(Col, {
			"xs": 2.,
			"className": 'filter-span'
		}, React.createElement("span", null, __('Expedition Setting'))), React.createElement(Col, {
			"xs": 2.
		}, React.createElement(Input, {
			"type": 'radio',
			"label": __('All'),
			"onChange": this.handleCilckRadio.bind(this, 0),
			"checked": this.props.keyRadio === 0
		})), React.createElement(Col, {
			"xs": 2.
		}, React.createElement(Input, {
			"type": 'radio',
			"label": __('In Expedition'),
			"onChange": this.handleCilckRadio.bind(this, 1),
			"checked": this.props.keyRadio === 1
		})), React.createElement(Col, {
			"xs": 2.
		}, React.createElement(Input, {
			"type": 'radio',
			"label": __('Not In Expedition'),
			"onChange": this.handleCilckRadio.bind(this, 2),
			"checked": this.props.keyRadio === 2
		}))));
	}
});

ModernizationCheck = React.createClass({displayName: "ModernizationCheck",
	getInitialState: function() {
		return {
			checked: [true, false, false]
		};
	},
	handleCilckRadio: function(index) {
		return this.props.filterRules('modernization', index);
	},
	render: function() {
		return React.createElement("div", null, React.createElement(Row, null, React.createElement(Col, {
			"xs": 2.,
			"className": 'filter-span'
		}, React.createElement("span", null, __('Modernization Setting'))), React.createElement(Col, {
			"xs": 2.
		}, React.createElement(Input, {
			"type": 'radio',
			"label": __('All'),
			"onChange": this.handleCilckRadio.bind(this, 0),
			"checked": this.props.keyRadio === 0
		})), React.createElement(Col, {
			"xs": 2.
		}, React.createElement(Input, {
			"type": 'radio',
			"label": __('Modernization Completed'),
			"onChange": this.handleCilckRadio.bind(this, 1),
			"checked": this.props.keyRadio === 1
		})), React.createElement(Col, {
			"xs": 2.
		}, React.createElement(Input, {
			"type": 'radio',
			"label": __('Modernization Incompleted'),
			"onChange": this.handleCilckRadio.bind(this, 2),
			"checked": this.props.keyRadio === 2
		}))));
	}
});

RemodelCheck = React.createClass({displayName: "RemodelCheck",
	getInitialState: function() {
		return {
			checked: [true, false, false]
		};
	},
	handleCilckRadio: function(index) {
		return this.props.filterRules('remodel', index);
	},
	render: function() {
		return React.createElement("div", null, React.createElement(Row, null, React.createElement(Col, {
			"xs": 2.,
			"className": 'filter-span'
		}, React.createElement("span", null, __('Remodel Setting'))), React.createElement(Col, {
			"xs": 2.
		}, React.createElement(Input, {
			"type": 'radio',
			"label": __('All'),
			"onChange": this.handleCilckRadio.bind(this, 0),
			"checked": this.props.keyRadio === 0
		})), React.createElement(Col, {
			"xs": 2.
		}, React.createElement(Input, {
			"type": 'radio',
			"label": __('Not Remodelable'),
			"onChange": this.handleCilckRadio.bind(this, 1),
			"checked": this.props.keyRadio === 1
		})), React.createElement(Col, {
			"xs": 2.
		}, React.createElement(Input, {
			"type": 'radio',
			"label": __('Remodelable'),
			"onChange": this.handleCilckRadio.bind(this, 2),
			"checked": this.props.keyRadio === 2
		}))));
	}
});

ShipInfoFilter = React.createClass({displayName: "ShipInfoFilter",
	render: function() {
		return React.createElement(Grid, null, React.createElement(TypeCheck, {
			"shipTypeBoxes": this.props.shipTypeBoxes,
			"filterRules": this.props.typeFilterRules,
			"buttonsOnly": !this.props.showDetails
		}), (this.props.showDetails ? React.createElement("div", null, React.createElement(LvCheck, {
			"keyRadio": this.props.lvRadio,
			"filterRules": this.props.lvFilterRules
		}), React.createElement(LockedCheck, {
			"keyRadio": this.props.lockedRadio,
			"filterRules": this.props.lockedFilterRules
		}), React.createElement(ExpeditionCheck, {
			"keyRadio": this.props.expeditionRadio,
			"filterRules": this.props.expeditionFilterRules
		}), React.createElement(ModernizationCheck, {
			"keyRadio": this.props.modernizationRadio,
			"filterRules": this.props.modernizationFilterRules
		}), React.createElement(RemodelCheck, {
			"keyRadio": this.props.remodelRadio,
			"filterRules": this.props.remodelFilterRules
		})) : void 0));
	}
});
