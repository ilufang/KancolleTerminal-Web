var Button, Col, Divider, Grid, Input, React, ReactBootstrap, Row, ShipInfoCheckboxArea, ShipInfoFilter, __, jQuery;

React = window.React, ReactBootstrap = window.ReactBootstrap, jQuery = window.jQuery, __ = window.__;

Button = ReactBootstrap.Button, Input = ReactBootstrap.Input, Col = ReactBootstrap.Col, Grid = ReactBootstrap.Grid, Row = ReactBootstrap.Row;

ShipInfoCheckboxArea = React.createClass({displayName: "ShipInfoCheckboxArea",
	getInitialState: function() {
		return {
			filterShow: false,
			sortShow: false
		};
	},
	handleClickAscend: function() {
		return this.props.sortRules(this.props.sortKey, 1);
	},
	handleClickDescend: function() {
		return this.props.sortRules(this.props.sortKey, 0);
	},
	handleKeyChange: function(e) {
		return this.props.sortRules(e.target.value, this.props.order);
	},
	handleSortShow: function() {
		var sortShow;
		sortShow = this.state.sortShow;
		sortShow = !sortShow;
		return this.setState({
			sortShow: sortShow
		});
	},
	handleFilterShow: function() {
		var filterShow;
		filterShow = this.state.filterShow;
		filterShow = !filterShow;
		return this.setState({
			filterShow: filterShow
		});
	},
	render: function() {
		return React.createElement("div", {
			"id": 'ship-info-settings'
		}, React.createElement("div", {
			"onClick": this.handleSortShow
		}, React.createElement(Divider, {
			"text": __('Sort Order Setting'),
			"icon": true,
			"show": this.state.sortShow
		})), React.createElement("div", {
			"className": 'vertical-center',
			"style": (this.state.sortShow ? {
				display: 'block'
			} : {
				display: 'none'
			})
		}, React.createElement(Grid, null, React.createElement(Col, {
			"xs": 2.,
			"className": 'filter-span'
		}, __('Sort By')), React.createElement(Col, {
			"xs": 6.
		}, React.createElement(Input, {
			"id": 'sortbase',
			"type": 'select',
			"defaultValue": this.props.sortKey,
			"value": this.props.sortKey,
			"onChange": this.handleKeyChange
		}, React.createElement("option", {
			"value": 'id'
		}, __('ID')), React.createElement("option", {
			"value": 'type'
		}, __('Class')), React.createElement("option", {
			"value": 'name'
		}, __('Name')), React.createElement("option", {
			"value": 'lv'
		}, __('Level')), React.createElement("option", {
			"value": 'cond'
		}, __('Cond')), React.createElement("option", {
			"value": 'karyoku'
		}, __('Firepower')), React.createElement("option", {
			"value": 'raisou'
		}, __('Torpedo')), React.createElement("option", {
			"value": 'taiku'
		}, __('AA')), React.createElement("option", {
			"value": 'soukou'
		}, __('Armor')), React.createElement("option", {
			"value": 'lucky'
		}, __('Luck')), React.createElement("option", {
			"value": 'sakuteki'
		}, __('LOS')), React.createElement("option", {
			"value": 'repairtime'
		}, __('Repair')))), React.createElement(Col, {
			"xs": 2.
		}, React.createElement(Button, {
			"bsStyle": (this.props.order === 0 ? 'success' : 'default'),
			"bsSize": 'small',
			"onClick": this.handleClickDescend,
			"block": true
		}, (this.props.order === 0 ? '√ ' : ''), " ", __('Descending'))), React.createElement(Col, {
			"xs": 2.
		}, React.createElement(Button, {
			"bsStyle": (this.props.order === 1 ? 'success' : 'default'),
			"bsSize": 'small',
			"onClick": this.handleClickAscend,
			"block": true
		}, (this.props.order === 1 ? '√ ' : ''), " ", __('Ascending'))))), React.createElement("div", {
			"onClick": this.handleFilterShow
		}, React.createElement(Divider, {
			"text": __('Filter Setting'),
			"icon": true,
			"show": this.state.filterShow
		})), React.createElement("div", {
			"id": 'ship-info-filter',
			"style": {
				display: 'block'
			}
		}, React.createElement(ShipInfoFilter, {
			"showDetails": this.state.filterShow,
			"shipTypeBoxes": this.props.shipTypeBoxes,
			"lvRadio": this.props.lvRadio,
			"lockedRadio": this.props.lockedRadio,
			"expeditionRadio": this.props.expeditionRadio,
			"modernizationRadio": this.props.modernizationRadio,
			"remodelRadio": this.props.remodelRadio,
			"typeFilterRules": this.props.filterRules,
			"lvFilterRules": this.props.filterRules,
			"lockedFilterRules": this.props.filterRules,
			"expeditionFilterRules": this.props.filterRules,
			"modernizationFilterRules": this.props.filterRules,
			"remodelFilterRules": this.props.filterRules
		})));
	}
});
