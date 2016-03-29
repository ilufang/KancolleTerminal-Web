(function() {
	var $, $$, APPDATA_PATH, CSON, Label, OverlayTrigger, Panel, ROOT, React, ReactBootstrap, Table, TaskPanel, Tooltip, _, __n, activateQuestRecord, clearQuestRecord, emptyTask, error, firstBattle, fs, getCategory, getStyleByPercent, getStyleByProgress, getToolTip, inactivateQuestRecord, isDifferentDay, isDifferentMonth, isDifferentWeek, join, layout, lockedTask, memberId, prevTime, questGoals, questRecord, ref, syncQuestRecord, updateQuestRecord, zero,
		indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

	ROOT = window.ROOT, APPDATA_PATH = window.APPDATA_PATH, layout = window.layout, _ = window._, $ = window.$, $$ = window.$$, React = window.React, ReactBootstrap = window.ReactBootstrap;

	Panel = ReactBootstrap.Panel, Table = ReactBootstrap.Table, Label = ReactBootstrap.Label, OverlayTrigger = ReactBootstrap.OverlayTrigger, Tooltip = ReactBootstrap.Tooltip;

	zero = 331200000;

	isDifferentDay = function(time1, time2) {
		var day1, day2;
		day1 = Math.floor((time1 - zero) / 86400000);
		day2 = Math.floor((time2 - zero) / 86400000);
		return day1 !== day2;
	};

	isDifferentWeek = function(time1, time2) {
		var week1, week2;
		week1 = Math.floor((time1 - zero) / 604800000);
		week2 = Math.floor((time2 - zero) / 604800000);
		return week1 !== week2;
	};

	isDifferentMonth = function(time1, time2) {
		var date1, date2;
		date1 = new Date(time1 + 14400000);
		date2 = new Date(time2 + 14400000);
		return date1.getUTCMonth() !== date2.getUTCMonth() || date1.getUTCFullYear() !== date2.getUTCFullYear();
	};

	prevTime = (new Date()).getTime();

	getCategory = function(api_category) {
		switch (api_category) {
			case 0:
				return '#ffffff';
			case 1:
				return '#19BB2E';
			case 2:
				return '#e73939';
			case 3:
				return '#87da61';
			case 4:
				return '#16C2A3';
			case 5:
				return '#E2C609';
			case 6:
				return '#805444';
			case 7:
				return '#c792e8';
			default:
				return '#fff';
		}
	};

	getStyleByProgress = function(progress) {
		switch (progress) {
			case __('In progress'):
				return 'warning';
			case '50%':
				return 'primary';
			case '80%':
				return 'info';
			case '达成':
				return 'success';
			default:
				return 'default';
		}
	};

	getStyleByPercent = function(percent) {
		if (percent < 0.5) {
			return 'warning';
		}
		if (percent < 0.8) {
			return 'primary';
		}
		if (percent < 1) {
			return 'info';
		}
		return 'success';
	};

	emptyTask = {
		name: __('Empty quest'),
		id: 100000,
		content: '...',
		progress: '',
		category: 0,
		type: 0
	};

	lockedTask = {
		name: __('Locked'),
		id: 100001,
		content: __("Increase your active quest limit with a \"Headquarters Personnel\"."),
		progress: '',
		category: 0,
		type: 0
	};

	memberId = -1;

	questGoals = <?php include "quest_goal.json";?>;

	questRecord = {};

	syncQuestRecord = function() {
		questRecord.time = (new Date()).getTime();
		return localStorage.setItem("quest_tracking_" + memberId, JSON.stringify(questRecord));
	};

	clearQuestRecord = function(id) {
		if (questRecord[id] != null) {
			delete questRecord[id];
		}
		return syncQuestRecord();
	};

	activateQuestRecord = function(id, progress) {
		var before, k, ref1, ref2, v;
		if (questRecord[id] != null) {
			questRecord[id].active = true;
		} else {
			questRecord[id] = {
				count: 0,
				required: 0,
				active: true
			};
			ref1 = questGoals[id];
			for (k in ref1) {
				v = ref1[k];
				if (k === 'type') {
					continue;
				}
				questRecord[id][k] = {
					count: v.init,
					required: v.required,
					description: v.description
				};
				questRecord[id].count += v.init;
				questRecord[id].required += v.required;
			}
		}
		if (Object.keys(questGoals[id]).length === 2) {
			progress = (function() {
				switch (progress) {
					case __('Completed'):
						return 1;
					case '80%':
						return 0.8;
					case '50%':
						return 0.5;
					default:
						return 0;
				}
			})();
			ref2 = questGoals[id];
			for (k in ref2) {
				v = ref2[k];
				if (k === 'type') {
					continue;
				}
				before = questRecord[id][k].count;
				questRecord[id][k].count = Math.max(questRecord[id][k].count, Math.ceil(questRecord[id][k].required * progress));
				questRecord[id].count += questRecord[id][k].count - before;
			}
		}
		return syncQuestRecord();
	};

	inactivateQuestRecord = function(id) {
		if (questRecord[id] == null) {
			return;
		}
		questRecord[id].active = false;
		return syncQuestRecord();
	};

	updateQuestRecord = function(e, options, delta) {
		var before, flag, id, q, ref1, ref2, ref3, ref4, ref5, ref6;
		flag = false;
		for (id in questRecord) {
			q = questRecord[id];
			if (!(q.active && (q[e] != null))) {
				continue;
			}
			if ((((ref1 = questGoals[id][e]) != null ? ref1.shipType : void 0) != null) && (ref2 = options.shipType, indexOf.call(questGoals[id][e].shipType, ref2) < 0)) {
				continue;
			}
			if ((((ref3 = questGoals[id][e]) != null ? ref3.mission : void 0) != null) && (ref4 = options.mission, indexOf.call(questGoals[id][e].mission, ref4) < 0)) {
				continue;
			}
			if ((((ref5 = questGoals[id][e]) != null ? ref5.maparea : void 0) != null) && (ref6 = options.maparea, indexOf.call(questGoals[id][e].maparea, ref6) < 0)) {
				continue;
			}
			before = q[e].count;
			q[e].count = Math.min(q[e].required, q[e].count + delta);
			q.count += q[e].count - before;
			flag = true;
		}
		if (flag) {
			syncQuestRecord();
			return true;
		}
		return false;
	};

	getToolTip = function(id) {
		var k, v;
		return React.createElement("div", null, (function() {
			var ref1, results;
			ref1 = questRecord[id];
			results = [];
			for (k in ref1) {
				v = ref1[k];
				if ((v.count != null) && (v.required != null)) {
					results.push(React.createElement("div", {
						"key": k
					}, v.description, " - ", v.count, " \x2F ", v.required));
				} else {
					results.push(void 0);
				}
			}
			return results;
		})());
	};

	firstBattle = false;

	TaskPanel = React.createClass({displayName: "TaskPanel",
		getInitialState: function() {
			return {
				taskLimits: 5,
				tasks: [Object.clone(emptyTask), Object.clone(emptyTask), Object.clone(emptyTask), Object.clone(emptyTask), Object.clone(emptyTask), Object.clone(lockedTask)],
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
			var body, err, error1, event, flag, id, idx, j, l, len, len1, method, path, postBody, progress, q, ref1, ref2, ref3, task, tasks;
			ref1 = e.detail, method = ref1.method, path = ref1.path, body = ref1.body, postBody = ref1.postBody;
			tasks = this.state.tasks;
			flag = false;
			switch (path) {
				case '/kcsapi/api_port/port':
					if (this.state.taskLimits !== body.api_parallel_quest_count) {
						tasks[5] = Object.clone(emptyTask);
						this.setState({
							tasks: tasks,
							taskLimits: body.api_parallel_quest_count
						});
					}
					break;
				case '/kcsapi/api_get_member/basic':
					memberId = window._nickNameId;
					try {
						questRecord = JSON.parse(localStorage.getItem("quest_tracking_" + memberId));
						if ((questRecord != null) && (questRecord.time != null)) {
							if (isDifferentDay((new Date()).getTime(), questRecord.time)) {
								for (id in questRecord) {
									q = questRecord[id];
									if (questGoals[id] == null) {
										continue;
									}
									if ((ref2 = questGoals[id].type) === 2 || ref2 === 4 || ref2 === 5) {
										delete questRecord[id];
									}
								}
							}
							if (isDifferentWeek((new Date()).getTime(), questRecord.time)) {
								for (id in questRecord) {
									q = questRecord[id];
									if (questGoals[id] == null) {
										continue;
									}
									if (questGoals[id].type === 3) {
										delete questRecord[id];
									}
								}
							}
							if (isDifferentMonth((new Date()).getTime(), questRecord.time)) {
								for (id in questRecord) {
									q = questRecord[id];
									if (questGoals[id] == null) {
										continue;
									}
									if (questGoals[id].type === 6) {
										delete questRecord[id];
									}
								}
							}
						} else if (!questRecord) {
							questRecord = {};
						} else {
							console.error("Unexpected questRecord");
							console.log(questRecord);
						}
					} catch (error1) {
						err = error1;
						questRecord = {};
					}
					break;
				case '/kcsapi/api_get_member/questlist':
					if (body.api_list == null) {
						return;
					}
					ref3 = body.api_list;
					for (j = 0, len = ref3.length; j < len; j++) {
						task = ref3[j];
						if (task === -1 || task.api_state < 2) {
							continue;
						}
						progress = __('In progress');
						if (task.api_state === 3) {
							progress = __('Completed');
						} else if (task.api_progress_flag === 1) {
							progress = '50%';
						} else if (task.api_progress_flag === 2) {
							progress = '80%';
						}
						if (questGoals[task.api_no] != null) {
							activateQuestRecord(task.api_no, progress);
						}
						idx = _.findIndex(tasks, function(e) {
							return e.id === task.api_no;
						});
						if (idx === -1) {
							idx = _.findIndex(tasks, function(e) {
								return e.id === 100000;
							});
						}
						tasks[idx] = {
							name: task.api_title,
							id: task.api_no,
							content: task.api_detail,
							progress: progress,
							category: task.api_category,
							type: task.api_type
						};
					}
					flag = true;
					break;
				case '/kcsapi/api_req_quest/clearitemget':
					clearQuestRecord(parseInt(postBody.api_quest_id));
					idx = _.findIndex(tasks, function(e) {
						return e.id === parseInt(postBody.api_quest_id);
					});
					if (idx === -1) {
						return;
					}
					tasks[idx] = Object.clone(emptyTask);
					flag = true;
					break;
				case '/kcsapi/api_req_quest/stop':
					inactivateQuestRecord(parseInt(postBody.api_quest_id));
					idx = _.findIndex(tasks, function(e) {
						return e.id === parseInt(postBody.api_quest_id);
					});
					if (idx === -1) {
						return;
					}
					tasks[idx] = Object.clone(emptyTask);
					flag = true;
					break;
				case '/kcsapi/api_req_practice/battle_result':
					switch (body.api_win_rank) {
						case 'S':
						case 'A':
						case 'B':
							flag = updateQuestRecord('practice_win', null, 1) || flag;
							flag = updateQuestRecord('practice', null, 1) || flag;
							break;
						default:
							flag = updateQuestRecord('practice', null, 1) || flag;
					}
					break;
				case '/kcsapi/api_req_mission/result':
					if (body.api_clear_result > 0) {
						flag = updateQuestRecord('mission_success', {
							mission: body.api_quest_name
						}, 1);
					}
					break;
				case '/kcsapi/api_req_nyukyo/start':
					flag = updateQuestRecord('repair', null, 1);
					break;
				case '/kcsapi/api_req_hokyu/charge':
					flag = updateQuestRecord('supply', null, 1);
					break;
				case '/kcsapi/api_req_kousyou/createitem':
					flag = updateQuestRecord('create_item', null, 1);
					break;
				case '/kcsapi/api_req_kousyou/createship':
					flag = updateQuestRecord('create_ship', null, 1);
					break;
				case '/kcsapi/api_req_kousyou/destroyship':
					flag = updateQuestRecord('destroy_ship', null, 1);
					break;
				case '/kcsapi/api_req_kousyou/remodel_slot':
					flag = updateQuestRecord('remodel_item', null, 1);
					break;
				case '/kcsapi/api_req_kaisou/powerup':
					if (body.api_powerup_flag === 1) {
						flag = updateQuestRecord('remodel_ship', null, 1);
					}
					break;
				case '/kcsapi/api_req_kousyou/destroyitem2':
					flag = updateQuestRecord('destory_item', null, 1);
					break;
				case '/kcsapi/api_req_map/start':
					firstBattle = true;
					break;
				case '/kcsapi/api_req_sortie/battleresult':
				case '/kcsapi/api_req_combined_battle/battleresult':
					if (firstBattle) {
						flag = updateQuestRecord('sally', null, 1);
						firstBattle = false;
					}
			}
			if (!flag) {
				return;
			}
			for (l = 0, len1 = tasks.length; l < len1; l++) {
				task = tasks[l];
				if (task.id >= 100000) {
					continue;
				}
				if (questGoals[task.id] != null) {
					task.tracking = true;
					task.percent = questRecord[task.id].count / questRecord[task.id].required;
					task.progress = questRecord[task.id].count + ' / ' + questRecord[task.id].required;
				}
			}
			tasks = _.sortBy(tasks, function(e) {
				return e.id;
			});
			this.setState({
				tasks: tasks
			});
			event = new CustomEvent('task.change', {
				bubbles: true,
				cancelable: true,
				detail: {
					tasks: tasks
				}
			});
			return window.dispatchEvent(event);
		},
		handleBattleResult: function(e) {
			var boss, enemyHp, enemyShipId, flag, idx, j, l, len, len1, map, rank, ref1, shipId, shipType, task, tasks;
			flag = false;
			ref1 = e.detail, rank = ref1.rank, boss = ref1.boss, map = ref1.map, enemyHp = ref1.enemyHp, enemyShipId = ref1.enemyShipId;
			flag = updateQuestRecord('battle', null, 1) || flag;
			if (rank === 'S' || rank === 'A' || rank === 'B') {
				flag = updateQuestRecord('battle_win', null, 1) || flag;
			}
			if (rank === 'S') {
				flag = updateQuestRecord('battle_rank_s', null, 1) || flag;
			}
			if (boss) {
				flag = updateQuestRecord('battle_boss', null, 1) || flag;
				if (rank === 'S' || rank === 'A' || rank === 'B') {
					flag = updateQuestRecord('battle_boss_win', {
						maparea: map
					}, 1) || flag;
				}
				if (rank === 'S' || rank === 'A') {
					flag = updateQuestRecord('battle_boss_win_rank_a', {
						maparea: map
					}, 1) || flag;
				}
				if (rank === 'S') {
					flag = updateQuestRecord('battle_boss_win_rank_s', {
						maparea: map
					}, 1) || flag;
				}
			}
			for (idx = j = 0, len = enemyShipId.length; j < len; idx = ++j) {
				shipId = enemyShipId[idx];
				if (shipId === -1 || enemyHp[idx] > 0) {
					continue;
				}
				shipType = window.$ships[shipId].api_stype;
				if (shipType === 7 || shipType === 11 || shipType === 13 || shipType === 15) {
					flag = updateQuestRecord('sinking', {
						shipType: shipType
					}, 1) || flag;
				}
			}
			if (flag) {
				tasks = this.state.tasks;
				for (l = 0, len1 = tasks.length; l < len1; l++) {
					task = tasks[l];
					if (task.id >= 100000) {
						continue;
					}
					if (questGoals[task.id] != null) {
						task.tracking = true;
						task.percent = questRecord[task.id].count / questRecord[task.id].required;
						task.progress = questRecord[task.id].count + ' / ' + questRecord[task.id].required;
					}
				}
				tasks = _.sortBy(tasks, function(e) {
					return e.id;
				});
				return this.setState({
					tasks: tasks
				});
			}
		},
		refreshDay: function() {
			var event, id, idx, j, len, q, ref1, ref2, task, tasks;
			if (!isDifferentDay((new Date()).getTime(), prevTime)) {
				return;
			}
			tasks = this.state.tasks;
			for (idx = j = 0, len = tasks.length; j < len; idx = ++j) {
				task = tasks[idx];
				if (task.id >= 100000) {
					continue;
				}
				if ((ref1 = task.type) === 2 || ref1 === 4 || ref1 === 5) {
					clearQuestRecord(task.id);
					tasks[idx] = Object.clone(emptyTask);
				}
				if (task.type === 3 && isDifferentWeek((new Date()).getTime(), prevTime)) {
					clearQuestRecord(task.id);
					tasks[idx] = Object.clone(emptyTask);
				}
				if (task.type === 6 && isDifferentMonth((new Date()).getTime(), prevTime)) {
					clearQuestRecord(task.id);
					tasks[idx] = Object.clone(emptyTask);
				}
			}
			for (id in questRecord) {
				q = questRecord[id];
				if (questGoals[id] == null) {
					continue;
				}
				if ((ref2 = questGoals[id].type) === 2 || ref2 === 4 || ref2 === 5) {
					clearQuestRecord(id);
				}
				if (questGoals[id].type === 3 && isDifferentWeek((new Date()).getTime(), prevTime)) {
					clearQuestRecord(id);
				}
				if (questGoals[id].type === 6 && isDifferentMonth((new Date()).getTime(), prevTime)) {
					clearQuestRecord(id);
				}
			}
			tasks = _.sortBy(tasks, function(e) {
				return e.id;
			});
			this.setState({
				tasks: tasks
			});
			event = new CustomEvent('task.change', {
				bubbles: true,
				cancelable: true,
				detail: {
					tasks: tasks
				}
			});
			window.dispatchEvent(event);
			return prevTime = (new Date()).getTime();
		},
		handleTaskInfo: function(e) {
			var tasks;
			tasks = e.detail.tasks;
			return this.setState({
				tasks: tasks
			});
		},
		componentDidMount: function() {
			window.addEventListener('game.response', this.handleResponse);
			window.addEventListener('task.info', this.handleTaskInfo);
			window.addEventListener('battle.result', this.handleBattleResult);
			window.addEventListener('view.main.visible', this.handleVisibleResponse);
			return this.interval = setInterval(this.refreshDay, 30000);
		},
		componentWillUnmount: function() {
			window.removeEventListener('game.response', this.handleResponse);
			window.removeEventListener('task.info', this.handleTaskInfo);
			window.removeEventListener('battle.result', this.handleBattleResult);
			window.removeEventListener('view.main.visible', this.handleVisibleResponse);
			return clearInterval(this.interval);
		},
		render: function() {
			var i;
			return React.createElement(Panel, {
				"bsStyle": "default"
			}, (function() {
				var j, results;
				results = [];
				for (i = j = 0; j <= 5; i = ++j) {
					if (this.state.tasks[i].tracking) {
						results.push(React.createElement("div", {
							"className": "panel-item task-item",
							"key": i
						}, React.createElement(OverlayTrigger, {
							"placement": ((!window.doubleTabbed) && (window.layout === 'vertical') ? 'top' : 'left'),
							"overlay": React.createElement(Tooltip, {
								"id": "task-quest-name-" + i
							}, React.createElement("strong", null, this.state.tasks[i].name), React.createElement("br", null), this.state.tasks[i].content)
						}, React.createElement("div", {
							"className": "quest-name"
						}, React.createElement("span", {
							"className": "cat-indicator",
							"style": {
								backgroundColor: getCategory(this.state.tasks[i].category)
							}
						}), this.state.tasks[i].name)), React.createElement("div", null, React.createElement(OverlayTrigger, {
							"placement": 'left',
							"overlay": React.createElement(Tooltip, {
								"id": "task-progress-" + i
							}, getToolTip(this.state.tasks[i].id))
						}, React.createElement(Label, {
							"className": "quest-progress",
							"bsStyle": getStyleByPercent(this.state.tasks[i].percent)
						}, this.state.tasks[i].progress)))));
					} else {
						results.push(React.createElement("div", {
							"className": "panel-item task-item",
							"key": i
						}, React.createElement(OverlayTrigger, {
							"placement": ((!window.doubleTabbed) && (window.layout === 'vertical') ? 'top' : 'left'),
							"overlay": React.createElement(Tooltip, {
								"id": "task-name-" + i
							}, React.createElement("strong", null, this.state.tasks[i].name), React.createElement("br", null), this.state.tasks[i].content)
						}, React.createElement("div", {
							"className": "quest-name"
						}, React.createElement("span", {
							"className": "cat-indicator",
							"style": {
								backgroundColor: getCategory(this.state.tasks[i].category)
							}
						}), this.state.tasks[i].name)), React.createElement("div", null, React.createElement(Label, {
							"className": "quest-progress",
							"bsStyle": getStyleByProgress(this.state.tasks[i].progress)
						}, this.state.tasks[i].progress))));
					}
				}
				return results;
			}).call(this));
		}
	});

	return TaskPanel;

})();
