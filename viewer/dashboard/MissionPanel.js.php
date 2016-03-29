(function() {
	var $, $$, Label, MissionPanel, OverlayTrigger, Panel, ROOT, React, ReactBootstrap, Table, Tooltip, _, join, layout, notify, ref, resolveTime, timeToString;

	ROOT = window.ROOT, layout = window.layout, _ = window._, $ = window.$, $$ = window.$$, React = window.React, ReactBootstrap = window.ReactBootstrap;

	Panel = ReactBootstrap.Panel, Table = ReactBootstrap.Table, Label = ReactBootstrap.Label, OverlayTrigger = ReactBootstrap.OverlayTrigger, Tooltip = ReactBootstrap.Tooltip;

	resolveTime = window.resolveTime;

	notify = window.notify;

	timeToString = function(dateTime) {
		var date;
		date = new Date(dateTime);
		return (date.getHours()) + ":" + (date.getMinutes()) + ":" + (date.getSeconds());
	};

	MissionPanel = React.createClass({displayName: "MissionPanel",
		getInitialState: function() {
			return {
				decks: [
					{
						name: __("No.%s fleet", '0'),
						completeTime: -1,
						countdown: -1,
						mission: null
					}, {
						name: __("No.%s fleet", '1'),
						completeTime: -1,
						countdown: -1,
						mission: null
					}, {
						name: __("No.%s fleet", '2'),
						completeTime: -1,
						countdown: -1,
						mission: null
					}, {
						name: __("No.%s fleet", '3'),
						completeTime: -1,
						countdown: -1,
						mission: null
					}, {
						name: __("No.%s fleet", '4'),
						completeTime: -1,
						countdown: -1,
						mission: null
					}
				],
				notified: [],
				show: true
			};
		},
		shouldComponentUpdate: function(nextProps, nextState) {
			return nextState.show;
		},
		handleVisibleResponse: function(e) {
			var visible;
			visible = e.detail.visible;
			return this.setState({
				show: visible
			});
		},
		handleResponse: function(e) {
			var $missions, body, completeTime, countdown, deck, decks, id, j, len, method, mission, mission_id, notified, path, postBody, ref1, ref2, ref3, ref4, ref5;
			$missions = window.$missions;
			ref1 = e.detail, method = ref1.method, path = ref1.path, body = ref1.body, postBody = ref1.postBody;
			switch (path) {
				case '/kcsapi/api_port/port':
					ref2 = this.state, decks = ref2.decks, notified = ref2.notified;
					ref3 = body.api_deck_port.slice(1, 4);
					for (j = 0, len = ref3.length; j < len; j++) {
						deck = ref3[j];
						id = deck.api_id;
						countdown = -1;
						switch (deck.api_mission[0]) {
							case 0:
								countdown = -1;
								completeTime = -1;
								notified[id] = false;
								break;
							case 1:
								completeTime = deck.api_mission[2];
								countdown = Math.floor((deck.api_mission[2] - new Date()) / 1000);
								break;
							case 2:
								completeTime = 0;
								countdown = 0;
						}
						mission_id = deck.api_mission[1];
						if (mission_id !== 0) {
							mission = $missions[mission_id].api_name;
						} else {
							mission = null;
						}
						decks[id] = {
							name: deck.api_name,
							completeTime: completeTime,
							countdown: countdown,
							mission: mission
						};
					}
					return this.setState({
						decks: decks,
						notified: notified
					});
				case '/kcsapi/api_req_mission/start':
					id = postBody.api_deck_id;
					ref4 = this.state, decks = ref4.decks, notified = ref4.notified;
					decks[id].completeTime = body.api_complatetime;
					decks[id].countdown = Math.floor((body.api_complatetime - new Date()) / 1000);
					mission_id = postBody.api_mission_id;
					decks[id].mission = $missions[mission_id].api_name;
					notified[id] = false;
					return this.setState({
						decks: decks,
						notified: notified
					});
				case '/kcsapi/api_req_mission/return_instruction':
					id = postBody.api_deck_id;
					ref5 = this.state, decks = ref5.decks, notified = ref5.notified;
					decks[id].completeTime = body.api_mission[2];
					decks[id].countdown = Math.floor((body.api_mission[2] - new Date()) / 1000);
					return this.setState({
						decks: decks,
						notified: notified
					});
			}
		},
		updateCountdown: function() {
			var decks, i, j, notified, ref1;
			ref1 = this.state, decks = ref1.decks, notified = ref1.notified;
			for (i = j = 1; j <= 4; i = ++j) {
				if (decks[i].countdown > 0) {
					decks[i].countdown = Math.max(0, Math.floor((decks[i].completeTime - new Date()) / 1000));
					if (decks[i].countdown <= 60 && !notified[i]) {
						notify(decks[i].name + " " + (__('mission complete')), {
							type: 'expedition',
							icon: 'assets/img/operation/expedition.png'
						});
						notified[i] = true;
					}
				}
			}
			return this.setState({
				decks: decks,
				notified: notified
			});
		},
		componentDidMount: function() {
			window.addEventListener('game.response', this.handleResponse);
			window.addEventListener('view.main.visible', this.handleVisibleResponse);
			return setInterval(this.updateCountdown, 1000);
		},
		componentWillUnmount: function() {
			window.removeEventListener('game.response', this.handleResponse);
			window.removeEventListener('view.main.visible', this.handleVisibleResponse);
			return clearInterval(this.updateCountdown, 1000);
		},
		render: function() {
			var i;
			return React.createElement(Panel, {
				"bsStyle": "default"
			}, (function() {
				var j, results;
				results = [];
				for (i = j = 2; j <= 4; i = ++j) {
					results.push(React.createElement("div", {
						"className": "panel-item mission-item",
						"key": i
					}, React.createElement("span", {
						"className": "mission-name"
					}, (this.state.decks[i].mission != null ? "" + this.state.decks[i].mission : __('Ready'))), (this.state.decks[i].countdown > 60 ? React.createElement(OverlayTrigger, {
						"placement": 'left',
						"overlay": React.createElement(Tooltip, {
							"id": "mission-return-by-" + i
						}, React.createElement("strong", null, __("Return by : ")), timeToString(this.state.decks[i].completeTime))
					}, React.createElement(Label, {
						"bsStyle": "primary"
					}, resolveTime(this.state.decks[i].countdown))) : this.state.decks[i].countdown > -1 ? React.createElement(Label, {
						"className": "mission-timer",
						"bsStyle": "success"
					}, resolveTime(this.state.decks[i].countdown)) : React.createElement(Label, {
						"className": "mission-timer",
						"bsStyle": "default"
					}))));
				}
				return results;
			}).call(this));
		}
	});

	return MissionPanel;

})();
