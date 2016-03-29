(function() {
	var $, $$, $shipTypes, $ships, Alert, Button, ButtonGroup, FontAwesome, Label, Overlay, OverlayTrigger, PaneBody, PaneBodyMini, Panel, Popover, ProgressBar, ROOT, React, ReactBootstrap, Tooltip, _, __, __n, _ships, combined, escapeId, getDeckState, getStyle, goback, inBattle, join, path, ref, ref1, ref2, relative, toggleModal, towId,
		indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };


	_ = window._, __ = window.__, $ = window.$, $$ = window.$$, React = window.React, ReactBootstrap = window.ReactBootstrap, ROOT = window.ROOT, FontAwesome = window.FontAwesome, toggleModal = window.toggleModal;

	$ships = window.$ships, $shipTypes = window.$shipTypes, _ships = window._ships;

	Button = ReactBootstrap.Button, ButtonGroup = ReactBootstrap.ButtonGroup;

	ProgressBar = ReactBootstrap.ProgressBar, OverlayTrigger = ReactBootstrap.OverlayTrigger, Tooltip = ReactBootstrap.Tooltip, Alert = ReactBootstrap.Alert, Overlay = ReactBootstrap.Overlay, Label = ReactBootstrap.Label, Panel = ReactBootstrap.Panel, Popover = ReactBootstrap.Popover;

	inBattle = [false, false, false, false];

	goback = {};

	combined = false;

	escapeId = -1;

	towId = -1;

	// Shared components

	window.Slotitems = <?php include 'Slotitems.js.php'; ?>;
	window.StatusLabel = <?php include 'Statuslabel.js.php';?>;
	window.TopAlert = <?php include 'Topalert.js.php';?>;


	PaneBody = <?php include 'PaneBody.js.php';?>;
	PaneBodyMini = <?php include 'PaneBodyMini.js.php' ?>

	getStyle = function(state) {
		if (indexOf.call([0, 1, 2, 3, 4, 5], state) >= 0) {
			return ['success', 'warning', 'danger', 'info', 'primary', 'default'][state];
		} else {
			return 'default';
		}
	};

	getDeckState = function(deck) {
		var j, len, ref3, ship, shipId, shipInfo, state;
		state = 0;
		$ships = window.$ships, _ships = window._ships;
		if (inBattle[deck.api_id - 1]) {
			state = Math.max(state, 5);
		}
		if (deck.api_mission[0] > 0) {
			state = Math.max(state, 4);
		}
		ref3 = deck.api_ship;
		for (j = 0, len = ref3.length; j < len; j++) {
			shipId = ref3[j];
			if (shipId === -1) {
				continue;
			}
			ship = _ships[shipId];
			shipInfo = $ships[ship.api_ship_id];
			if (ship.api_cond < 20 || ship.api_nowhp / ship.api_maxhp < 0.25) {
				state = Math.max(state, 2);
			} else if (ship.api_cond < 40 || ship.api_nowhp / ship.api_maxhp < 0.5) {
				state = Math.max(state, 1);
			}
			if (ship.api_fuel / shipInfo.api_fuel_max < 0.99 || ship.api_bull / shipInfo.api_bull_max < 0.99) {
				state = Math.max(state, 1);
			}
			if (indexOf.call(window._ndocks, shipId) >= 0) {
				state = Math.max(state, 3);
			}
		}
		return state;
	};

	return {
		name: 'MiniShip',
		priority: 100000.1,
		displayName: "MiniShip",
		description: '舰队展示页面，展示舰队详情信息',
		reactClass: React.createClass({displayName: "reactClass",
			getInitialState: function() {
				return {
					show: false,
					names: ["" + (__('I')), "" + (__('II')), "" + (__('III')), "" + (__('IV'))],
					fullnames: [__('No.%s fleet', 1), __('No.%s fleet', 2), __('No.%s fleet', 3), __('No.%s fleet', 4)],
					states: [-1, -1, -1, -1],
					decks: [],
					activeDeck: 0,
					dataVersion: 0
				};
			},
			showDataVersion: 0,
			nowTime: 0,
			/*
			componentWillUpdate: function(nextProps, nextState) {
				return this.nowTime = (new Date()).getTime();
			},
			componentDidUpdate: function(prevProps, prevState) {
				var cur;
				cur = (new Date()).getTime();
				if (process.env.DEBUG != null) {
					return console.log("the cost of ship-module's render: " + (cur - this.nowTime) + "ms");
				}
			},
			*/
			shouldComponentUpdate: function(nextProps, nextState) {
				if (nextProps.selectedKey === this.props.index && nextState.dataVersion !== this.showDataVersion) {
					this.showDataVersion = nextState.dataVersion;
					return true;
				}
				if (this.state.decks.length === 0 && nextState.decks.length !== 0) {
					return true;
				}
				return false;
			},
			handleClick: function(idx) {
				if (idx !== this.state.activeDeck) {
					return this.setState({
						activeDeck: idx,
						dataVersion: this.state.dataVersion + 1
					});
				}
			},
			toggle: function(e) {
				var event;
				event = new CustomEvent('view.main.visible', {
					bubbles: true,
					cancelable: false,
					detail: {
						visible: this.state.show
					}
				});
				window.dispatchEvent(event);
				return e.preventDefault();
			},
			handleMiniShipChange: function(e) {
				e.preventDefault();
				if (e.detail.visible === this.state.show) {
					return this.setState({
						show: !this.state.show,
						dataVersion: this.state.dataVersion + 1
					});
				}
			},
			handleResponse: function(e) {
				var _slotitems, body, damagedShips, deck, deckId, decks, escapeIdx, flag, fullnames, idx, j, k, l, len, len1, method, postBody, ref3, ref4, ref5, ref6, safe, ship, shipId, slotId, states, towIdx;
				ref3 = e.detail, method = ref3.method, path = ref3.path, body = ref3.body, postBody = ref3.postBody;
				fullnames = this.state.fullnames;
				flag = true;
				switch (path) {
					case '/kcsapi/api_port/port':
						fullnames = body.api_deck_port.map(function(e) {
							return e.api_name;
						});
						inBattle = [false, false, false, false];
						goback = {};
						combined = (body.api_combined_flag != null) && body.api_combined_flag > 0;
						break;
					case '/kcsapi/api_req_hensei/change':
					case '/kcsapi/api_req_hokyu/charge':
					case '/kcsapi/api_get_member/deck':
					case '/kcsapi/api_get_member/ship_deck':
					case '/kcsapi/api_get_member/ship2':
					case '/kcsapi/api_get_member/ship3':
					case '/kcsapi/api_req_kousyou/destroyship':
					case '/kcsapi/api_req_kaisou/powerup':
					case '/kcsapi/api_req_nyukyo/start':
					case '/kcsapi/api_req_nyukyo/speedchange':
					case '/kcsapi/api_req_hensei/preset_select':
					case '/kcsapi/api_req_kaisou/slot_exchange_index':
						true;
						break;
					case '/kcsapi/api_req_sortie/battleresult':
					case '/kcsapi/api_req_combined_battle/battleresult':
						decks = this.state.decks;
						if ((body.api_escape_flag != null) && body.api_escape_flag > 0) {
							escapeIdx = body.api_escape.api_escape_idx[0] - 1;
							towIdx = body.api_escape.api_tow_idx[0] - 1;
							escapeId = decks[Math.floor(escapeIdx / 6)].api_ship[escapeIdx % 6];
							towId = decks[Math.floor(towIdx / 6)].api_ship[towIdx % 6];
						}
						break;
					case '/kcsapi/api_req_combined_battle/goback_port':
						decks = this.state.decks;
						_ships = window._ships;
						if (escapeId !== -1 && towId !== -1) {
							goback[escapeId] = goback[towId] = true;
						}
						break;
					case '/kcsapi/api_req_map/start':
					case '/kcsapi/api_req_map/next':
						if (path === '/kcsapi/api_req_map/start') {
							if (combined && parseInt(postBody.api_deck_id) === 1) {
								deckId = 0;
								inBattle[0] = inBattle[1] = true;
							} else {
								deckId = parseInt(postBody.api_deck_id) - 1;
								inBattle[deckId] = true;
							}
						}
						escapeId = towId = -1;
						ref4 = this.state, decks = ref4.decks, states = ref4.states;
						_ships = window._ships, _slotitems = window._slotitems;
						damagedShips = [];
						for (deckId = j = 0; j <= 3; deckId = ++j) {
							if (!inBattle[deckId]) {
								continue;
							}
							deck = decks[deckId];
							ref5 = deck.api_ship;
							for (idx = k = 0, len = ref5.length; k < len; idx = ++k) {
								shipId = ref5[idx];
								if (shipId === -1 || idx === 0) {
									continue;
								}
								ship = _ships[shipId];
								if (ship.api_nowhp / ship.api_maxhp < 0.250001 && !goback[shipId]) {
									safe = false;
									ref6 = ship.api_slot.concat(ship.api_slot_ex || -1);
									for (l = 0, len1 = ref6.length; l < len1; l++) {
										slotId = ref6[l];
										if (slotId === -1) {
											continue;
										}
										if (_slotitems[slotId].api_type[3] === 14) {
											safe = true;
										}
									}
									if (!safe) {
										damagedShips.push("Lv. " + ship.api_lv + " - " + ship.api_name);
									}
								}
							}
						}
						if (damagedShips.length > 0) {
							toggleModal(__('Attention!'), damagedShips.join(' ') + __('is heavily damaged!'));
						}
						break;
					default:
						flag = false;
				}
				if (!flag) {
					return;
				}
				decks = window._decks;
				states = decks.map(function(deck) {
					return getDeckState(deck);
				});
				return this.setState({
					fullnames: fullnames,
					decks: decks,
					states: states,
					dataVersion: this.state.dataVersion + 1
				});
			},
			componentDidMount: function() {
				window.addEventListener('game.response', this.handleResponse);
				return window.addEventListener('view.main.visible', this.handleMiniShipChange);
			},
			componentWillUnmount: function() {
				window.removeEventListener('game.response', this.handleResponse);
				window.removeEventListener('view.main.visible', this.handleMiniShipChange);
				if (this.interval != null) {
					return this.interval = clearInterval(this.interval);
				}
			},
			render: function() {
				var deck, i;
				return React.createElement("div", {
					"style": {
						height: '100%'
					},
					"onDoubleClick": this.toggle
				}, React.createElement(Panel, {
					"bsStyle": "default",
					"style": {
						minHeight: 322,
						height: 'calc(100% - 8px)'
					}
				}, React.createElement("link", {
					"rel": "stylesheet",
					"href": 'assets/css/miniship.css'
				}), React.createElement("div", {
					"className": "panel-row"
				}, React.createElement(ButtonGroup, {
					"bsSize": "xsmall"
				}, (function() {
					var j, results;
					results = [];
					for (i = j = 0; j <= 3; i = ++j) {
						results.push(React.createElement(Button, {
							"key": i,
							"bsStyle": getStyle(this.state.states[i]),
							"onClick": this.handleClick.bind(this, i),
							"className": (this.state.activeDeck === i ? 'active' : '')
						}, this.state.names[i]));
					}
					return results;
				}).call(this)), React.createElement(Button, {
					"bsSize": "xsmall",
					"onClick": this.toggle
				}, React.createElement(FontAwesome, {
					"name": 'external-link'
				}))), (function() {
					var j, len, ref3, results;
					ref3 = this.state.decks;
					results = [];
					for (i = j = 0, len = ref3.length; j < len; i = ++j) {
						deck = ref3[i];
						results.push(React.createElement("div", {
							"className": "ship-deck",
							"className": (this.state.activeDeck === i ? 'show' : 'hidden'),
							"key": i
						}, React.createElement(PaneBodyMini, {
							"key": i,
							"show": !this.state.show,
							"deckIndex": i,
							"deck": this.state.decks[i],
							"activeDeck": this.state.activeDeck,
							"deckName": this.state.names[i]
						})));
					}
					return results;
				}).call(this)), React.createElement(Panel, {
					"id": "ShipView",
					"className": "" + (window.doubleTabbed ? 'ship-panel-half' : 'ship-panel-full') + (this.state.show ? '' : '-hidden') + " " + ((!window.doubleTabbed) && (window.layout === 'vertical') ? 'toright' : 'toleft') + " " + ((window.layout === 'vertical') && (!window.doubleTabbed) ? !this.state.show ? 'top-vertical' : void 0 : !this.state.show ? 'top-horizontal' : void 0)
				}, React.createElement("link", {
					"rel": "stylesheet",
					"href": 'assets/css/ship.css'
				}), React.createElement("div", {
					"className": "panel-row"
				}, React.createElement(ButtonGroup, {
					"className": "fleet-name-button"
				}, (function() {
					var j, results;
					results = [];
					for (i = j = 0; j <= 3; i = ++j) {
						results.push(React.createElement(Button, {
							"key": i,
							"bsSize": "small",
							"bsStyle": getStyle(this.state.states[i]),
							"onClick": this.handleClick.bind(this, i),
							"className": (this.state.activeDeck === i ? 'active' : '')
						}, this.state.fullnames[i]));
					}
					return results;
				}).call(this)), React.createElement(ButtonGroup, {
					"style": {
						width: 50
					}
				}, React.createElement(Button, {
					"bsSize": "small",
					"onClick": this.toggle
				}, React.createElement(FontAwesome, {
					"name": 'external-link-square',
					"rotate": 180
				})))), (function() {
					var j, len, ref3, results;
					ref3 = this.state.decks;
					results = [];
					for (i = j = 0, len = ref3.length; j < len; i = ++j) {
						deck = ref3[i];
						results.push(React.createElement("div", {
							"className": "ship-deck " + (this.state.activeDeck === i ? 'show' : 'hidden'),
							"key": i
						}, React.createElement(PaneBody, {
							"key": i,
							"show": this.state.show,
							"deckIndex": i,
							"deck": this.state.decks[i],
							"activeDeck": this.state.activeDeck,
							"deckName": this.state.fullnames[i]
						})));
					}
					return results;
				}).call(this)));
			}
		})
	};

})();
