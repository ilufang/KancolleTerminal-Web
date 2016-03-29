<?php
header("Content-Type: application/json; charset=utf-8");
require_once 'agectrl.php';
tryModified("quest.json");
?>

Object.assign(i18nDB, <?php
	echo file_get_contents("i18n_questinfo.json");
?>);

var $, $$, Col, FontAwesome, Grid, Input, OverlayTrigger, Panel, React, ReactBootstrap, Row, Tooltip, _, categoryNames, filterNames, join, layout, typeFreqs, typeNames;


var React = window.React, ReactBootstrap = window.ReactBootstrap, FontAwesome = window.FontAwesome, layout = window.layout;

var Grid = ReactBootstrap.Grid, Row = ReactBootstrap.Row, Col = ReactBootstrap.Col, Input = ReactBootstrap.Input, Panel = ReactBootstrap.Panel, OverlayTrigger = ReactBootstrap.OverlayTrigger, Tooltip = ReactBootstrap.Tooltip;

var filterNames = ["空", "编成任务", "出击任务", "演习任务", "远征任务", "补给/入渠任务", "工厂任务", "改装任务", "结婚任务", "日常任务", "周常任务", "月常任务"];

var categoryNames = ["", "编成", "出击", "演习", "远征", "补给/入渠", "工厂", "改装"];

var typeNames = ["", "单次任务", "每日任务", "每周任务", "3/7/0日任务", "2/8日任务", "每月任务"];

var typeFreqs = [0, 1, 5, 3, 4, 4, 2];

<?php
// include 'queststr.js'; // Go Die reqstr! Stupid deoptimization with no performance!
?>

QuestInfo = {
	reactClass: React.createClass({displayName: "reactClass",
		getInitialState: function() {
			var fs, i, j, json, k, l, len, len1, len2, len3, pid, prereq, quest, quests, quests_status, ref;
			json = <?php
				echo file_get_contents("quest.json"); // Do not update to the sketchy "dynamic" version
			?>;
			quests = [];
			for (i = 0, len = json.length; i < len; i++) {
				quest = json[i];
				// quest.condition = reqstr(quest['requirements']); // Go Die reqstr!!!
				quests[quest.game_id] = quest;
			}
			quests_status = [];
			for (j = 0, len1 = json.length; j < len1; j++) {
				quest = json[j];
				quest.postquest = [];
				quests_status[quest.game_id] = 1;
			}
			for (k = 0, len2 = json.length; k < len2; k++) {
				quest = json[k];
				ref = quest.prerequisite;
				for (l = 0, len3 = ref.length; l < len3; l++) {
					pid = ref[l];
					prereq = quests[pid];
					prereq.postquest.push(quest.game_id);
				}
			}
			return {
				quests: quests,
				quests_status: quests_status,
				quest_filter: 0,
				quest_id: 0,
				quests_filtered: [],
				quest_selected: null
			};
		},
		handleTaskChange: function(e) {
			var event, i, len, quest, task, tasks;
			tasks = e.detail.tasks;
			for (i = 0, len = tasks.length; i < len; i++) {
				task = tasks[i];
				if (task.id < 100000) {
					if (this.state.quests[task.id] != null) {
						quest = this.state.quests[task.id];
						task.content = React.createElement("div", null, categoryNames[quest.category], " - ", typeNames[quest.type], React.createElement("br", null), quest.condition);
					} else if (typeof task.content !== 'object') {
						task.content = React.createElement("div", null, "任务ID: ", task.id, React.createElement("br", null), task.content);
					}
				}
			}
			event = new CustomEvent('task.info', {
				bubbles: true,
				cancelable: true,
				detail: {
					tasks: tasks
				}
			});
			return window.dispatchEvent(event);
		},
		handleFilterChange: function(fid) {
			var quest, quest_filter, quests_filtered;
			quest_filter = fid;
			quests_filtered = (function() {
				var i, j, k, l, len, len1, len2, len3, len4, m, ref, ref1, ref2, ref3, ref4, ref5, results, results1, results2, results3, results4;
				switch (quest_filter) {
					case 0:
						return [];
					case 1:
					case 2:
					case 3:
					case 4:
					case 5:
					case 6:
					case 7:
						ref = this.state.quests;
						results = [];
						for (i = 0, len = ref.length; i < len; i++) {
							quest = ref[i];
							if ((quest != null) && quest.category === quest_filter && quest.wiki_id.charAt(0) !== "W") {
								results.push(quest);
							}
						}
						return results;
					case 8:
						ref1 = this.state.quests;
						results1 = [];
						for (j = 0, len1 = ref1.length; j < len1; j++) {
							quest = ref1[j];
							if ((quest != null) && quest.wiki_id.charAt(0) === "W") {
								results1.push(quest);
							}
						}
						return results1;
					case 9:
						ref2 = this.state.quests;
						results2 = [];
						for (k = 0, len2 = ref2.length; k < len2; k++) {
							quest = ref2[k];
							if ((quest != null) && ((ref3 = quest.type) === 2 || ref3 === 4 || ref3 === 5)) {
								results2.push(quest);
							}
						}
						return results2;
					case 10:
						ref4 = this.state.quests;
						results3 = [];
						for (l = 0, len3 = ref4.length; l < len3; l++) {
							quest = ref4[l];
							if ((quest != null) && quest.type === 3) {
								results3.push(quest);
							}
						}
						return results3;
					case 11:
						ref5 = this.state.quests;
						results4 = [];
						for (m = 0, len4 = ref5.length; m < len4; m++) {
							quest = ref5[m];
							if ((quest != null) && quest.type === 6) {
								results4.push(quest);
							}
						}
						return results4;
				}
			}).call(this);
			quests_filtered.sort(function(a, b) {
				return a.wiki_id-b.wiki_id;
			});
			return this.setState({
				quest_filter: quest_filter,
				quests_filtered: quests_filtered
			});
		},
		handleQuestChange: function(qid) {
			var quest_id, quest_selected;
			quest_id = qid;
			quest_selected = this.state.quests[quest_id];
			return this.setState({
				quest_id: quest_id,
				quest_selected: quest_selected
			});
		},
		handleFilterSelect: function(e) {
			var quest_filter;
			quest_filter = parseInt(e.target.value);
			this.handleFilterChange(quest_filter);
			return this.setState({
				quest_id: 0,
				quest_selected: null
			});
		},
		handleQuestSelect: function(e) {
			var quest_id;
			quest_id = parseInt(e.target.value);
			return this.handleQuestChange(quest_id);
		},
		handlePrereqClick: function(qid) {
			var quest, quest_filter, quest_id;
			quest = this.state.quests[qid];
			quest_filter = (function() {
				var ref;
				switch (false) {
					case (ref = quest.type) !== 2 && ref !== 4 && ref !== 5:
						return 9;
					case quest.type !== 3:
						return 10;
					case quest.type !== 6:
						return 11;
					case quest.wiki_id.charAt(0) !== "W":
						return 8;
					default:
						return quest.category;
				}
			})();
			quest_id = qid;
			this.handleFilterChange(quest_filter);
			return this.handleQuestChange(quest_id);
		},
		updateQuestStatus: function(qid, status) {
			var i, len, pid, postq, quest, ref, results;
			quest = this.state.quests[qid];
			if (quest == null) {
				return;
			}
			ref = quest.postquest;
			results = [];
			for (i = 0, len = ref.length; i < len; i++) {
				pid = ref[i];
				postq = this.state.quests[pid];
				if (typeFreqs[quest.type] <= typeFreqs[postq.type] && status[postq.game_id] !== 3) {
					status[postq.game_id] = 3;
					results.push(this.updateQuestStatus(postq.game_id, status));
				} else {
					results.push(void 0);
				}
			}
			return results;
		},
		handleResponse: function(e) {
			var body, i, j, len, len1, method, path, postBody, postq, qid, quest, quests_status, ref, ref1, ref2;
			ref = e.detail, method = ref.method, path = ref.path, body = ref.body, postBody = ref.postBody;
			quests_status = this.state.quests_status;
			switch (path) {
				case "/kcsapi/api_get_member/questlist":
					if (body.api_list != null) {
						ref1 = body.api_list;
						for (i = 0, len = ref1.length; i < len; i++) {
							quest = ref1[i];
							if (quest === -1) {
								continue;
							}
							if (quests_status[quest.api_no] !== 2) {
								quests_status[quest.api_no] = 2;
								this.updateQuestStatus(quest.api_no, quests_status);
							}
						}
					}
					break;
				case "/kcsapi/api_req_quest/clearitemget":
					qid = parseInt(postBody.api_quest_id);
					quests_status[qid] = 1;
					ref2 = this.state.quests[qid].postquest;
					for (j = 0, len1 = ref2.length; j < len1; j++) {
						postq = ref2[j];
						if (quests_status[postq] === 3) {
							quests_status[postq] = 2;
						}
					}
			}
			return this.setState({
				quests_status: quests_status
			});
		},
		componentDidMount: function() {
			window.addEventListener("task.change", this.handleTaskChange);
			return window.addEventListener("game.response", this.handleResponse);
		},
		render: function() {
			var filter, idx, qid, quest;
			return React.createElement("div", null, React.createElement("link", {
				"rel": 'stylesheet',
				"href": "assets/css/quest.css"
			}), React.createElement(Grid, null, React.createElement(Row, null, React.createElement(Col, {
				"xs": 12
			}, React.createElement(Panel, {
				"header": '任务选择',
				"bsStyle": 'primary'
			}, React.createElement(Input, {
				"type": 'select',
				"label": '任务种类',
				"value": this.state.quest_filter,
				"onChange": this.handleFilterSelect
			}, (function() {
				var i, len, results;
				results = [];
				for (idx = i = 0, len = filterNames.length; i < len; idx = ++i) {
					filter = filterNames[idx];
					results.push(React.createElement("option", {
						"key": idx,
						"value": idx
					}, filter));
				}
				return results;
			})()), React.createElement(Input, {
				"type": 'select',
				"label": '任务名称',
				"value": this.state.quest_id,
				"onChange": this.handleQuestSelect
			}, React.createElement("option", {
				"key": 0.
			}, "空"), React.createElement("optgroup", {
				"label": '可执行'
			}, (function() {
				var i, len, ref, results;
				ref = this.state.quests_filtered;
				results = [];
				for (i = 0, len = ref.length; i < len; i++) {
					quest = ref[i];
					if (this.state.quests_status[quest.game_id] === 2) {
						results.push(React.createElement("option", {
							"key": quest.game_id,
							"value": quest.game_id
						}, quest.wiki_id, " - ", quest.name));
					}
				}
				return results;
			}).call(this)), React.createElement("optgroup", {
				"label": '未开放'
			}, (function() {
				var i, len, ref, results;
				ref = this.state.quests_filtered;
				results = [];
				for (i = 0, len = ref.length; i < len; i++) {
					quest = ref[i];
					if (this.state.quests_status[quest.game_id] === 3) {
						results.push(React.createElement("option", {
							"key": quest.game_id,
							"value": quest.game_id
						}, quest.wiki_id, " - ", quest.name));
					}
				}
				return results;
			}).call(this)), React.createElement("optgroup", {
				"label": '已完成'
			}, (function() {
				var i, len, ref, results;
				ref = this.state.quests_filtered;
				results = [];
				for (i = 0, len = ref.length; i < len; i++) {
					quest = ref[i];
					if (this.state.quests_status[quest.game_id] === 1) {
						results.push(React.createElement("option", {
							"key": quest.game_id,
							"value": quest.game_id
						}, quest.wiki_id, " - ", quest.name));
					}
				}
				return results;
			}).call(this)))))), React.createElement(Row, null, React.createElement(Col, {
				"xs": 12
			}, React.createElement(Panel, {
				"header": '任务详情',
				"bsStyle": 'danger'
			}, (this.state.quest_selected != null ? React.createElement("div", null, React.createElement("div", {
				"className": 'questTitle'
			}, this.state.quest_selected.name), React.createElement("div", {
				"className": 'questType'
			}, categoryNames[this.state.quest_selected.category], " - ", typeNames[this.state.quest_selected.type])) : React.createElement("div", null, React.createElement("div", {
				"className": 'questTitle'
			}, "请选择任务"), React.createElement("div", {
				"className": 'questType'
			}, "未知类型"))), React.createElement(Row, null, React.createElement("div", {
				"className": 'questInfo'
			}, React.createElement(Panel, {
				"header": '任务奖励',
				"bsStyle": 'info'
			}, (this.state.quest_selected != null ? React.createElement("ul", null, React.createElement("li", {
				"key": 'reward_fuel'
			}, React.createElement('span',null,'获得燃料 '+this.state.quest_selected.reward_fuel)), React.createElement("li", {
				"key": 'reward_bullet'
			}, React.createElement('span',null,'获得弹药 '+this.state.quest_selected.reward_bullet)), React.createElement("li", {
				"key": 'reward_steel'
			}, React.createElement('span',null,'获得钢材 '+this.state.quest_selected.reward_steel)), React.createElement("li", {
				"key": 'reward_alum'
			}, React.createElement('span',null,'获得铝土 '+this.state.quest_selected.reward_alum)), React.createElement("li", {
				"key": 'reward_other'
			}, this.state.quest_selected.reward_other)) : void 0)), React.createElement(Panel, {
				"header": '必要条件',
				"bsStyle": 'success'
			}, (this.state.quest_selected != null ? React.createElement("div", null, React.createElement("div", null, "完成条件:"), React.createElement("div", {
				"className": 'reqDetail'
			}, React.createElement(OverlayTrigger, {
				"placement": 'left',
				"overlay": React.createElement(Tooltip, null, this.state.quest_selected.detail)
			}, React.createElement("div", {
				"className": 'tooltipTrigger'
			}, this.state.quest_selected.condition))), (this.state.quest_selected.prerequisite.length > 0 ? React.createElement("div", null, "前置任务:") : void 0), ((function() {
				var i, len, ref, results;
				if (this.state.quest_selected.prerequisite.length > 0) {
					ref = this.state.quest_selected.prerequisite;
					results = [];
					for (i = 0, len = ref.length; i < len; i++) {
						qid = ref[i];
						results.push(React.createElement("div", {
							"className": 'prereqName'
						}, React.createElement(OverlayTrigger, {
							"placement": 'left',
							"overlay": React.createElement(Tooltip, null, React.createElement("strong", null, this.state.quests[qid].name), React.createElement("br", null), categoryNames[this.state.quests[qid].category], " - ", typeNames[this.state.quests[qid].type], React.createElement("br", null), this.state.quests[qid].condition)
						}, React.createElement("div", {
							"className": 'tooltipTrigger'
						}, React.createElement("a", {
							"onClick": this.handlePrereqClick.bind(this, qid)
						}, this.state.quests[qid].wiki_id, " - ", this.state.quests[qid].name)))));
					}
					return results;
				}
			}).call(this)), (this.state.quest_selected.postquest.length > 0 ? React.createElement("div", null, "后续任务:") : void 0), ((function() {
				var i, len, ref, results;
				if (this.state.quest_selected.postquest.length > 0) {
					ref = this.state.quest_selected.postquest;
					results = [];
					for (i = 0, len = ref.length; i < len; i++) {
						qid = ref[i];
						results.push(React.createElement("div", {
							"className": 'prereqName'
						}, React.createElement(OverlayTrigger, {
							"placement": 'left',
							"overlay": React.createElement(Tooltip, null, React.createElement("strong", null, this.state.quests[qid].name), React.createElement("br", null), categoryNames[this.state.quests[qid].category], " - ", typeNames[this.state.quests[qid].type], React.createElement("br", null), this.state.quests[qid].condition)
						}, React.createElement("div", {
							"className": 'tooltipTrigger'
						}, " ", React.createElement("a", {
							"onClick": this.handlePrereqClick.bind(this, qid)
						}, this.state.quests[qid].wiki_id, " - ", this.state.quests[qid].name)))));
					}
					return results;
				}
			}).call(this))) : void 0)))))))));
		}
	})
};

