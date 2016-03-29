(function() {
	var $, $$, Accordion, Col, Grid, KdockPanel, MiniShip, MissionPanel, Nav, NavItem, NdockPanel, Panel, ROOT, React, ReactBootstrap, ResourcePanel, Row, Tab, Tabs, TaskPanel, TeitokuPanel, layout, ref, ref1;

	layout = window.layout, ROOT = window.ROOT, $ = window.$, $$ = window.$$, __ = window.__, React = window.React, ReactBootstrap = window.ReactBootstrap;

	Tab = ReactBootstrap.Tab, Tabs = ReactBootstrap.Tabs, Grid = ReactBootstrap.Grid, Col = ReactBootstrap.Col, Row = ReactBootstrap.Row, Accordion = ReactBootstrap.Accordion, Panel = ReactBootstrap.Panel, Nav = ReactBootstrap.Nav, NavItem = ReactBootstrap.NavItem;

	// public shared
	window.CommonIcon = <?php include 'commonicon.js.php'; ?>;

	var MissionPanel = <?php include 'MissionPanel.js.php';?>;
	var NdockPanel = <?php include 'NdockPanel.js.php';?>;
	var KdockPanel = <?php include 'KdockPanel.js.php';?>;
	var TaskPanel = <?php include 'TaskPanel.js.php';?>;
	var MiniShip = <?php include 'MiniShip.js.php';?>;
	var ResourcePanel = <?php include 'ResourcePanel.js.php';?>;
	var TeitokuPanel = <?php include 'TeitokuPanel.js.php';?>;

	return {
		name: 'MainView',
		priority: 0,
		displayName: "dashboard",
		description: '概览面板，提供基本的概览界面',
		reactClass: React.createClass({displayName: "reactClass",
			getInitialState: function() {
				return {
					layout: window.layout,
					key: 1
				};
			},
			handleChangeLayout: function(e) {
				return this.setState({
					layout: e.detail.layout
				});
			},
			componentDidMount: function() {
				return window.addEventListener('layout.change', this.handleChangeLayout);
			},
			componentWillUnmount: function() {
				return window.removeEventListener('layout.change', this.handleChangeLayout);
			},
			shouldComponentUpdate: function(nextProps, nextState) {
				return false;
			},
			render: function() {
				return React.createElement("div", null, React.createElement("link", {
					"rel": "stylesheet",
					"href": 'assets/css/main.css'
				}), (this.state.layout === 'horizontal' || window.doubleTabbed ? React.createElement("div", {
					"className": "panel-col main-area-horizontal"
				}, React.createElement("div", {
					"className": "panel-col teitoku-panel-area"
				}, React.createElement(TeitokuPanel, null)), React.createElement("div", {
					"className": "panel-row bottom-area"
				}, React.createElement("div", {
					"className": "panel-col half bottom-left-area"
				}, React.createElement("div", {
					"className": "panel-col resource-panel resource-panel-area-horizontal",
					"ref": "resourcePanel"
				}, React.createElement(ResourcePanel, null)), React.createElement("div", {
					"className": "miniship miniship-area-horizontal",
					"id": MiniShip.name,
					"ref": "miniship"
				}, React.createElement(MiniShip.reactClass))), React.createElement("div", {
					"className": "panel-col half bottom-left-area"
				}, React.createElement(Panel, {
					"className": "combined-panels panel-col combined-panels-area-horizontal"
				}, React.createElement(Tabs, {
					"defaultActiveKey": 1.,
					"animation": false
				}, React.createElement(Tab, {
					"eventKey": 1.,
					"title": __('Docking')
				}, React.createElement("div", {
					"className": "ndock-panel flex"
				}, React.createElement(NdockPanel, null))), React.createElement(Tab, {
					"eventKey": 2.,
					"title": __('Construction')
				}, React.createElement("div", {
					"className": "kdock-panel flex"
				}, React.createElement(KdockPanel, null))))), React.createElement("div", {
					"className": "mission-panel mission-panel-area-horizontal",
					"ref": "missionPanel"
				}, React.createElement(MissionPanel, null)), React.createElement("div", {
					"className": "task-panel task-panel-area-horizontal",
					"ref": "taskPanel"
				}, React.createElement(TaskPanel, null))))) : React.createElement("div", {
					"className": "panel-row main-area-vertical"
				}, React.createElement("div", {
					"className": "panel-col left-area",
					"style": {
						width: "60%"
					}
				}, React.createElement("div", {
					"className": "panel-col teitoku-panel-area"
				}, React.createElement(TeitokuPanel, null)), React.createElement("div", {
					"className": "panel-row bottom-area"
				}, React.createElement("div", {
					"className": "panel-col half left-bottom-area"
				}, React.createElement("div", {
					"className": "panel-col resource-panel resource-panel-area-vertical",
					"ref": "resourcePanel"
				}, React.createElement(ResourcePanel, null)), React.createElement("div", {
					"className": "panel-col task-panel-area task-panel-area-vertical",
					"ref": "taskPanel"
				}, React.createElement(TaskPanel, null))), React.createElement("div", {
					"className": "panel-col half right-bottom-area"
				}, React.createElement(Panel, {
					"className": "combined-panels panel-col combined-panels-area-vertical"
				}, React.createElement(Tabs, {
					"defaultActiveKey": 1.,
					"animation": false
				}, React.createElement(Tab, {
					"eventKey": 1.,
					"title": __('Docking')
				}, React.createElement("div", {
					"className": "ndock-panel flex"
				}, React.createElement(NdockPanel, null))), React.createElement(Tab, {
					"eventKey": 2.,
					"title": __('Construction')
				}, React.createElement("div", {
					"className": "kdock-panel flex"
				}, React.createElement(KdockPanel, null))))), React.createElement("div", {
					"className": "panel-col mission-panel mission-panel-area-vertical",
					"ref": "missionPanel"
				}, React.createElement(MissionPanel, null))))), React.createElement("div", {
					"className": "miniship panel-col",
					"id": MiniShip.name,
					"ref": "miniship",
					"style": {
						width: "40%"
					}
				}, React.createElement(MiniShip.reactClass)))));
			}
		})
	};

})();
