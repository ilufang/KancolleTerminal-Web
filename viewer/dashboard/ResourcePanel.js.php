(function() {
	var $, $$, Col, Grid, MaterialIcon, Panel, ROOT, React, ReactBootstrap, ResourcePanel, _, __, __n, error, layout, log, order, path, ref, toggleModal, warn;

	ROOT = window.ROOT, layout = window.layout, _ = window._, $ = window.$, $$ = window.$$, React = window.React, ReactBootstrap = window.ReactBootstrap, toggleModal = window.toggleModal;

	log = window.log, warn = window.warn, error = window.error;

	Panel = ReactBootstrap.Panel, Grid = ReactBootstrap.Grid, Col = ReactBootstrap.Col;

	order = [1, 3, 2, 4, 5, 7, 6, 8];

	MaterialIcon = CommonIcon.MaterialIcon;

	ResourcePanel = React.createClass({displayName: "ResourcePanel",
		getInitialState: function() {
			return {
				material: ['??', '??', '??', '??', '??', '??', '??', '??', '??'],
				limit: 30750,
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
			var body, i, j, k, l, len, len1, level, limit, m, material, method, n, o, p, ref1, ref2, ref3;
			ref1 = e.detail, method = ref1.method, path = ref1.path, body = ref1.body;
			switch (path) {
				case '/kcsapi/api_get_member/material':
					material = this.state.material;
					for (j = 0, len = body.length; j < len; j++) {
						e = body[j];
						material[e.api_id] = e.api_value;
					}
					return this.setState({
						material: material
					});
				case '/kcsapi/api_port/port':
					ref2 = this.state, material = ref2.material, limit = ref2.limit;
					ref3 = body.api_material;
					for (k = 0, len1 = ref3.length; k < len1; k++) {
						e = ref3[k];
						material[e.api_id] = e.api_value;
					}
					level = parseInt(body.api_basic.api_level);
					if (level > 0) {
						limit = 750 + level * 250;
					}
					return this.setState({
						material: material,
						limit: limit
					});
				case '/kcsapi/api_req_hokyu/charge':
					material = this.state.material;
					for (i = l = 0; l <= 3; i = ++l) {
						material[i + 1] = body.api_material[i];
					}
					return this.setState({
						material: material
					});
				case '/kcsapi/api_req_kousyou/createitem':
					material = this.state.material;
					for (i = m = 0; m <= 7; i = ++m) {
						material[i + 1] = body.api_material[i];
					}
					return this.setState({
						material: material
					});
				case '/kcsapi/api_req_kousyou/createship_speedchange':
					material = this.state.material;
					if (body.api_result === 1) {
						material[4] -= 1;
					}
					return this.setState({
						material: material
					});
				case '/kcsapi/api_req_kousyou/destroyitem2':
					material = this.state.material;
					for (i = n = 0; n <= 3; i = ++n) {
						material[i + 1] += body.api_get_material[i];
					}
					return this.setState({
						material: material
					});
				case '/kcsapi/api_req_kousyou/destroyship':
					material = this.state.material;
					for (i = o = 0; o <= 3; i = ++o) {
						material[i + 1] = body.api_material[i];
					}
					return this.setState({
						material: material
					});
				case '/kcsapi/api_req_kousyou/remodel_slot':
					material = this.state.material;
					for (i = p = 0; p <= 7; i = ++p) {
						material[i + 1] = body.api_after_material[i];
					}
					return this.setState({
						material: material,
						slotitemCount: Object.keys(window._slotitems).length
					});
				case '/kcsapi/api_req_nyukyo/speedchange':
					material = this.state.material;
					if (body.api_result === 1) {
						material[5] -= 1;
					}
					return this.setState({
						material: material
					});
				case '/kcsapi/api_req_nyukyo/start':
					material = this.state.material;
					if (body.api_highspeed === 1) {
						material[5] -= 1;
					}
					return this.setState({
						material: material
					});
			}
		},
		componentDidMount: function() {
			window.addEventListener('game.response', this.handleResponse);
			return window.addEventListener('view.main.visible', this.handleVisibleResponse);
		},
		componentWillUnmount: function() {
			window.removeEventListener('game.response', this.handleResponse);
			return window.removeEventListener('view.main.visible', this.handleVisibleResponse);
		},
		render: function() {
			var i;
			return React.createElement(Panel, {
				"bsStyle": "default"
			}, React.createElement(Grid, null, (function() {
				var j, len, results;
				results = [];
				for (j = 0, len = order.length; j < len; j++) {
					i = order[j];
					results.push(React.createElement(Col, {
						"key": i,
						"xs": 6.,
						"style": {
							marginBottom: 3
						}
					}, React.createElement(MaterialIcon, {
						"materialId": i,
						"className": "material-icon " + (i <= 4 && this.state.material[i] < this.state.limit ? 'grow' : '')
					}), React.createElement("span", {
						"className": "material-value"
					}, this.state.material[i])));
				}
				return results;
			}).call(this)));
		}
	});

	return ResourcePanel;

})();
